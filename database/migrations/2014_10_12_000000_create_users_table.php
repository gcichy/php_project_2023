<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('firstName');
            $table->string('lastName');
            $table->string('employeeNo');
            $table->string('role')->default('employee');
            $table->string('phoneNr')->nullable();
            $table->string('email')->unique();
            $table->boolean('is_verified')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->double('salary')->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
