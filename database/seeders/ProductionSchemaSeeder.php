<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionSchemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('production_schema')->insert([
            'production_schema' => 'Pakowanie produktu',
            'description' => '',
            'tasks_count' => 1,
            'waste_unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema')->insert([
            'production_schema' => 'Obróbka drewna bez łączenia elementów',
            'description' => 'Od deski do niepolakierowanego elementu. Używany przy tworzeniu drewnianych nóg, poprzeczek itp.',
            'tasks_count' => 5,
            'waste_unit_id' => 6,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema')->insert([
            'production_schema' => 'Lakierowanie elementu - 2 warstwy + szlifowanie',
            'description' => 'Wszystkie etapy wytwarzania od pierwszego lakierowania do elementu gotowego na pakowanie.',
            'tasks_count' => 3,
            'waste_unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema')->insert([
            'production_schema' => 'Obróbka płyty MDF',
            'description' => 'Od surowej płyty do niepolakierowanego elementu. Używany przy tworzeniu barierek, nóg, poprzeczek itp. z płyty MDF.',
            'tasks_count' => 3,
            'waste_unit_id' => 5,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema')->insert([
            'production_schema' => 'Lakierowanie elementu - 1 warstwa',
            'description' => 'Lakierowanie elementu, po którym jest gotowy na pakowanie.',
            'tasks_count' => 1,
            'waste_unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema')->insert([
            'production_schema' => 'Obróbka drewna - stelaż',
            'description' => 'Od deski do niepolakierowanego elementu z pominięciem frezarki 4-stronnej i szlifierki. Używany przy tworzeniu stelaży.',
            'tasks_count' => 3,
            'waste_unit_id' => 6,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema')->insert([
            'production_schema' => 'Obróbka drewna z łączeniem elementów',
            'description' => 'Od deski do niepolakierowanego elementu, który na został utworzony przez połączenie długich elementów i poprzecznych szczebelków. Używany przy tworzeniu drewnianych barierek.',
            'tasks_count' => 6,
            'waste_unit_id' => 6,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
    }
}
