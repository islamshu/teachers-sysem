<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->enum('status', ['pending', 'completed', 'approved', 'rejected'])->default('pending')->change();
            $table->text('rejection_reason')->nullable()->after('status');
            $table->timestamp('approved_at')->nullable()->after('rejection_reason');
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->enum('status', ['pending', 'completed'])->default('pending')->change();
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['rejection_reason', 'approved_at', 'approved_by']);
        });
    }
};
