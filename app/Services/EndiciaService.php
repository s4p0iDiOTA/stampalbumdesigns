<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * EndiciaService
 *
 * Integrates with Endicia Label Server API for real-time shipping rate calculations.
 * Documentation: https://www.endicia.com/developer/docs/els.html
 */
class EndiciaService
{
    private string $apiUrl;
    private string $accountId;
    private string $passPhrase;
    private bool $testMode;

    public function __construct()
    {
        $this->apiUrl = config('services.endicia.api_url', 'https://elstestserver.endicia.com/LabelService/EwsLabelService.asmx');
        $this->accountId = config('services.endicia.account_id');
        $this->passPhrase = config('services.endicia.pass_phrase');
        $this->testMode = config('services.endicia.test_mode', true);
    }

    /**
     * Get shipping rates for an order
     *
     * @param array $shippingAddress Address information
     * @param array $cart Cart items
     * @return array Array of shipping options with rates
     */
    public function getRates(array $shippingAddress, array $cart): array
    {
        $calculator = new ShippingCalculator();
        $breakdown = $calculator->getShippingBreakdown($cart);

        $weight = $breakdown['total_weight'];
        $dimensions = $breakdown['dimensions'];

        // Build list of mail classes to quote
        $mailClasses = [
            'Priority' => 'Priority Mail',
            'PriorityExpress' => 'Priority Mail Express',
            'First' => 'First-Class Mail',
            'MediaMail' => 'Media Mail',
        ];

        $rates = [];

        foreach ($mailClasses as $mailClass => $displayName) {
            try {
                $rate = $this->getPostageRate(
                    $shippingAddress,
                    $weight,
                    $dimensions,
                    $mailClass
                );

                if ($rate !== null) {
                    $rates[] = [
                        'service_code' => strtolower(str_replace(' ', '_', $mailClass)),
                        'service_name' => $displayName,
                        'cost' => $rate['amount'],
                        'currency' => 'USD',
                        'delivery_days' => $rate['delivery_days'] ?? null,
                        'provider' => 'USPS',
                        'package_type' => $dimensions['type'],
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Failed to get rate for {$mailClass}: " . $e->getMessage());
            }
        }

        // Sort by price
        usort($rates, fn($a, $b) => $a['cost'] <=> $b['cost']);

        return $rates;
    }

    /**
     * Get postage rate for a specific mail class
     *
     * @param array $toAddress Destination address
     * @param array $weight Weight information
     * @param array $dimensions Package dimensions
     * @param string $mailClass Mail class (Priority, PriorityExpress, etc.)
     * @return array|null Rate information or null if unavailable
     */
    private function getPostageRate(
        array $toAddress,
        array $weight,
        array $dimensions,
        string $mailClass
    ): ?array {
        $xml = $this->buildPostageRateXml(
            $toAddress,
            $weight,
            $dimensions,
            $mailClass
        );

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/xml',
                'SOAPAction' => 'http://www.endicia.com/CalculatePostageRate',
            ])
            ->withBody($xml, 'application/xml')
            ->post($this->apiUrl);

            if ($response->successful()) {
                return $this->parsePostageRateResponse($response->body(), $mailClass);
            }

            Log::error('Endicia API error: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Endicia request failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Build XML request for PostageRateRequest
     *
     * @param array $toAddress
     * @param array $weight
     * @param array $dimensions
     * @param string $mailClass
     * @return string XML request
     */
    private function buildPostageRateXml(
        array $toAddress,
        array $weight,
        array $dimensions,
        string $mailClass
    ): string {
        $fromZip = config('services.endicia.from_zip', '90210');
        $toZip = $toAddress['zip'] ?? $toAddress['postcode'] ?? '';

        // Convert weight to proper format (pounds and ounces)
        $weightOz = $weight['weight_oz'];
        $weightLbs = floor($weightOz / 16);
        $weightRemainingOz = $weightOz - ($weightLbs * 16);

        $xml = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
               xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
               xmlns:xsd="http://www.w3.org/2001/XMLSchema">
    <soap:Body>
        <CalculatePostageRateRequest xmlns="http://www.endicia.com/">
            <CalculatePostageRateRequest>
                <RequesterID>{$this->accountId}</RequesterID>
                <CertifiedIntermediary>
                    <AccountID>{$this->accountId}</AccountID>
                    <PassPhrase>{$this->passPhrase}</PassPhrase>
                </CertifiedIntermediary>
                <MailClass>{$mailClass}</MailClass>
                <DateAdvance>0</DateAdvance>
                <WeightOz>{$weightRemainingOz}</WeightOz>
                <WeightLbs>{$weightLbs}</WeightLbs>
                <MailpieceShape>{$dimensions['endicia_type']}</MailpieceShape>
                <AutomationRate>FALSE</AutomationRate>
                <Machinable>TRUE</Machinable>
                <Services>
                    <DeliveryConfirmation>OFF</DeliveryConfirmation>
                </Services>
                <FromPostalCode>{$fromZip}</FromPostalCode>
                <ToPostalCode>{$toZip}</ToPostalCode>
                <ToCountryCode>US</ToCountryCode>
XML;

        // Add dimensions if it's a package
        if ($dimensions['type'] === 'box') {
            $xml .= <<<XML
                <Length>{$dimensions['length']}</Length>
                <Width>{$dimensions['width']}</Width>
                <Height>{$dimensions['height']}</Height>
XML;
        }

        $xml .= <<<XML
            </CalculatePostageRateRequest>
        </CalculatePostageRateRequest>
    </soap:Body>
</soap:Envelope>
XML;

        return $xml;
    }

    /**
     * Parse PostageRate response XML
     *
     * @param string $xmlResponse
     * @param string $mailClass
     * @return array|null
     */
    private function parsePostageRateResponse(string $xmlResponse, string $mailClass): ?array
    {
        try {
            // Remove namespaces for easier parsing
            $xmlResponse = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $xmlResponse);

            $xml = simplexml_load_string($xmlResponse);

            if ($xml === false) {
                Log::error('Failed to parse Endicia XML response');
                return null;
            }

            // Navigate through SOAP response structure
            $body = $xml->children('soap', true)->Body;
            $response = $body->children()->CalculatePostageRateResponse;

            if (!isset($response->CalculatePostageRateResponse)) {
                return null;
            }

            $result = $response->CalculatePostageRateResponse;

            // Check for errors
            if (isset($result->Status) && $result->Status != 0) {
                Log::warning("Endicia error for {$mailClass}: " . ($result->ErrorMessage ?? 'Unknown error'));
                return null;
            }

            // Extract postage amount
            $postage = (float) ($result->Postage->TotalAmount ?? 0);

            if ($postage <= 0) {
                return null;
            }

            return [
                'amount' => $postage,
                'delivery_days' => $this->estimateDeliveryDays($mailClass),
                'zone' => (string) ($result->Zone ?? ''),
            ];

        } catch (\Exception $e) {
            Log::error('Failed to parse Endicia response: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Estimate delivery days for mail class
     *
     * @param string $mailClass
     * @return string
     */
    private function estimateDeliveryDays(string $mailClass): string
    {
        return match($mailClass) {
            'PriorityExpress' => '1-2 business days',
            'Priority' => '2-3 business days',
            'First' => '2-5 business days',
            'MediaMail' => '2-8 business days',
            default => 'Varies',
        };
    }

    /**
     * Get available mail classes
     *
     * @return array
     */
    public function getAvailableMailClasses(): array
    {
        return [
            'Priority' => [
                'name' => 'USPS Priority Mail',
                'description' => 'Fast delivery with tracking',
                'typical_days' => '2-3',
            ],
            'PriorityExpress' => [
                'name' => 'USPS Priority Mail Express',
                'description' => 'Overnight to 2-day delivery',
                'typical_days' => '1-2',
            ],
            'First' => [
                'name' => 'USPS First-Class Mail',
                'description' => 'Affordable option for lighter packages',
                'typical_days' => '2-5',
            ],
            'MediaMail' => [
                'name' => 'USPS Media Mail',
                'description' => 'Economical rate for media',
                'typical_days' => '2-8',
            ],
        ];
    }

    /**
     * Validate address with Endicia
     *
     * @param array $address
     * @return array|null Validated address or null if invalid
     */
    public function validateAddress(array $address): ?array
    {
        // TODO: Implement Endicia Address Verification if needed
        // This would use the AddressVerification endpoint
        return $address;
    }

    /**
     * Test API connection
     *
     * @return bool
     */
    public function testConnection(): bool
    {
        try {
            $testAddress = [
                'zip' => '10001',
                'state' => 'NY',
                'city' => 'New York',
            ];

            $testWeight = ['weight_oz' => 5, 'weight_lbs' => 0];
            $testDimensions = [
                'length' => 10,
                'width' => 8,
                'height' => 1,
                'type' => 'envelope',
                'endicia_type' => 'Flat',
            ];

            $result = $this->getPostageRate(
                $testAddress,
                $testWeight,
                $testDimensions,
                'Priority'
            );

            return $result !== null;

        } catch (\Exception $e) {
            Log::error('Endicia connection test failed: ' . $e->getMessage());
            return false;
        }
    }
}
