<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Horodatage du dernier e-mail de rappel de paiement (pour statut 'past_due')
            $table->timestamp('last_payment_reminder_email_sent_at')->nullable()->after('last_plan_swapped_email_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('last_payment_reminder_email_sent_at');
        });
    }
};
