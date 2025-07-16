<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->timestamp('last_subscription_created_email_sent_at')->nullable()->after('ends_at');
            $table->timestamp('last_plan_swapped_email_sent_at')->nullable()->after('last_subscription_created_email_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('last_subscription_created_email_sent_at');
            $table->dropColumn('last_plan_swapped_email_sent_at');
        });
    }
};
