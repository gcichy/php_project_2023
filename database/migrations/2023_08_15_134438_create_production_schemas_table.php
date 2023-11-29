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
            $table->string('production_schema');
            $table->string('description')->nullable();
            $table->integer('tasks_count');
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('production_schema')
                ->onUpdate('cascade')
                ->onDelete('restrict');
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
        Schema::dropIfExists('production_schema');
    }
};
