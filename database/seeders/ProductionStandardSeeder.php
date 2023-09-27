<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionStandardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //łóżko domek z mdf - fronty
        DB::table('production_standard')->insert([
            'production_schema_id' => 4,
            'component_id' => 1,
            'name' => 'Łóżko domek MDF, fronty - produkt gotowy do lakierowania',
            'duration_hours' => 8,
            'amount' => 10,
            'unit_id' => 2,
            'description' => 'Produkt: Łóżko Domek - fronty. Materiał: MDF. Wykonanie zadań 1-3 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_standard')->insert([
            'production_schema_id' => 5,
            'component_id' => 1,
            'name' => 'Łóżko domek MDF, fronty - produkt polakierowany',
            'duration_hours' => 8,
            'amount' => 30,
            'unit_id' => 2,
            'description' => 'Produkt: Łóżko Domek - fronty. Materiał: MDF. Wykonanie zadania 4 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        //łóżko domek z mdf - barierka długa
        DB::table('production_standard')->insert([
            'production_schema_id' => 4,
            'component_id' => 2,
            'name' => 'Łóżko domek MDF, barierka długa - produkt gotowy do lakierowania',
            'duration_hours' => 1,
            'amount' => 10,
            'unit_id' => 1,
            'description' => 'Produkt: Łóżko Domek - barierka długa. Materiał: MDF. Wykonanie zadań 1-3 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_standard')->insert([
            'production_schema_id' => 5,
            'component_id' => 2,
            'name' => 'Łóżko domek MDF, barierka długa - produkt polakierowany',
            'duration_hours' => 1,
            'amount' => 20,
            'unit_id' => 1,
            'description' => 'Produkt: Łóżko Domek - barierka długa. Materiał: MDF. Wykonanie zadania 4 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        //łóżko domek z mdf - barierka gitara
        DB::table('production_standard')->insert([
            'production_schema_id' => 4,
            'component_id' => 3,
            'name' => 'Łóżko domek MDF, barierka gitara - produkt gotowy do lakierowania',
            'duration_hours' => 1,
            'amount' => 10,
            'unit_id' => 1,
            'description' => 'Produkt: Łóżko Domek - barierka gitara. Materiał: MDF. Wykonanie zadań 1-3 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_standard')->insert([
            'production_schema_id' => 5,
            'component_id' => 3,
            'name' => 'Łóżko domek MDF, barierka gitara - produkt polakierowany',
            'duration_hours' => 1,
            'amount' => 20,
            'unit_id' => 1,
            'description' => 'Produkt: Łóżko Domek - barierka gitara. Materiał: MDF. Wykonanie zadania 4 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        //łóżko domek z mdf - poprzeczka górna
        DB::table('production_standard')->insert([
            'production_schema_id' => 4,
            'component_id' => 4,
            'name' => 'Łóżko domek MDF, poprzeczka górna - produkt gotowy do lakierowania',
            'duration_hours' => 1,
            'amount' => 20,
            'unit_id' => 1,
            'description' => 'Produkt: Łóżko Domek - poprzeczka górna. Materiał: MDF. Wykonanie zadań 1-3 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_standard')->insert([
            'production_schema_id' => 5,
            'component_id' => 4,
            'name' => 'Łóżko domek MDF, poprzeczka górna - produkt polakierowany',
            'duration_hours' => 1,
            'amount' => 40,
            'unit_id' => 1,
            'description' => 'Produkt: Łóżko Domek - poprzeczka górna. Materiał: MDF. Wykonanie zadania 4 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        //łóżko domek z mdf - poprzeczki boczne
        DB::table('production_standard')->insert([
            'production_schema_id' => 4,
            'component_id' => 5,
            'name' => 'Łóżko domek MDF, poprzeczki boczne - produkt gotowy do lakierowania',
            'duration_hours' => 1,
            'amount' => 15,
            'unit_id' => 2,
            'description' => 'Produkt: Łóżko Domek - poprzeczki boczne. Materiał: MDF. Wykonanie zadań 1-3 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_standard')->insert([
            'production_schema_id' => 5,
            'component_id' => 5,
            'name' => 'Łóżko domek MDF, poprzeczki boczne - produkt polakierowany',
            'duration_hours' => 1,
            'amount' => 30,
            'unit_id' => 2,
            'description' => 'Produkt: Łóżko Domek - poprzeczki boczne. Materiał: MDF. Wykonanie zadania 4 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        //łóżko domek z drewna - nogi
        DB::table('production_standard')->insert([
            'production_schema_id' => 2,
            'component_id' => 6,
            'name' => 'Łóżko domek drewno, nogi - produkt gotowy do lakierowania',
            'duration_hours' => 8,
            'amount' => 20,
            'unit_id' => 2,
            'description' => 'Produkt: Łóżko Domek - nogi. Materiał: drewno. Wykonanie zadań 1-5 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_standard')->insert([
            'production_schema_id' => 3,
            'component_id' => 6,
            'name' => 'Łóżko domek drewno, nogi - produkt polakierowany',
            'duration_hours' => 8,
            'amount' => 30,
            'unit_id' => 2,
            'description' => 'Produkt: Łóżko Domek - nogi. Materiał: drewno. Wykonanie zadań 6-8 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        //łóżko domek z drewna - barierka krótka
        DB::table('production_standard')->insert([
            'production_schema_id' => 7,
            'component_id' => 7,
            'name' => 'Łóżko Domek - barierka krótka, drewno - produkt gotowy do lakierowania',
            'duration_hours' => 8,
            'amount' => 10,
            'unit_id' => 2,
            'description' => 'Produkt: Łóżko Domek - barierka krótka. Materiał: drewno. Wykonanie zadań 1-6 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_standard')->insert([
            'production_schema_id' => 3,
            'component_id' => 7,
            'name' => 'Łóżko Domek - barierka krótka, drewno - produkt polakierowany',
            'duration_hours' => 8,
            'amount' => 20,
            'unit_id' => 2,
            'description' => 'Produkt: Łóżko Domek - barierka krótka. Materiał: drewno. Wykonanie zadań 7-9 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        //łóżko domek z drewna - barierka długa
        DB::table('production_standard')->insert([
            'production_schema_id' => 7,
            'component_id' => 8,
            'name' => 'Łóżko Domek - barierka długa, drewno - produkt gotowy do lakierowania',
            'duration_hours' => 8,
            'amount' => 15,
            'unit_id' => 1,
            'description' => 'Produkt: Łóżko Domek - barierka długa. Materiał: drewno. Wykonanie zadań 1-6 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_standard')->insert([
            'production_schema_id' => 3,
            'component_id' => 8,
            'name' => 'Łóżko Domek - barierka długa, drewno - produkt polakierowany',
            'duration_hours' => 8,
            'amount' => 35,
            'unit_id' => 1,
            'description' => 'Produkt: Łóżko Domek - barierka długa. Materiał: drewno. Wykonanie zadań 7-9 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        //łóżko domek z drewna - barierka gitara
        DB::table('production_standard')->insert([
            'production_schema_id' => 7,
            'component_id' => 9,
            'name' => 'Łóżko Domek - barierka gitara, drewno - produkt gotowy do lakierowania',
            'duration_hours' => 8,
            'amount' => 15,
            'unit_id' => 1,
            'description' => 'Produkt: Łóżko Domek - barierka gitara. Materiał: drewno. Wykonanie zadań 1-6 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_standard')->insert([
            'production_schema_id' => 3,
            'component_id' => 9,
            'name' => 'Łóżko Domek - barierka gitara, drewno - produkt polakierowany',
            'duration_hours' => 8,
            'amount' => 35,
            'unit_id' => 1,
            'description' => 'Produkt: Łóżko Domek - barierka gitara. Materiał: drewno. Wykonanie zadań 7-9 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        //łóżko domek z drewna - poprzeczka górna
        DB::table('production_standard')->insert([
            'production_schema_id' => 2,
            'component_id' => 10,
            'name' => 'Łóżko Domek - poprzeczka górna, drewno - produkt gotowy do lakierowania',
            'duration_hours' => 8,
            'amount' => 40,
            'unit_id' => 1,
            'description' => 'Produkt: Łóżko Domek - poprzeczka górna. Materiał: drewno. Wykonanie zadań 1-6 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_standard')->insert([
            'production_schema_id' => 3,
            'component_id' => 10,
            'name' => 'Łóżko Domek - poprzeczka górna, drewno - produkt polakierowany',
            'duration_hours' => 8,
            'amount' => 80,
            'unit_id' => 1,
            'description' => 'Produkt: Łóżko Domek - poprzeczka górna. Materiał: drewno. Wykonanie zadań 7-9 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        //łóżko domek z drewna - poprzeczki boczne
        DB::table('production_standard')->insert([
            'production_schema_id' => 2,
            'component_id' => 11,
            'name' => 'Łóżko Domek - poprzeczki boczne, drewno - produkt gotowy do lakierowania',
            'duration_hours' => 8,
            'amount' => 25,
            'unit_id' => 2,
            'description' => 'Produkt: Łóżko Domek - poprzeczki boczne. Materiał: drewno. Wykonanie zadań 1-6 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_standard')->insert([
            'production_schema_id' => 3,
            'component_id' => 11,
            'name' => 'Łóżko Domek - poprzeczki boczne, drewno - produkt polakierowany',
            'duration_hours' => 8,
            'amount' => 40,
            'unit_id' => 2,
            'description' => 'Produkt: Łóżko Domek - poprzeczki boczne. Materiał: drewno. Wykonanie zadań 7-9 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        //stelaż
        DB::table('production_standard')->insert([
            'production_schema_id' => 6,
            'component_id' => 12,
            'name' => 'stelaż - produkt gotowy do lakierowania',
            'duration_hours' => 8,
            'amount' => 30,
            'unit_id' => 2,
            'description' => 'Produkt: stelaż. Materiał: drewno. Wykonanie zadań 1-3 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_standard')->insert([
            'production_schema_id' => 5,
            'component_id' => 12,
            'name' => 'stelaż, drewno - produkt polakierowany',
            'duration_hours' => 8,
            'amount' => 30,
            'unit_id' => 2,
            'description' => 'stelaż. Materiał: drewno. Wykonanie zadaia 4 z cyklu produkcyjnego produktu.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
    }
}
