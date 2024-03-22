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
        //
        Schema::create('rateables', function (Blueprint $table) {

            $table->foreignId('rating_id')->constrained('ratings')->cascadeOnUpdate()->cascadeOnDelete();
            $table->morphs('rateable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rateables');

    }
};
