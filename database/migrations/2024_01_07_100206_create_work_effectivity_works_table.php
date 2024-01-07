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
        Schema::create('work_effectivity_work', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_id')
                ->constrained('work')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('work_effectivity_id')
                ->nullable()
                ->constrained('work_effectivity')
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
        Schema::dropIfExists('work_effectivity_work');
    }
};
