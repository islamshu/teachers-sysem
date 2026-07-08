<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixed_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('fixed_task_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained(config('permission.table_names.roles', 'roles'))->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['fixed_task_id', 'role_id']);
        });

        Schema::create('daily_task_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fixed_task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->timestamp('completed_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['fixed_task_id', 'user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_task_logs');
        Schema::dropIfExists('fixed_task_role');
        Schema::dropIfExists('fixed_tasks');
    }
};
