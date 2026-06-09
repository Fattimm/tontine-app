<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Admin',
                'email'    => 'admin@tontine.sn',
                'password' => Hash::make('passer123'),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Organisateur 1',
                'email'    => 'organisateur1@tontine.sn',
                'password' => Hash::make('passer123'),
                'role'     => 'organisateur',
            ],
            [
                'name'     => 'Organisateur 2',
                'email'    => 'organisateur2@tontine.sn',
                'password' => Hash::make('passer123'),
                'role'     => 'organisateur',
            ],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(['email' => $data['email']], $data);
        }

        $this->command->info(User::count() . ' utilisateur(s) en base.');
    }
}
