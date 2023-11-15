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
            $table->string('name', 50);
            $table->string('gtin', 14)->nullable();
            $table->string('description',255)->nullable();
            $table->string('material', 30)->nullable();
            $table->string('color', 30)->nullable();
            $table->string('image', 50)->nullable();
            $table->string('barcode_image', 50)->nullable();
            $table->float('price')->nullable();
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
