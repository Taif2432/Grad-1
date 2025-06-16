<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;


class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@example.com'
            ],
            [
                'name' => 'Second Admin',
                'email' => 'admin2@example.com'
            ],
            [
                'name' => 'HR Admin',
                'email' => 'adminHR@example.com'
            ],
        ];

        foreach ($admins as $admin){
            User::updateOrCreate(
                ['email' => $admin['email']],
                [
                    'name' => $admin['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'admin',
                    'is_approved' => true,
                    'email_verified_at' => Carbon::now(),
                ]
                );
        }
     
    }
}
