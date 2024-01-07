<?php

namespace Database\Seeders;

use App\Models\ProductComponent;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersSeeder::class);
        $this->call(StaticValueSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(ReasonCodeSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(ComponentSeeder::class);
        $this->call(ProductComponentSeeder::class);
        $this->call(ProductionSchemaSeeder::class);
        $this->call(ComponentProductionSchemaSeeder::class);
        $this->call(TaskSeeder::class);
        $this->call(InstructionSeeder::class);
        $this->call(ProductionStandardSeeder::class);
    }
}
