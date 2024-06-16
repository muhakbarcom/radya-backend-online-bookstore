<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Order::factory(10)->create()->each(function ($order) {
            // Mengisi tabel order_items dengan 5 data dummy untuk setiap order
            OrderItem::factory(5)->create(['order_id' => $order->id]);
        });
    }
}
