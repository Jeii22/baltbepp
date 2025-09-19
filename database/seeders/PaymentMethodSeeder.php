<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        if (!\Schema::hasTable('payment_methods')) {
            return; // table not yet migrated
        }

        // Create a default COD method by convention (no DB row needed), and two digital wallets
        PaymentMethod::updateOrCreate(
            ['type' => 'gcash', 'label' => 'GCash Main Wallet'],
            [
                'account_name' => 'BaltBep Transport',
                'account_number' => '0999-000-0000',
                'is_active' => true,
            ]
        );

        PaymentMethod::updateOrCreate(
            ['type' => 'paymaya', 'label' => 'Maya Corporate'],
            [
                'account_name' => 'BaltBep Transport',
                'account_number' => 'MY-1234-5678',
                'is_active' => true,
            ]
        );
    }
}