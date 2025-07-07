<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the Freemium plan
        Plan::create([
            'name' => 'Freemium',
            'price_monthly' => 0.00,
            'features' => [
                '5 analyses per week',
                'Basic PDF export',
                'Access to basic features',
            ],
            'stripe_price_id' => 'price_1RfmYN2eRAxJv0GUQijhod6g',
            'description' => 'Perfect for beginners who want to explore the platform.',
            'analyses_per_week' => 5,
            'pdf_pro' => false,
            'comparator' => false,
            'pdf_expert' => false,
            'fiscal_analysis' => false,
            'custom_alerts' => false,
        ]);

        // Create the Pro plan
        Plan::create([
            'name' => 'Pro',
            'price_monthly' => 29.99,
            'features' => [
                'Unlimited analyses',
                'Pro PDF export',
                'Terrain comparator',
                'Priority support',
            ],
            'stripe_price_id' => 'price_1RfmcB2eRAxJv0GUZtwaLftc', // This would be replaced with an actual Stripe price ID
            'description' => 'For serious investors who need more advanced features.',
            'analyses_per_week' => 0, // Unlimited
            'pdf_pro' => true,
            'comparator' => true,
            'pdf_expert' => false,
            'fiscal_analysis' => false,
            'custom_alerts' => false,
        ]);

        // Create the Investor plan
        Plan::create([
            'name' => 'Investor',
            'price_monthly' => 99.99,
            'features' => [
                'Unlimited analyses',
                'Expert PDF export',
                'Terrain comparator',
                'Fiscal analysis',
                'Custom alerts',
                'Priority support',
                'Dedicated account manager',
            ],
            'stripe_price_id' => 'price_1Rfmg22eRAxJv0GUPRsNhP9m', // This would be replaced with an actual Stripe price ID
            'description' => 'For professional investors who need the most advanced features.',
            'analyses_per_week' => 0, // Unlimited
            'pdf_pro' => true,
            'comparator' => true,
            'pdf_expert' => true,
            'fiscal_analysis' => true,
            'custom_alerts' => true,
        ]);
    }
}
