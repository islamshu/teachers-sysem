<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('general_task_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('general_task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['general_task_id', 'user_id']);
        });

        Schema::create('general_task_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('general_task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained(config('permission.table_names.roles', 'roles'))->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['general_task_id', 'role_id']);
        });

        Schema::create('general_task_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('general_task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('completed_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['general_task_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_task_logs');
        Schema::dropIfExists('general_task_role');
        Schema::dropIfExists('general_task_user');
        Schema::dropIfExists('general_tasks');
    }
};
