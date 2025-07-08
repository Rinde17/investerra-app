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
        Schema::table('terrain_analyses', function (Blueprint $table) {
            $table->decimal('price_difference_percentage', 8, 2)->nullable()->after('market_price_m2');
            $table->decimal('profit_margin_percentage', 8, 2)->nullable()->after('net_margin_estimate');
            $table->string('overall_risk')->nullable()->after('profitability_label');
            $table->string('overall_recommendation')->nullable()->after('overall_risk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terrain_analyses', function (Blueprint $table) {
            $table->dropColumn('price_difference_percentage');
            $table->dropColumn('profit_margin_percentage');
            $table->dropColumn('overall_risk');
            $table->dropColumn('overall_recommendation');
        });
    }
};
