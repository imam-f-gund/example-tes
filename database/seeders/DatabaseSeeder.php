<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\Customer;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'ABC',
            'description' => 'cetak ABC',
            'price' => 1000.00,
            'category' => 'cetak digital',
        ]);

        Product::create([
            'name' => 'XYZ',
            'description' => 'copy XYZ',
            'price' => 100.00,
            'category' => 'fotocopy',
        ]);
        
        User::create([
            'name' => 'admin',
            'email' => 'admin@mail.com',
            'username' => 'admin',
            'password' => bcrypt('password'),
        ]);

        User::create([
            'name' => 'operator',
            'email' => 'operator@mail.com',
            'username' => 'operator',
            'password' => bcrypt('password'),
        ]);

        Customer::create([
            'name' => 'cust',
            'email' => 'cust@mail.com',
        ]);
    }
}
