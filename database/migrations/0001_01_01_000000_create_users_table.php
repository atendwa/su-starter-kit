<?php

use Atendwa\SuStarterKit\Models\Department;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {

            $table->id();
            $table->foreignIdFor(Department::class)->nullable();

            $table->string('first_name', 50);
            $table->string('other_names', 50)->nullable();
            $table->string('last_name', 50);
            $table->string('name', 150)->index();

            $table->string('email', 100)->nullable();
            $table->string('password', 100)->nullable();

            $table->string('username', 50)->unique();
            $table->string('employee_number', 10)->unique()->nullable();
            $table->string('phone_number', 15)->nullable()->unique();
            $table->string('extension', 10)->nullable()->unique();

            $table->boolean('has_completed_initial_login')->default(false);
            $table->boolean('is_system_user')->default(false);
            $table->boolean('can_login')->default(true);
            $table->boolean('is_active')->default(true);

            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table): void {
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
