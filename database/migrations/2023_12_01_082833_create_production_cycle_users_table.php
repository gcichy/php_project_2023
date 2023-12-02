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
        Schema::create('production_cycle_user', function (Blueprint $table) {
            $table->foreignId('production_cycle_id')
                ->constrained('production_cycle')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');
            $table->string('created_by',30)->nullable();
            $table->string('updated_by',30)->nullable();
            $table->timestamps();
            $table->primary(['production_cycle_id','user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_cycle_user');
    }
};
