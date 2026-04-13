<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Pharmacy Business Configuration
|--------------------------------------------------------------------------
|
| Constantes de negocio para Hedman Garcia Pharmacy. Fuente unica de
| verdad para impuestos, limites de inputs, TTLs y branding de facturas.
| Consumir con `config('pharmacy.*')` en lugar de magic numbers.
|
*/

return [

    'timezone' => env('APP_TIMEZONE', 'America/Tegucigalpa'),

    'tax' => [
        'isv_rate' => (float) env('PHARMACY_ISV_RATE', 0.15),
    ],

    'billing' => [
        'invoice_number_prefix' => env('PHARMACY_INVOICE_PREFIX', 'FHG-'),
        'invoice_number_padding' => 6,
        'max_discount_percent' => (float) env('PHARMACY_MAX_DISCOUNT', 30),
        'default_payment_methods' => [
            'Efectivo',
            'Tarjeta de Credito',
            'Tarjeta de Debito',
            'Transferencia',
        ],
    ],

    'limits' => [
        'user_name_max' => 30,
        'user_lastname_max' => 30,
        'user_email_max' => 100,
        'product_name_max' => 50,
        'product_description_max' => 500,
        'product_presentation_max' => 50,
        'product_administration_max' => 50,
        'product_storage_max' => 100,
        'product_packaging_max' => 50,
    ],

    'stock' => [
        'low_threshold' => 10,
        'expiring_soon_days' => 30,
    ],

    'password_reset' => [
        'code_ttl_minutes' => 15,
        'code_length_bytes' => 8,
    ],

    'roles' => [
        'admin' => 'Administrador',
        'cashier' => 'Cajero',
        'guest' => 'Invitado',
    ],
];
