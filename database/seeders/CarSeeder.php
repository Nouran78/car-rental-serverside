<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarSeeder extends Seeder
{
    public function run()
    {
        DB::table('cars')->insert([
            [
                'name' => 'Toyota Corolla',
                'type' => 'Sedan',
                'price_per_day' => 100.00,
                'availability_status' => true,
            ],
            [
                'name' => 'Honda Civic',
                'type' => 'Sedan',
                'price_per_day' => 120.00,
                'availability_status' => true,
            ],
            [
                'name' => 'Ford Mustang',
                'type' => 'Coupe',
                'price_per_day' => 150.00,
                'availability_status' => true,
            ],
            [
                'name' => 'Chevrolet Camaro',
                'type' => 'Coupe',
                'price_per_day' => 140.00,
                'availability_status' => true,
            ],
            [
                'name' => 'Tesla Model 3',
                'type' => 'Electric',
                'price_per_day' => 200.00,
                'availability_status' => true,
            ],
            [
                'name' => 'BMW X5',
                'type' => 'SUV',
                'price_per_day' => 180.00,
                'availability_status' => true,
            ],
            [
                'name' => 'Jeep Wrangler',
                'type' => 'SUV',
                'price_per_day' => 160.00,
                'availability_status' => true,
            ],
            [
                'name' => 'Audi A4',
                'type' => 'Sedan',
                'price_per_day' => 170.00,
                'availability_status' => true,
            ],
            [
                'name' => 'Mercedes-Benz GLC',
                'type' => 'SUV',
                'price_per_day' => 190.00,
                'availability_status' => true,
            ],
            [
                'name' => 'Nissan Leaf',
                'type' => 'Electric',
                'price_per_day' => 130.00,
                'availability_status' => true,
            ],

        ]);
    }
}
