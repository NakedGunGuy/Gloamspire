<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('element');
            $table->string('name');
            $table->string('slug');
            $table->longText('effect')->nullable();
            $table->longText('effect_raw')->nullable();
            $table->string('flavor')->nullable();
            $table->string('cost_memory')->nullable();
            $table->string('cost_reserve')->nullable();
            $table->string('level')->nullable();
            $table->string('power')->nullable();
            $table->string('life')->nullable();
            $table->string('durability')->nullable();
            $table->string('speed')->nullable();
            $table->timestamp('last_update')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
