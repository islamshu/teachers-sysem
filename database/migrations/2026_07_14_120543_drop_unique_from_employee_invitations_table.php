<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('employee_interview_answers');
        Schema::dropIfExists('employee_interviews');
        Schema::dropIfExists('employee_invitations');

        Schema::create('employee_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('users')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->string('status')->default('invited');
            $table->timestamp('hired_at')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_invitation_id')->constrained()->onDelete('cascade');
            $table->foreignId('interviewer_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_score', 8, 2)->default(0);
            $table->decimal('max_score', 8, 2)->default(0);
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_interview_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_interview_id')->constrained()->onDelete('cascade');
            $table->foreignId('interview_question_id')->constrained()->onDelete('cascade');
            $table->decimal('score', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_interview_answers');
        Schema::dropIfExists('employee_interviews');
        Schema::dropIfExists('employee_invitations');
    }
};
