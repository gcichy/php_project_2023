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
        Schema::create('production_cycle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_schema_id')
                ->constrained('production_schema')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('product')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('component_id')
                ->nullable()
                ->constrained('component')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('production_cycle')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->dateTime('expected_start_time');
            $table->dateTime('expected_end_time');
            $table->unsignedBigInteger('duration_minute_sum');
            $table->double('total_amount');
            $table->double('current_amount');
            $table->double('defect_amount');
            $table->boolean('settled');
            $table->string('additional_comment',200)->nullable();
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
        Schema::dropIfExists('production_cycle');
    }
};
