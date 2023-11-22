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
        Schema::create('production_schema_task', function (Blueprint $table) {
            $table->foreignId('production_schema_id')
                ->constrained('production_schema')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('task_id')
                ->constrained('task')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->unsignedSmallInteger('sequence_no');
            $table->boolean('amount_required');
            $table->string('additional_description')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
            $table->primary(['production_schema_id', 'task_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_schema_task');
    }
};
