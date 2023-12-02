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
//        Schema::create('defect', function (Blueprint $table) {
//            $table->id();
//            $table->foreignId('work_id')
//                ->constrained('work')
//                ->onUpdate('cascade')
//                ->onDelete('restrict');
//            $table->foreignId('reason_code')
//                ->constrained('reason_code', 'reason_code')
//                ->onUpdate('cascade')
//                ->onDelete('restrict');
//            $table->double('amount');
//            $table->foreignId('unit_id')
//                ->constrained('unit')
//                ->onUpdate('cascade')
//                ->onDelete('restrict');
//            $table->string('additional_comment',100);
//            $table->string('created_by',30)->nullable();
//            $table->string('updated_by',30)->nullable();
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('defect');
    }
};
