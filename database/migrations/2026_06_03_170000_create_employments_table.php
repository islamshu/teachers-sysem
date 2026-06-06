<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teacher_profiles')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->text('message')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('status')->default('invited'); // invited, accepted, interviewed, hired, rejected
            $table->timestamp('hired_at')->nullable();
            $table->timestamps();
        });

        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->string('employment_status')->default('available')->after('status'); // available, employed, unavailable
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employments');
        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->dropColumn('employment_status');
        });
    }
};
