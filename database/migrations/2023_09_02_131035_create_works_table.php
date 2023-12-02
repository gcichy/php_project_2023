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
        Schema::create('work', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_cycle_id')
                ->nullable()
                ->constrained('production_cycle')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('task_id')
                ->constrained('task')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('production_schema_id')
                ->constrained('production_schema')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('component_id')
                ->nullable()
                ->constrained('component')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('product')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->unsignedBigInteger('duration_minute');
            $table->double('amount')->nullable()->default(0);
            $table->string('additional_comment',200)->nullable();
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
        Schema::dropIfExists('work');
    }
};
