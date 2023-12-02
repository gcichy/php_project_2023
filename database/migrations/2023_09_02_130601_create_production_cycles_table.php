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
            $table->smallInteger('level');
            $table->foreignId('production_schema_id')
                ->nullable()
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
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->dateTime('expected_start_time')->nullable();
            $table->dateTime('expected_end_time')->nullable();
            $table->unsignedBigInteger('duration_minute_sum')->default(0);
            $table->double('total_amount')->default(1);
            $table->double('current_amount')->default(0);
            $table->double('defect_amount')->default(0);
            $table->boolean('finished')->default(false);
            $table->boolean('settled')->nullable();
            $table->string('additional_comment')->nullable();
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
