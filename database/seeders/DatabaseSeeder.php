<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Llamar a RoleSeeder
        $this->call([
            RoleSeeder::class
        ]);
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Hector Pat',
            'email' => 'hector@gmail.com',
            'password'=> bcrypt ('12345678')
        ]);
    }
}
