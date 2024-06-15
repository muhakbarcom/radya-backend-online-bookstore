<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create a user with the role of admin
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
        ])->assignRole('admin');

        // create a user with the role of customer
        $user = User::create([
            'name' => 'Customer',
            'email' => 'customer_1@gmail.com',
            'password' => bcrypt('password'),
        ])->assignRole('customer');
    }
}
