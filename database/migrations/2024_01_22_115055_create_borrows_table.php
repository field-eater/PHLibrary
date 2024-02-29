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
        Schema::create('borrows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete()->nullable();
            $table->foreignId('book_id')->constrained('books')->cascadeOnUpdate()->cascadeOnDelete()->nullable();
            $table->foreignId('book_copy_id')->constrained('book_copies')->cascadeOnUpdate()->cascadeOnDelete()->nullable();
            $table->date('date_borrowed');
            $table->date('estimated_return_date')->nullable();
            $table->date('date_returned')->nullable();
            $table->string('return_status')->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrows');
    }
};
