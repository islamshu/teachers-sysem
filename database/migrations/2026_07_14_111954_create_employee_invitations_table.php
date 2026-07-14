<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->string('status')->default('invited');
            $table->timestamp('hired_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_invitations');
    }
};
