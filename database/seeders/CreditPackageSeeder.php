<?php

namespace Database\Seeders;

use App\Models\CreditPackage;
use Illuminate\Database\Seeder;

class CreditPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Pachet Micro',
                'description' => '1 videoclip',
                'credits' => 1,
                'price' => 999, // 9.99 lei (stored in cents)
                'is_active' => true,
            ],
            [
                'name' => 'Pachet Basic',
                'description' => '3 videoclipuri (~6,67 lei/video)',
                'credits' => 3,
                'price' => 1999, // 19.99 lei
                'is_active' => true,
            ],
            [
                'name' => 'Pachet Standard',
                'description' => '5 videoclipuri (5 lei/video)',
                'credits' => 5,
                'price' => 2499, // 24.99 lei
                'is_active' => true,
            ],
            [
                'name' => 'Pachet Value',
                'description' => '10 videoclipuri (4 lei/video)',
                'credits' => 10,
                'price' => 3999, // 39.99 lei
                'is_active' => true,
            ],
            [
                'name' => 'Pachet Premium',
                'description' => '20 videoclipuri (3,5 lei/video)',
                'credits' => 20,
                'price' => 6999, // 69.99 lei
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            CreditPackage::updateOrCreate(
                ['name' => $package['name']],
                $package
            );
        }
    }
}
