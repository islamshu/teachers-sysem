<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('owner_name');
            $table->boolean('same_driver')->default(true);
            $table->string('owner_phone')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->string('license');
            $table->string('van_color');
            $table->integer('seats_count');
            $table->string('location');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_contracts');
    }
};
