<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * @return void
     */
    public function run(): void
    {
        User::create([
            'name' => 'Joe',
            'email' => 'joe.test@example.com',
            'password' => Hash::make('joes_password')
        ]);
        User::create([
            'name' => 'John',
            'email' => 'john.test@example.com',
            'password' => Hash::make('johns_password')
        ]);
        User::create([
            'name' => 'Joel',
            'email' => 'joel.test@example.com',
            'password' => Hash::make('joels_password')
        ]);
    }
}
