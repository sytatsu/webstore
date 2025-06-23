<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Database\Seeders\products\ProductSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create some dev users
        if (env('APP_DEBUG')) {
            User::firstOrCreate([
                'name' => 'Steve Admin',
                'email' => 'admin@sytatsu.nl',
                'password' => Hash::make('password'),
            ]);

            User::firstOrCreate([
                'name' => 'Steve User',
                'email' => 'user@sytatsu.nl',
                'password' => Hash::make('password'),
            ]);
        }

        // fill database with real data
        $this->call([
            //
        ]);
    }
}
