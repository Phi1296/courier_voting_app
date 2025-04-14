<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Courier;
use Illuminate\Support\Carbon;

class CouriersSeeder extends Seeder
{
    public function run()
    {
        $couriers = [
            ['title' => 'ACS', 'description' => 'Courier service for fast and secure deliveries.'],
            ['title' => 'Μεταφορική', 'description' => 'Εξειδικευμένες υπηρεσίες μεταφοράς και παράδοσης για βαρύ φορτίο και εμπορεύματα.'],
            ['title' => 'Speedex', 'description' => 'Express delivery services for all your shipping needs.'],
            ['title' => 'DHL', 'description' => 'International courier and logistics services.'],
            ['title' => 'FedEx', 'description' => 'Global shipping and courier solutions.'],
            ['title' => 'Courier Center', 'description' => 'Υπηρεσίες courier και μεταφορών για άμεση παράδοση και οικονομικές λύσεις.'],
        ];

        $now = Carbon::now();

        foreach ($couriers as $index => $data) {
            Courier::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'created_at' => $now->copy()->addSeconds($index),
                'updated_at' => $now->copy()->addSeconds($index),
            ]);
        }
    }
}
