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
        Schema::create('instruction', function (Blueprint $table) {
            $table->id();
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
            $table->foreignId('production_schema_id')
                ->nullable()
                ->constrained('production_schema')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('name');
            $table->string('instruction_pdf')->nullable();
            $table->string('video')->nullable();
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
        Schema::dropIfExists('instruction');
    }
};
