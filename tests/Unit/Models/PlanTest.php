<?php

use App\Models\Plan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('plan has users relationship', function () {
    $plan = createPlan();
    $user = createUser(['plan_id' => $plan->id]);

    expect($plan->users)->toBeCollection()
        ->and($plan->users->first())->toBeInstanceOf(User::class)
        ->and($plan->users->first()->id)->toBe($user->id);
});

test('plan casts price_monthly to decimal', function () {
    $plan = createPlan(['price_monthly' => 19.99]);

    expect((float) $plan->price_monthly)->toBe(19.99)
        ->and(is_float((float) $plan->price_monthly))->toBeTrue();
});

test('plan casts pdf_pro to boolean', function () {
    $plan = createPlan(['pdf_pro' => true]);

    expect($plan->pdf_pro)->toBeTrue()
        ->and($plan->pdf_pro)->toBeBool();
});

test('plan casts comparator to boolean', function () {
    $plan = createPlan(['comparator' => true]);

    expect($plan->comparator)->toBeTrue()
        ->and($plan->comparator)->toBeBool();
});

test('plan casts pdf_expert to boolean', function () {
    $plan = createPlan(['pdf_expert' => true]);

    expect($plan->pdf_expert)->toBeTrue()
        ->and($plan->pdf_expert)->toBeBool();
});

test('plan casts fiscal_analysis to boolean', function () {
    $plan = createPlan(['fiscal_analysis' => true]);

    expect($plan->fiscal_analysis)->toBeTrue()
        ->and($plan->fiscal_analysis)->toBeBool();
});

test('plan casts custom_alerts to boolean', function () {
    $plan = createPlan(['custom_alerts' => true]);

    expect($plan->custom_alerts)->toBeTrue()
        ->and($plan->custom_alerts)->toBeBool();
});

test('plan casts priority_support to boolean', function () {
    $plan = createPlan(['priority_support' => true]);

    expect($plan->priority_support)->toBeTrue()
        ->and($plan->priority_support)->toBeBool();
});

test('plan casts dedicated_account_manager to boolean', function () {
    $plan = createPlan(['dedicated_account_manager' => true]);

    expect($plan->dedicated_account_manager)->toBeTrue()
        ->and($plan->dedicated_account_manager)->toBeBool();
});
