<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Config::get('pharmacy.billing.default_payment_methods') as $name) {
            PaymentMethod::firstOrCreate(
                ['name' => $name],
                ['is_active' => true],
            );
        }
    }
}
