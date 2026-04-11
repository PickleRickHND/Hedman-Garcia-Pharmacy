<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['SKU100', 'Acetaminofen 500mg',      'Analgesico y antipiretico para fiebre y dolor leve a moderado',   150,  45.00, '2027-06-30', 'Tableta 500mg',  'Oral',     'Temperatura ambiente', 'Caja 24 und'],
            ['SKU101', 'Ibuprofeno 400mg',        'Antiinflamatorio no esteroideo para dolor e inflamacion',         120,  60.00, '2027-03-15', 'Tableta 400mg',  'Oral',     'Temperatura ambiente', 'Caja 20 und'],
            ['SKU102', 'Amoxicilina 500mg',       'Antibiotico betalactamico de amplio espectro',                     80, 180.00, '2026-12-20', 'Capsula 500mg',  'Oral',     'Lugar seco',           'Caja 21 und'],
            ['SKU103', 'Omeprazol 20mg',          'Inhibidor de la bomba de protones para reflujo y gastritis',       95, 120.00, '2027-09-10', 'Capsula 20mg',   'Oral',     'Temperatura ambiente', 'Caja 14 und'],
            ['SKU104', 'Loratadina 10mg',         'Antihistaminico para alergias estacionales',                      110,  75.00, '2027-05-01', 'Tableta 10mg',   'Oral',     'Temperatura ambiente', 'Caja 10 und'],
            ['SKU105', 'Metformina 850mg',        'Antidiabetico oral para diabetes tipo 2',                          60,  95.00, '2026-11-30', 'Tableta 850mg',  'Oral',     'Lugar seco',           'Caja 30 und'],
            ['SKU106', 'Atorvastatina 20mg',      'Hipolipemiante para control de colesterol',                        45, 220.00, '2027-02-14', 'Tableta 20mg',   'Oral',     'Temperatura ambiente', 'Caja 30 und'],
            ['SKU107', 'Salbutamol Inhalador',    'Broncodilatador para asma y EPOC',                                 30, 350.00, '2026-08-25', 'Aerosol 100mcg', 'Inhalada', 'Temperatura ambiente', 'Envase 200 dosis'],
            ['SKU108', 'Diclofenaco Gel 1%',      'Antiinflamatorio topico para dolor muscular',                      70, 140.00, '2027-04-18', 'Gel tubo 60g',   'Topica',   'Temperatura ambiente', 'Tubo'],
            ['SKU109', 'Azitromicina 500mg',      'Antibiotico macrolido para infecciones respiratorias',             50, 280.00, '2026-10-05', 'Tableta 500mg',  'Oral',     'Lugar seco',           'Caja 3 und'],
            ['SKU110', 'Losartan 50mg',           'Antihipertensivo antagonista de angiotensina II',                  85, 110.00, '2027-07-22', 'Tableta 50mg',   'Oral',     'Temperatura ambiente', 'Caja 30 und'],
            ['SKU111', 'Cetirizina 10mg',         'Antihistaminico para rinitis alergica',                           100,  68.00, '2027-01-12', 'Tableta 10mg',   'Oral',     'Temperatura ambiente', 'Caja 10 und'],
            ['SKU112', 'Ranitidina 150mg',        'Antiacido antagonista H2',                                          0,  85.00, '2026-09-30', 'Tableta 150mg',  'Oral',     'Lugar seco',           'Caja 20 und'],
            ['SKU113', 'Ciprofloxacina 500mg',    'Antibiotico de amplio espectro',                                   40, 210.00, '2027-03-08', 'Tableta 500mg',  'Oral',     'Temperatura ambiente', 'Caja 10 und'],
            ['SKU114', 'Paracetamol Jarabe',      'Analgesico pediatrico sabor fresa',                                55,  95.00, '2026-07-15', 'Jarabe 120ml',   'Oral',     'Refrigerar tras abrir','Frasco'],
            ['SKU115', 'Aspirina 100mg',          'Antiagregante plaquetario en dosis baja',                         200,  40.00, '2027-11-20', 'Tableta 100mg',  'Oral',     'Temperatura ambiente', 'Caja 30 und'],
            ['SKU116', 'Dexametasona 4mg',        'Corticoide sistemico antiinflamatorio',                             5, 180.00, '2026-12-01', 'Tableta 4mg',    'Oral',     'Temperatura ambiente', 'Caja 20 und'],
            ['SKU117', 'Clotrimazol Crema 1%',    'Antimicotico topico',                                              35, 125.00, '2027-02-28', 'Crema tubo 30g', 'Topica',   'Temperatura ambiente', 'Tubo'],
            ['SKU118', 'Enalapril 10mg',          'Inhibidor de la ECA para hipertension',                            75,  88.00, '2027-05-16', 'Tableta 10mg',   'Oral',     'Temperatura ambiente', 'Caja 30 und'],
            ['SKU119', 'Multivitaminico Adulto',  'Suplemento vitaminico y mineral diario',                          130, 250.00, '2028-01-10', 'Tableta',        'Oral',     'Temperatura ambiente', 'Frasco 60 und'],
            ['SKU120', 'Suero Oral Pediatrico',   'Solucion de rehidratacion oral',                                   90,  35.00, '2027-08-05', 'Sobre 30g',      'Oral',     'Temperatura ambiente', 'Sobre'],
            ['SKU121', 'Amoxi+Clavulanico 875mg', 'Antibiotico betalactamico con inhibidor de betalactamasas',        42, 320.00, '2026-06-18', 'Tableta 875mg',  'Oral',     'Lugar seco',           'Caja 14 und'],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(
                ['sku' => $p[0]],
                [
                    'name' => $p[1],
                    'description' => $p[2],
                    'stock' => $p[3],
                    'price' => $p[4],
                    'expiration_date' => $p[5],
                    'presentation' => $p[6],
                    'administration_form' => $p[7],
                    'storage' => $p[8],
                    'packaging' => $p[9],
                ]
            );
        }
    }
}
