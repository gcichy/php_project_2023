<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'firstName' => 'Grzegorz',
            'lastName' => 'Cichy',
            'employeeNo' => 'gcichy',
            'role' => 'admin',
            'email' => 'g.cichy2001@gmail.com',
            'password' => bcrypt('secret'),
            'isVerified' => true,
        ]);
        DB::table('users')->insert([
            'firstName' => 'Marcin',
            'lastName' => 'Borcz',
            'employeeNo' => 'mborcz',
            'role' => 'manager',
            'email' => 'default@gmail.com',
            'password' => bcrypt('secret'),
            'isVerified' => true,
        ]);
    }

}
