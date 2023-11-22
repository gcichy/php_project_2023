<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product')->insert([
            'name' => 'Łóżko Domek',
            'material' => 'drewno',
            'color' => 'biały',
            'description' => 'Łóżko Domek wykonane z drewna w kolorze białym.',
            'price' => 799,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('product')->insert([
            'name' => 'Łóżko Domek',
            'material' => 'płyta MDF',
            'color' => 'szary',
            'description' => 'Łóżko Domek wykonane z płyty MDF w kolorze szarym.',
            'price' => 799,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
    }

}
