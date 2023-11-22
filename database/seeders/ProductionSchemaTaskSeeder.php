<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductionSchemaTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 1,
            'task_id' => 9,
            'sequence_no' => 1,
            'amount_required' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 2,
            'task_id' => 1,
            'sequence_no' => 1,
            'amount_required' => 0,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 2,
            'task_id' => 2,
            'sequence_no' => 2,
            'amount_required' => 0,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 2,
            'task_id' => 3,
            'sequence_no' => 3,
            'amount_required' => 0,
            'additional_description' => 'Od tego etapu znana jest liczba produkowanych kompletów i może być podawana.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 2,
            'task_id' => 4,
            'sequence_no' => 4,
            'amount_required' => 0,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 2,
            'task_id' => 5,
            'sequence_no' => 5,
            'amount_required' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 3,
            'task_id' => 6,
            'sequence_no' => 1,
            'amount_required' => 0,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 3,
            'task_id' => 8,
            'sequence_no' => 2,
            'amount_required' => 0,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 3,
            'task_id' => 7,
            'sequence_no' => 3,
            'amount_required' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 4,
            'task_id' => 10,
            'sequence_no' => 1,
            'amount_required' => 0,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 4,
            'task_id' => 11,
            'sequence_no' => 2,
            'amount_required' => 0,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 4,
            'task_id' => 5,
            'sequence_no' => 3,
            'amount_required' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 5,
            'task_id' => 6,
            'sequence_no' => 1,
            'amount_required' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 6,
            'task_id' => 1,
            'sequence_no' => 1,
            'amount_required' => 0,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 6,
            'task_id' => 3,
            'sequence_no' => 2,
            'amount_required' => 0,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 6,
            'task_id' => 5,
            'sequence_no' => 3,
            'amount_required' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);

        DB::table('production_schema_task')->insert([
            'production_schema_id' => 7,
            'task_id' => 1,
            'sequence_no' => 1,
            'amount_required' => 0,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 7,
            'task_id' => 2,
            'sequence_no' => 2,
            'amount_required' => 0,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 7,
            'task_id' => 3,
            'sequence_no' => 3,
            'amount_required' => 0,
            'additional_description' => 'Od tego etapu znana jest liczba produkowanych kompletów i może być podawana.',
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 7,
            'task_id' => 4,
            'sequence_no' => 4,
            'amount_required' => 0,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 7,
            'task_id' => 5,
            'sequence_no' => 5,
            'amount_required' => 0,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
        DB::table('production_schema_task')->insert([
            'production_schema_id' => 7,
            'task_id' => 12,
            'sequence_no' => 6,
            'amount_required' => 1,
            'created_by' => 'system',
            'updated_by' => 'system',
            'created_at' => date('y-m-d h:i:s'),
            'updated_at' => date('y-m-d h:i:s'),
        ]);
    }
}
