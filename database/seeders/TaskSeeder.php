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
            'production_schema_id' => 2,
            'name' => 'piła - formatowanie deski do szerokości',
            'sequence_no' => 1,
            'amount_required' => 0,
            'description'=> '1 etap obróbki elementów drewnianych - surowa deska jest formatowana do wymaganej szerkości.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'production_schema_id' => 2,
            'name' => 'frezarka 4-stronna - struganie deski',
            'sequence_no' => 2,
            'amount_required' => 0,
            'description'=> '',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'production_schema_id' => 2,
            'name' => 'jakaś maszyna - formatowanie deski na wymiar',
            'sequence_no' => 3,
            'amount_required' => 0,
            'description'=> 'Na tym etapie desce nadawany jest finalny kształt elementu ',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'production_schema_id' => 2,
            'name' => 'szlifierka - szlifowanie elementu',
            'sequence_no' => 4,
            'amount_required' => 0,
            'description'=> '',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'production_schema_id' => 2,
            'name' => 'jakaś maszyna - wiercenie elementu',
            'sequence_no' => 5,
            'amount_required' => 1,
            'description'=> 'Wiercenie otworów na kołki pozwalające na złożenie późniejsze złożenie całości produktu',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'production_schema_id' => 3,
            'name' => 'lakiernia - lakierowanie 1',
            'sequence_no' => 1,
            'amount_required' => 0,
            'description'=> 'Nałożenie 1 warstwy lakieru/farby',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'production_schema_id' => 3,
            'name' => 'lakiernia - lakierowanie 2',
            'sequence_no' => 3,
            'amount_required' => 1,
            'description'=> 'Nałożenie 2 warstwy lakieru/farby',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'production_schema_id' => 3,
            'name' => 'szlifierka - szlifowanie po lakierowaniu',
            'sequence_no' => 2,
            'amount_required' => 0,
            'description'=> '',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'production_schema_id' => 1,
            'name' => 'pakowanie produktu',
            'sequence_no' => 1,
            'amount_required' => 1,
            'description'=> 'Pakowanie wszystkich elementów produktu i przygotowanie towaru do wysyłki.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'production_schema_id' => 4,
            'name' => 'CNC - wycinanie elementu',
            'sequence_no' => 1,
            'amount_required' => 0,
            'description'=> 'Wycinanie z surowej płyty MDF kształtu elementu na maszynie CNC.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'production_schema_id' => 4,
            'name' => 'szlifierka - szlifowanie krawędzi elementu wykonanego z płyty MDF',
            'sequence_no' => 2,
            'amount_required' => 0,
            'description'=> '',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        DB::table('task')->insert([
            'production_schema_id' => 4,
            'name' => 'jakaś maszyna - wiercenie elementu',
            'sequence_no' => 3,
            'amount_required' => 1,
            'description'=> 'Wiercenie otworów na kołki pozwalające na złożenie późniejsze złożenie całości produktu',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        DB::table('task')->insert([
            'production_schema_id' => 5,
            'name' => 'lakiernia - lakierowanie 1',
            'sequence_no' => 1,
            'amount_required' => 1,
            'description'=> 'Nałożenie 1 warstwy lakieru/farby',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);


        DB::table('task')->insert([
            'production_schema_id' => 6,
            'name' => 'piła - formatowanie deski do szerokości',
            'sequence_no' => 1,
            'amount_required' => 0,
            'description'=> '1 etap obróbki elementów drewnianych - surowa deska jest formatowana do wymaganej szerkości.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        DB::table('task')->insert([
            'production_schema_id' => 6,
            'name' => 'jakaś maszyna - formatowanie deski na wymiar',
            'sequence_no' => 2,
            'amount_required' => 0,
            'description'=> 'Na tym etapie desce nadawany jest finalny kształt elementu ',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        DB::table('task')->insert([
            'production_schema_id' => 6,
            'name' => 'jakaś maszyna - wiercenie elementu',
            'sequence_no' => 3,
            'amount_required' => 1,
            'description'=> 'Wiercenie otworów na kołki pozwalające na złożenie późniejsze złożenie całości produktu',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);


        DB::table('task')->insert([
            'production_schema_id' => 7,
            'name' => 'piła - formatowanie deski do szerokości',
            'sequence_no' => 1,
            'amount_required' => 0,
            'description'=> '1 etap obróbki elementów drewnianych - surowa deska jest formatowana do wymaganej szerkości.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);


        DB::table('task')->insert([
            'production_schema_id' => 7,
            'name' => 'frezarka 4-stronna - struganie deski',
            'sequence_no' => 2,
            'amount_required' => 0,
            'description'=> '',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        DB::table('task')->insert([
            'production_schema_id' => 7,
            'name' => 'jakaś maszyna - formatowanie deski na wymiar',
            'sequence_no' => 3,
            'amount_required' => 0,
            'description'=> 'Na tym etapie desce nadawany jest finalny kształt elementu ',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'production_schema_id' => 7,
            'name' => 'szlifierka - szlifowanie elementu',
            'sequence_no' => 4,
            'amount_required' => 0,
            'description'=> '',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'production_schema_id' => 7,
            'name' => 'jakaś maszyna - wiercenie elementu',
            'sequence_no' => 5,
            'amount_required' => 0,
            'description'=> 'Wiercenie otworów na kołki pozwalające na złożenie późniejsze złożenie całości produktu',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('task')->insert([
            'production_schema_id' => 7,
            'name' => 'stworzenie drewnianej barierki',
            'sequence_no' => 6,
            'amount_required' => 1,
            'description'=> 'tworzenie drewnianych barierek ze sformatowanych długich elementów oraz porzecznych szczebelków ',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
    }

}
