<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['Distribuidora CEFA',   'Carlos Mendoza',  '2232-5500', 'ventas@cefa.hn',         'Tegucigalpa, Col. Palmira'],
            ['Droguería Nacional',   'Ana Flores',      '2237-8800', 'pedidos@drogueria.hn',    'San Pedro Sula, Bo. Guamilito'],
            ['Farmacéutica Global',  'Roberto Pineda',  '2221-3300', 'info@farmaglobal.hn',     'Tegucigalpa, Col. Kennedy'],
        ];

        foreach ($suppliers as [$name, $contact, $phone, $email, $address]) {
            Supplier::firstOrCreate(
                ['name' => $name],
                [
                    'contact_name' => $contact,
                    'phone' => $phone,
                    'email' => $email,
                    'address' => $address,
                    'is_active' => true,
                ],
            );
        }
    }
}
