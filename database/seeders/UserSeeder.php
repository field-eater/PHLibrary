<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('users')->insert([
            'user_name' => 'z33ro',
            'first_name' => 'Phil',
            'last_name' => 'Atun',
            'gender' => 'male',
            'address' => 'No. 44 Road 7, North Daanghari, Taguig City 1630, Metro Manila, Philippines',
            'date_of_birth' => new Carbon('11/16/2000'),
            'is_admin' => true,
            'is_activated' => true,
            'email' => 'atunphil@gmail.com',
            'password' => Hash::make('password'),
        ]);

        DB::table('users')->insert([
            'user_name' => 'lastManStanding',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'male',
            'address' => 'Random St.',
            'date_of_birth' => new Carbon ('10/31/2004'),
            'is_admin' => false,
            'is_activated' => true,
            'email' => 'johndoe@gmail.com',
            'password' => Hash::make('password'),
        ]);

        DB::table('students')->insert([
            'user_id' => 2,
            'student_number' => '2019-00237-TG-0',
            'course' => 'BSIT',
            'admission_year' => '2019',
            'year_level' => '4',
            'biography' => 'Happy guy',
        ]);

        DB::table('users')->insert([
            'user_name' => 'janeD',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'female',
            'address' => 'Random St.',
            'date_of_birth' => new Carbon('05/29/2001'),
            'is_admin' => false,
            'is_activated' => true,
            'email' => 'janedoe@gmail.com',
            'password' => Hash::make('password'),
        ]);

        DB::table('students')->insert([
            'user_id' => 3,
            'student_number' => '2019-00427-TG-0',
            'course' => 'BSOA',
            'admission_year' => '2019',
            'year_level' => '4',
            'biography' => 'Happy Girl',
        ]);
    }
}
