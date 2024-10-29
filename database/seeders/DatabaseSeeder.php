<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (env('APP_DEBUG')) {
            User::firstOrCreate([
                'name' => 'Steve Admin',
                'email' => 'admin@sytatsu.nl',
                'password' => Hash::make('password'),
                'password_updated_at' => Carbon::now(),
                'is_admin' => true,
            ]);

            User::firstOrCreate([
                'name' => 'Steve User',
                'email' => 'user@sytatsu.nl',
                'password' => Hash::make('password'),
                'password_updated_at' => Carbon::now(),
                'is_admin' => false,
            ]);

            $products = Product::factory(3)->create(['has_multiple_variants' => true]);
            $products->map(fn (Product $product) => ProductVariant::factory(3)->create([
                'product_uuid' => $product->uuid
            ]));
        }
    }
}
