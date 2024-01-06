<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_schema', function (Blueprint $table) {
            $table->id();
            $table->string('production_schema',100);
            $table->string('description')->nullable();
            $table->integer('tasks_count');
            $table->boolean('non_countable')->default(0);
            $table->foreignId('waste_unit_id')
                ->nullable()
                ->constrained('unit')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('created_by',30)->nullable();
            $table->string('updated_by',30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_schema');
    }
};
