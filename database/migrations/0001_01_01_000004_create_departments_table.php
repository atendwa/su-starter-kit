<?php

use Atendwa\SuStarterKit\Models\Department;
use Atendwa\SuStarterKit\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class, 'head_user_id')->index();
            $table->foreignIdFor(Department::class, 'parent_department_id')->nullable()->index();
            $table->string('name', 100)->unique();
            $table->string('code', 50)->unique()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
