<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(){
        Schema::create('users', function (Blueprint $table){
            $table->id();
            $table->string('first_name'); // required
            $table->string('last_name');  // required
            $table->date('dob');           // required
            $table->string('phone')->unique(); // required + unique
            $table->string('phone_country_code'); // required
            $table->string('location_country_code'); // required
            $table->string('location'); // required
            $table->string('email')->unique(); // required + unique
            $table->enum('gender', ['male','female','other']); // required
            $table->string('password'); // required
            $table->string('role')->default('student');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(){
        Schema::dropIfExists('users');
    }
};