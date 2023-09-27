<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('task')->insert([
            'name' => 'piła - formatowanie deski do szerokości',
            'description'=> '1 etap obróbki elementów drewnianych - surowa deska jest formatowana do wymaganej szerkości.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'name' => 'frezarka 4-stronna - struganie deski',
            'description'=> '',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'name' => 'jakaś maszyna - formatowanie deski na wymiar',
            'description'=> 'Na tym etapie desce nadawany jest finalny kształt elementu ',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'name' => 'szlifierka - szlifowanie elementu',
            'description'=> '',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'name' => 'jakaś maszyna - wiercenie elementu',
            'description'=> 'Wiercenie otworów na kołki pozwalające na złożenie późniejsze złożenie całości produktu',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'name' => 'lakiernia - lakierowanie 1',
            'description'=> 'Nałożenie 1 warstwy lakieru/farby',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'name' => 'lakiernia - lakierowanie 2',
            'description'=> 'Nałożenie 2 warstwy lakieru/farby',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'name' => 'szlifierka - szlifowanie po lakierowaniu',
            'description'=> '',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'name' => 'pakowanie produktu',
            'description'=> 'Pakowanie wszystkich elementów produktu i przygotowanie towaru do wysyłki.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'name' => 'CNC - wycinanie elementu',
            'description'=> 'Wycinanie z surowej płyty MDF kształtu elementu na maszynie CNC.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'name' => 'szlifierka - szlifowanie krawędzi elementu wykonanego z płyty MDF',
            'description'=> '',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'name' => 'stworzenie drewnianej barierki',
            'description'=> 'tworzenie drewnianych barierek ze sformatowanych długich elementów oraz porzecznych szczebelków ',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
    }
}
