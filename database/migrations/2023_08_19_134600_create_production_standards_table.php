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
        Schema::create('production_standard', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_schema_id')
                ->constrained('production_schema')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('component_id')
                ->nullable()
                ->constrained('component')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('name');
            $table->integer('duration_hours');
            $table->double('amount');
            $table->foreignId('unit_id')
                ->constrained('unit')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('description')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
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
        Schema::dropIfExists('production_standard');
    }
};