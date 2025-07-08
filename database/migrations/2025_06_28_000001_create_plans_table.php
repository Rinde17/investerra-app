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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price_monthly', 8, 2);
            $table->string('stripe_price_id')->nullable();
            $table->string('description')->nullable();
            $table->integer('analyses_per_week')->default(5);
            $table->boolean('pdf_pro')->default(false);
            $table->boolean('comparator')->default(false);
            $table->boolean('pdf_expert')->default(false);
            $table->boolean('fiscal_analysis')->default(false);
            $table->boolean('custom_alerts')->default(false);
            $table->boolean('priority_support')->default(false);
            $table->boolean('dedicated_account_manager')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
