<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@noctocode.online'],
            [
                'name'     => 'Admin',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role'     => 'admin',
                'bio'      => 'Основатель nocto.hub',
            ]
        );
    }
}
