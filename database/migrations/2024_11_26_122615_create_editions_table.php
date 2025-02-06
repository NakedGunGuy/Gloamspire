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
        Schema::disableForeignKeyConstraints();

        Schema::create('editions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->foreignId('card_id');
            $table->string('card_uuid');
            $table->string('collector_number');
            $table->string('slug');
            $table->string('flavor')->nullable();
            $table->string('illustrator')->nullable();
            $table->string('rarity')->nullable();
            $table->foreignId('set_id')->constrained('sets')->onDelete('cascade');
            $table->timestamp('last_update')->nullable();
            $table->timestamps();

            $table->foreign('set_id')->references('id')->on('sets');
            $table->foreign('card_id')->references('id')->on('cards');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editions');
    }
};
