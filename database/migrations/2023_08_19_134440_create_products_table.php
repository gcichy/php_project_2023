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
        Schema::create('product', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('gtin', 14)->nullable();
            $table->string('description')->nullable();
            $table->string('material', 30)->nullable();
            $table->string('color', 30)->nullable();
            $table->string('image')->nullable();
            $table->string('barcode_image')->nullable();
            $table->double('price')->nullable();
            $table->double('piecework_fee')->nullable();
            $table->string('created_by', 30)->nullable();
            $table->string('updated_by', 30)->nullable();
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
        Schema::dropIfExists('product');
    }
};
