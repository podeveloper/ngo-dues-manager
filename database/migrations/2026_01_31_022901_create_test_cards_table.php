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
        Schema::create('test_cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_number')->unique();
            $table->string('bank_name')->nullable(); // Akbank, Garanti
            $table->string('scheme')->nullable();    // Visa, Master, Troy
            $table->string('type')->nullable();      // Credit, Debit
            $table->boolean('should_succeed')->default(true);
            $table->string('error_code')->nullable();
            $table->string('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_cards');
    }
};
