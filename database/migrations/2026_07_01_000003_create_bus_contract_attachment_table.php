<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_contract_attachment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contract_attachment_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_contract_attachment');
    }
};
