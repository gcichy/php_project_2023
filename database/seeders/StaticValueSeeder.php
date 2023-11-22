<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaticValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('static_value')->insert([
            'type' => 'material',
            'value' => 'drewno',
            'value_full' => 'drewno',
        ]);
        DB::table('static_value')->insert([
            'type' => 'material',
            'value' => 'MDF',
            'value_full' => 'płyta MDF',
        ]);
    }
}
