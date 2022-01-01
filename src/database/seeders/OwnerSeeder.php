<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('owners')->insert([
            'name' => 'オーナー1',
            'email' => 'test1@test.com',
            'password' => Hash::make('password1'),
            'created_at' => '2021/01/01 11:11:11'
        ]);
        DB::table('owners')->insert([
            'name' => 'オーナー1',
            'email' => 'test2@test.com',
            'password' => Hash::make('password1'),
            'created_at' => '2021/01/01 11:11:11'
        ]);
        DB::table('owners')->insert([
            'name' => 'オーナー1',
            'email' => 'test3@test.com',
            'password' => Hash::make('password1'),
            'created_at' => '2021/01/01 11:11:11'
        ]);
    }
}
