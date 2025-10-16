<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lunar\Models\Channel;
use Lunar\Models\Country;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Language;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration ensures that the core Lunar data exists.
     * These records are required for Lunar to function properly.
     */
    public function up(): void
    {
        // Create default Currency if none exists
        if (Currency::count() === 0) {
            Currency::create([
                'code' => 'USD',
                'name' => 'US Dollar',
                'exchange_rate' => 1.00,
                'decimal_places' => 2,
                'enabled' => true,
                'default' => true,
            ]);
        }

        // Create default Channel if none exists
        if (Channel::count() === 0) {
            Channel::create([
                'name' => 'Webstore',
                'handle' => 'webstore',
                'default' => true,
                'url' => config('app.url', 'http://localhost'),
            ]);
        }

        // Create default Language if none exists
        if (Language::count() === 0) {
            Language::create([
                'code' => 'en',
                'name' => 'English',
                'default' => true,
            ]);
        }

        // Create default Customer Group if none exists
        if (CustomerGroup::count() === 0) {
            CustomerGroup::create([
                'name' => 'Retail',
                'handle' => 'retail',
                'default' => true,
            ]);
        }

        // Create default Country (United States) if none exists
        if (Country::count() === 0) {
            Country::create([
                'name' => 'United States',
                'iso3' => 'USA',
                'iso2' => 'US',
                'phonecode' => 1,
                'capital' => 'Washington',
                'currency' => 'USD',
                'native' => 'United States',
                'emoji' => '🇺🇸',
                'emoji_u' => 'U+1F1FA U+1F1F8',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't delete these records on rollback as they may be in use
        // If you need to remove them, do so manually
    }
};
