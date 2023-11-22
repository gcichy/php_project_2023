<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstructionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('instruction')->insert([
            'task_id' => 1,
            'name' => 'Instrukcja zadania: piła - formatowanie deski do szerokości',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('instruction')->insert([
            'product_id' => 1,
            'name' => 'Instrukcja wykonania produktu: Łóżko Domek - drewno',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
    }
}
