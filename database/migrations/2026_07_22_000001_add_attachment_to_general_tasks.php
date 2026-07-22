<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('general_tasks', function (Blueprint $table) {
            $table->string('attachment_path')->nullable()->after('created_by');
            $table->boolean('attachment_required')->default(false)->after('attachment_path');
        });

        Schema::table('general_task_logs', function (Blueprint $table) {
            $table->string('completion_attachment')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('general_tasks', function (Blueprint $table) {
            $table->dropColumn(['attachment_path', 'attachment_required']);
        });

        Schema::table('general_task_logs', function (Blueprint $table) {
            $table->dropColumn('completion_attachment');
        });
    }
};
