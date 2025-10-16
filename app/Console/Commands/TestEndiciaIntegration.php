<?php

namespace App\Console\Commands;

use App\Services\EndiciaService;
use App\Services\ShippingCalculator;
use Illuminate\Console\Command;

class TestEndiciaIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'endicia:test
                          {--zip=10001 : Test destination ZIP code}
                          {--pages=50 : Number of pages to test}
                          {--paper=0.25 : Paper type price}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Endicia API integration and shipping calculations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('===========================================');
        $this->info('   Endicia Integration Test');
        $this->info('===========================================');
        $this->newLine();

        // Get options
        $testZip = $this->option('zip');
        $testPages = (int) $this->option('pages');
        $testPaperType = $this->option('paper');

        // Step 1: Check configuration
        $this->info('Step 1: Checking Configuration');
        $this->line('-------------------------------------------');

        $config = config('services.endicia');
        $this->table(
            ['Setting', 'Value'],
            [
                ['API URL', $config['api_url'] ?? 'Not set'],
                ['Account ID', $config['account_id'] ? '✓ Set' : '✗ Not set'],
                ['Pass Phrase', $config['pass_phrase'] ? '✓ Set' : '✗ Not set'],
                ['From ZIP', $config['from_zip'] ?? 'Not set'],
                ['Test Mode', $config['test_mode'] ? 'Yes' : 'No'],
            ]
        );
        $this->newLine();

        if (!$config['account_id'] || !$config['pass_phrase']) {
            $this->error('❌ Endicia credentials not configured!');
            $this->info('Add ENDICIA_ACCOUNT_ID and ENDICIA_PASS_PHRASE to your .env file');
            return Command::FAILURE;
        }

        // Step 2: Test cart creation
        $this->info('Step 2: Creating Test Cart');
        $this->line('-------------------------------------------');

        $testCart = [
            'test_item' => [
                'order_groups' => [
                    [
                        'paperType' => $testPaperType,
                        'totalPages' => $testPages,
                        'country' => 'Test Country',
                    ]
                ],
                'quantity' => 1,
                'total' => $testPages * (float) $testPaperType,
            ]
        ];

        $this->info("Pages: {$testPages}");
        $this->info("Paper Type: \${$testPaperType}/page");
        $this->info("Order Total: \$" . number_format($testCart['test_item']['total'], 2));
        $this->newLine();

        // Step 3: Test weight calculation
        $this->info('Step 3: Calculating Weight & Dimensions');
        $this->line('-------------------------------------------');

        $calculator = new ShippingCalculator();
        $breakdown = $calculator->getShippingBreakdown($testCart);

        $weight = $breakdown['total_weight'];
        $dimensions = $breakdown['dimensions'];

        $this->table(
            ['Metric', 'Value'],
            [
                ['Weight (oz)', $weight['weight_oz']],
                ['Weight (lbs)', $weight['weight_lbs']],
                ['Length', $dimensions['length'] . '"'],
                ['Width', $dimensions['width'] . '"'],
                ['Height', $dimensions['height'] . '"'],
                ['Package Type', $dimensions['type']],
                ['Endicia Type', $dimensions['endicia_type']],
            ]
        );
        $this->newLine();

        // Step 4: Display paper breakdown
        if (!empty($breakdown['paper_types'])) {
            $this->info('Paper Type Breakdown:');
            foreach ($breakdown['paper_types'] as $type => $data) {
                $this->line("  • {$data['name']}: {$data['pages']} pages, " .
                           number_format($data['weight_oz'], 2) . " oz");
            }
            $this->newLine();
        }

        // Step 5: Test API connection
        $this->info('Step 4: Testing API Connection');
        $this->line('-------------------------------------------');

        $endiciaService = new EndiciaService();

        $this->line('Testing connection to Endicia API...');
        $bar = $this->output->createProgressBar(3);
        $bar->start();

        try {
            $bar->advance();
            sleep(1);

            $isConnected = $endiciaService->testConnection();

            $bar->advance();
            sleep(1);

            $bar->finish();
            $this->newLine(2);

            if ($isConnected) {
                $this->info('✓ Successfully connected to Endicia API');
            } else {
                $this->warn('⚠ Could not connect to Endicia API');
                $this->line('Check your credentials and network connection');
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $bar->finish();
            $this->newLine(2);
            $this->error('❌ API Connection Failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
        $this->newLine();

        // Step 6: Get live rates
        $this->info('Step 5: Fetching Live Shipping Rates');
        $this->line('-------------------------------------------');

        $testAddress = [
            'zip' => $testZip,
            'state' => '',
            'city' => '',
        ];

        $this->line("Destination ZIP: {$testZip}");
        $this->newLine();

        try {
            $rates = $endiciaService->getRates($testAddress, $testCart);

            if (empty($rates)) {
                $this->warn('⚠ No rates returned from API');
                $this->line('This could mean:');
                $this->line('  • Invalid ZIP code');
                $this->line('  • Service not available for this combination');
                $this->line('  • API error (check logs)');
                return Command::FAILURE;
            }

            $this->info('✓ Received ' . count($rates) . ' shipping option(s)');
            $this->newLine();

            $rateData = array_map(function($rate) {
                return [
                    $rate['service_name'],
                    '$' . number_format($rate['cost'], 2),
                    $rate['delivery_days'],
                    $rate['package_type'],
                ];
            }, $rates);

            $this->table(
                ['Service', 'Cost', 'Delivery Time', 'Package'],
                $rateData
            );

        } catch (\Exception $e) {
            $this->error('❌ Failed to fetch rates: ' . $e->getMessage());
            $this->line('Check storage/logs/laravel.log for details');
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info('===========================================');
        $this->info('✓ All tests completed successfully!');
        $this->info('===========================================');

        return Command::SUCCESS;
    }
}
