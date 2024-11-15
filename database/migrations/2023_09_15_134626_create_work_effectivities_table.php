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
        Schema::create('work_effectivity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_cycle_schema_id')
                ->constrained('production_cycle')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('production_standard_id')
                ->nullable()
                ->constrained('production_standard')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->double('productivity')->default(0);
            $table->integer('duration_minute')->default(0);
            $table->integer('amount')->default(0);
            $table->boolean('finished')->default(false);
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
        Schema::dropIfExists('work_effectivity');
    }
};
