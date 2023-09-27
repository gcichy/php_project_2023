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
            'instruction_html' => '<h1>Instrukcja zadania: piła - formatowanie deski do szerokości</h1><br><p>1. Odpalić piłę i palców se nie urżnąć</p>',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('instruction')->insert([
            'product_id' => 1,
            'name' => 'Instrukcja wykonania produktu: Łóżko Domek - drewno',
            'instruction_html' => '<h1>Instrukcja wykonania produktu: Łóżko Domek - drewno</h1><br><p>1. Poskładać domek</p>',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
    }
}
