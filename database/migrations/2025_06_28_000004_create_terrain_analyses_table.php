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
        Schema::create('terrain_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terrain_id')->constrained()->onDelete('cascade');
            $table->decimal('price_m2', 10, 2)->nullable();
            $table->decimal('market_price_m2', 10, 2)->nullable();
            $table->decimal('viability_cost', 12, 2)->nullable();
            $table->integer('lots_possible')->nullable();
            $table->decimal('resale_estimate_min', 12, 2)->nullable();
            $table->decimal('resale_estimate_max', 12, 2)->nullable();
            $table->decimal('net_margin_estimate', 12, 2)->nullable();
            $table->decimal('ai_score', 5, 2)->nullable();
            $table->string('profitability_label')->nullable();
            $table->json('analysis_details')->nullable();
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terrain_analyses');
    }
};
