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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('property_id');
            $table->string('book_image');
            $table->string('book_name');
            $table->year('publication_date');
            $table->text('book_details');
            $table->foreignId('author_id')->constrained('authors')->cascadeOnDelete()->cascadeOnUpdate();

            $table->integer('rating')->default(0);
            $table->unsignedInteger('available_copies')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
