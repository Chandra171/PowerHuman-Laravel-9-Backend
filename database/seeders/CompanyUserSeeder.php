<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CompanyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($i =0; $i<10; $i++){
            DB::table('user_company')->insert([
                'user_id' => rand(1,10), //rand adalah random integer
                'company_id' => rand(1,10)
            ]);
        }
    }
}
