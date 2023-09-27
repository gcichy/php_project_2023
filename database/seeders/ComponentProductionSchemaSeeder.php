<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComponentProductionSchemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('component_production_schema')->insert([
            'component_id' => 1,
            'production_schema_id' => 4,
            'sequence_no' => 1,
            'unit_id' => 2,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 1,
            'production_schema_id' => 5,
            'sequence_no' => 2,
            'unit_id' => 2,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 2,
            'production_schema_id' => 4,
            'sequence_no' => 1,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 2,
            'production_schema_id' => 5,
            'sequence_no' => 2,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 3,
            'production_schema_id' => 4,
            'sequence_no' => 1,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 3,
            'production_schema_id' => 5,
            'sequence_no' => 2,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 4,
            'production_schema_id' => 4,
            'sequence_no' => 1,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 4,
            'production_schema_id' => 5,
            'sequence_no' => 2,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 5,
            'production_schema_id' => 4,
            'sequence_no' => 1,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 5,
            'production_schema_id' => 5,
            'sequence_no' => 2,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 6,
            'production_schema_id' => 2,
            'sequence_no' => 1,
            'unit_id' => 2,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 6,
            'production_schema_id' => 3,
            'sequence_no' => 2,
            'unit_id' => 2,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 7,
            'production_schema_id' => 7,
            'sequence_no' => 1,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 7,
            'production_schema_id' => 3,
            'sequence_no' => 2,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 8,
            'production_schema_id' => 7,
            'sequence_no' => 1,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 8,
            'production_schema_id' => 3,
            'sequence_no' => 2,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 9,
            'production_schema_id' => 7,
            'sequence_no' => 1,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 9,
            'production_schema_id' => 3,
            'sequence_no' => 2,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 10,
            'production_schema_id' => 2,
            'sequence_no' => 1,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 10,
            'production_schema_id' => 3,
            'sequence_no' => 2,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 11,
            'production_schema_id' => 2,
            'sequence_no' => 1,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 11,
            'production_schema_id' => 3,
            'sequence_no' => 2,
            'unit_id' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 12,
            'production_schema_id' => 6,
            'sequence_no' => 1,
            'unit_id' => 2,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('component_production_schema')->insert([
            'component_id' => 12,
            'production_schema_id' => 5,
            'sequence_no' => 2,
            'unit_id' => 2,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
    }
}
