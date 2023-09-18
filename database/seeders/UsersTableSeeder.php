<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'umesh badgujar',
            'mobile_no' => '9722862510',
            'email' => 'umesh@yopmail.com',
            'address' => 'new sama road,vadodara',
            'password' => Hash::make('123456'),
            'is_active' => '1',
            'is_delete' => '0'
        ]);
    }
}
