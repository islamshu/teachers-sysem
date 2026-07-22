<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_task_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('general_task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('general_task_attachment_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->timestamps();
            $table->unique(['general_task_id', 'user_id', 'general_task_attachment_id'], 'gtc_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_task_completions');
    }
};
