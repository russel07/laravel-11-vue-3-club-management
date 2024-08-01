<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id'                =>  1,
                'team_id'           =>  null,
                'name'              =>  'System Admin',
                'gender'            =>  'Male',
                'user_type'         =>  'Admin',
                'birth_year'        =>  '1991',
                'email'             =>  'test@admin.co',
                'password'          =>   bcrypt('Password12@'),
                'user_type'         =>  'Admin',
                'created_at'        =>   date('Y-m-d H:i:s'),
                'updated_at'        =>   date('Y-m-d H:i:s'),
            ],
            [
                'id'                =>  2,
                'team_id'           =>  null,
                'name'              =>  'Club Admin',
                'gender'            =>  'Male',
                'user_type'         =>  'Club Admin',
                'birth_year'        =>  '1991',
                'email'             =>  'test@club_admin.co',
                'password'          =>   bcrypt('Password12@'),
                'user_type'         =>  'Admin',
                'created_at'        =>  date('Y-m-d H:i:s'),
                'updated_at'        =>  date('Y-m-d H:i:s'),
            ]
        ]);
    }
}
