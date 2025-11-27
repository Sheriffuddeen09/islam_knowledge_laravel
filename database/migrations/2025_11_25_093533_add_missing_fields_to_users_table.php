<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name');
            }
            if (!Schema::hasColumn('users', 'dob')) {
                $table->date('dob');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->unique();
            }
            if (!Schema::hasColumn('users', 'phone_country_code')) {
                $table->string('phone_country_code');
            }
            if (!Schema::hasColumn('users', 'location')) {
                $table->string('location');
            }
            if (!Schema::hasColumn('users', 'location_country_code')) {
                $table->string('location_country_code');
            }
            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male','female','other']);
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('student');
            }
        });

        // Create new tables
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'dob', 'phone',
                'phone_country_code', 'location', 'location_country_code', 'gender', 'role'
            ]);
        });

        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
}
