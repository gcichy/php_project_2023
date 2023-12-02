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
        Schema::create('product_component', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->constrained('product')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('component_id')
                ->constrained('component')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->integer('amount_per_product')->default(1);
            $table->string('description')->nullable();
            $table->string('created_by',30)->nullable();
            $table->string('updated_by',30)->nullable();
            $table->timestamps();
            $table->primary(['product_id','component_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_component');
    }
};
