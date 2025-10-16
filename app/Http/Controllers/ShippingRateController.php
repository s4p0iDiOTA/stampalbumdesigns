<?php

namespace App\Http\Controllers;

use App\Services\EndiciaService;
use App\Services\ShippingCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ShippingRateController extends Controller
{
    private EndiciaService $endiciaService;
    private ShippingCalculator $shippingCalculator;

    public function __construct(EndiciaService $endiciaService, ShippingCalculator $shippingCalculator)
    {
        $this->endiciaService = $endiciaService;
        $this->shippingCalculator = $shippingCalculator;
    }

    /**
     * Get real-time shipping rates for current cart
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRates(Request $request)
    {
        $validated = $request->validate([
            'zip' => 'required|string|max:10',
            'state' => 'nullable|string|max:2',
            'city' => 'nullable|string|max:100',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        try {
            // Get shipping breakdown
            $breakdown = $this->shippingCalculator->getShippingBreakdown($cart);

            // Get rates from Endicia
            $rates = $this->endiciaService->getRates($validated, $cart);

            if (empty($rates)) {
                // Fallback to static rates if API fails
                $rates = $this->getFallbackRates();
            }

            return response()->json([
                'success' => true,
                'rates' => $rates,
                'breakdown' => $breakdown,
            ]);

        } catch (\Exception $e) {
            Log::error('Shipping rate calculation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate shipping rates',
                'rates' => $this->getFallbackRates(),
            ], 500);
        }
    }

    /**
     * Get shipping breakdown for current cart
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBreakdown()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        try {
            $breakdown = $this->shippingCalculator->getShippingBreakdown($cart);

            return response()->json([
                'success' => true,
                'breakdown' => $breakdown,
            ]);

        } catch (\Exception $e) {
            Log::error('Shipping breakdown calculation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate shipping breakdown',
            ], 500);
        }
    }

    /**
     * Test Endicia API connection
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection()
    {
        try {
            $isConnected = $this->endiciaService->testConnection();

            return response()->json([
                'success' => $isConnected,
                'message' => $isConnected
                    ? 'Successfully connected to Endicia API'
                    : 'Failed to connect to Endicia API',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get fallback rates when API is unavailable
     *
     * @return array
     */
    private function getFallbackRates(): array
    {
        return [
            [
                'service_code' => 'first',
                'service_name' => 'USPS First-Class Mail',
                'cost' => 5.99,
                'currency' => 'USD',
                'delivery_days' => '2-5 business days',
                'provider' => 'USPS',
            ],
            [
                'service_code' => 'priority',
                'service_name' => 'USPS Priority Mail',
                'cost' => 9.99,
                'currency' => 'USD',
                'delivery_days' => '2-3 business days',
                'provider' => 'USPS',
            ],
            [
                'service_code' => 'priority_express',
                'service_name' => 'USPS Priority Mail Express',
                'cost' => 29.99,
                'currency' => 'USD',
                'delivery_days' => '1-2 business days',
                'provider' => 'USPS',
            ],
        ];
    }
}
