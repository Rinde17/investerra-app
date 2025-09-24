<?php

use App\Mail\SubscriptionCancelled;
use App\Mail\SubscriptionConfirmed;
use App\Mail\SubscriptionResumed;
use App\Mail\SubscriptionSwapped;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Cashier\Subscription;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

test('subscriptionCheckout redirects to Stripe checkout', function () {
    $user = createUser();
    $plan = createPlan(['stripe_price_id' => 'price_123']);

    // Mock the user's createAsStripeCustomer method
    $user->shouldReceive('createAsStripeCustomer')
        ->once()
        ->andReturn(true);

    // Mock the newSubscription method chain
    $subscriptionBuilder = Mockery::mock();
    $subscriptionBuilder->shouldReceive('allowPromotionCodes')
        ->once()
        ->andReturn($subscriptionBuilder);

    $subscriptionBuilder->shouldReceive('checkout')
        ->once()
        ->with([
            'success_url' => route('checkout-success'),
            'cancel_url' => route('checkout-cancel'),
        ])
        ->andReturn(redirect('https://stripe.com/checkout'));

    $user->shouldReceive('newSubscription')
        ->once()
        ->with('default', 'price_123')
        ->andReturn($subscriptionBuilder);

    $this->actingAs($user)
        ->get(route('subscription.checkout', ['stripePriceId' => 'price_123']))
        ->assertRedirect('https://stripe.com/checkout');
});

test('billingPortal redirects to Stripe billing portal', function () {
    $user = createUser();

    // Mock the redirectToBillingPortal method
    $user->shouldReceive('redirectToBillingPortal')
        ->once()
        ->andReturn(redirect('https://stripe.com/billing'));

    $this->actingAs($user)
        ->get(route('billing-portal'))
        ->assertRedirect('https://stripe.com/billing');
});

test('resumeSubscription resumes a cancelled subscription', function () {
    Mail::fake();

    $user = createUser();

    // Create a mock subscription
    $subscription = Mockery::mock(Subscription::class);
    $subscription->shouldReceive('onGracePeriod')
        ->once()
        ->andReturn(true);

    $subscription->shouldReceive('resume')
        ->once()
        ->andReturn(true);

    // Mock the user's subscription method
    $user->shouldReceive('subscription')
        ->once()
        ->with('default')
        ->andReturn($subscription);

    $this->actingAs($user)
        ->post(route('subscription.resume'))
        ->assertRedirect()
        ->assertSessionHas('success');

    Mail::assertSent(SubscriptionResumed::class, function ($mail) use ($user, $subscription) {
        return $mail->hasTo($user->email) &&
               $mail->user->is($user) &&
               $mail->subscription === $subscription;
    });
});

test('resumeSubscription handles errors', function () {
    $user = createUser();

    // Create a mock subscription that throws an exception
    $subscription = Mockery::mock(Subscription::class);
    $subscription->shouldReceive('onGracePeriod')
        ->once()
        ->andReturn(true);

    $subscription->shouldReceive('resume')
        ->once()
        ->andThrow(new \Exception('Resume error'));

    // Mock the user's subscription method
    $user->shouldReceive('subscription')
        ->once()
        ->with('default')
        ->andReturn($subscription);

    $this->actingAs($user)
        ->post(route('subscription.resume'))
        ->assertRedirect()
        ->assertSessionHas('error');
});

test('resumeSubscription redirects when subscription is not on grace period', function () {
    $user = createUser();

    // Create a mock subscription
    $subscription = Mockery::mock(Subscription::class);
    $subscription->shouldReceive('onGracePeriod')
        ->once()
        ->andReturn(false);

    // Mock the user's subscription method
    $user->shouldReceive('subscription')
        ->once()
        ->with('default')
        ->andReturn($subscription);

    $this->actingAs($user)
        ->post(route('subscription.resume'))
        ->assertRedirect(route('pricing.index'))
        ->assertSessionHas('error');
});

test('showSubscriptionSettings displays subscription settings', function () {
    $user = createUser();
    $plan = createPlan(['stripe_price_id' => 'price_123']);

    // Create a mock subscription
    $subscription = Mockery::mock();
    $subscription->id = 'sub_123';
    $subscription->stripe_status = 'active';
    $subscription->stripe_price = 'price_123';
    $subscription->ends_at = null;

    $subscription->shouldReceive('onGracePeriod')
        ->once()
        ->andReturn(false);

    // Mock the user's subscription method
    $user->shouldReceive('subscription')
        ->once()
        ->andReturn($subscription);

    // Mock the user's hasStripeId method
    $user->shouldReceive('hasStripeId')
        ->once()
        ->andReturn(true);

    // Mock the user's invoicesIncludingPending method
    $user->shouldReceive('invoicesIncludingPending')
        ->once()
        ->andReturn(collect([]));

    $this->actingAs($user)
        ->get(route('settings.subscription'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/SubscriptionSettings')
            ->has('subscription')
            ->has('invoices')
            ->has('plans')
        );
});

test('cancelSubscription cancels a subscription', function () {
    Mail::fake();

    $user = createUser();

    // Create a mock subscription
    $subscription = Mockery::mock(Subscription::class);
    $subscription->shouldReceive('cancel')
        ->once()
        ->andReturn(true);

    // Mock the user's subscription method
    $user->shouldReceive('subscription')
        ->once()
        ->with('default')
        ->andReturn($subscription);

    $this->actingAs($user)
        ->delete(route('subscription.cancel'))
        ->assertRedirect()
        ->assertSessionHas('success');

    Mail::assertSent(SubscriptionCancelled::class, function ($mail) use ($user, $subscription) {
        return $mail->hasTo($user->email) &&
               $mail->user->is($user) &&
               $mail->subscription === $subscription;
    });
});

test('cancelSubscription handles errors', function () {
    $user = createUser();

    // Create a mock subscription that throws an exception
    $subscription = Mockery::mock(Subscription::class);
    $subscription->shouldReceive('cancel')
        ->once()
        ->andThrow(new \Exception('Cancel error'));

    // Mock the user's subscription method
    $user->shouldReceive('subscription')
        ->once()
        ->with('default')
        ->andReturn($subscription);

    $this->actingAs($user)
        ->delete(route('subscription.cancel'))
        ->assertRedirect()
        ->assertSessionHas('error');
});

test('swapSubscription changes subscription plan', function () {
    Mail::fake();

    $user = createUser();
    $newPlan = createPlan(['id' => 2, 'name' => 'Pro Plan', 'stripe_price_id' => 'price_456']);

    // Create a mock subscription
    $subscription = Mockery::mock(Subscription::class);
    $subscription->shouldReceive('swap')
        ->once()
        ->with('price_456')
        ->andReturn($subscription);

    $subscription->shouldReceive('update')
        ->once()
        ->andReturn(true);

    // Mock the user's subscription method
    $user->shouldReceive('subscription')
        ->once()
        ->with('default')
        ->andReturn($subscription);

    $this->actingAs($user)
        ->put(route('subscription.swap'), [
            'new_plan_id' => 2,
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    Mail::assertSent(SubscriptionSwapped::class, function ($mail) use ($user, $subscription, $newPlan) {
        return $mail->hasTo($user->email) &&
               $mail->user->is($user) &&
               $mail->subscription === $subscription &&
               $mail->plan->is($newPlan);
    });
});

test('swapSubscription validates plan existence', function () {
    $user = createUser();

    $this->actingAs($user)
        ->put(route('subscription.swap'), [
            'new_plan_id' => 999, // Non-existent plan
        ])
        ->assertRedirect()
        ->assertSessionHas('error');
});

test('swapSubscription handles errors', function () {
    $user = createUser();
    $newPlan = createPlan(['id' => 2, 'stripe_price_id' => 'price_456']);

    // Create a mock subscription that throws an exception
    $subscription = Mockery::mock(Subscription::class);
    $subscription->shouldReceive('swap')
        ->once()
        ->with('price_456')
        ->andThrow(new \Exception('Swap error'));

    // Mock the user's subscription method
    $user->shouldReceive('subscription')
        ->once()
        ->with('default')
        ->andReturn($subscription);

    $this->actingAs($user)
        ->put(route('subscription.swap'), [
            'new_plan_id' => 2,
        ])
        ->assertRedirect()
        ->assertSessionHas('error');
});

test('downloadUserInvoice returns invoice download response', function () {
    $user = createUser();

    // Create a mock invoice
    $invoice = Mockery::mock();
    $invoice->shouldReceive('date')
        ->once()
        ->andReturn(Carbon::parse('2025-07-01'));

    // Mock the user's findInvoice method
    $user->shouldReceive('findInvoice')
        ->once()
        ->with('inv_123')
        ->andReturn($invoice);

    // Mock the user's downloadInvoice method
    $user->shouldReceive('downloadInvoice')
        ->once()
        ->with('inv_123', Mockery::any(), 'invoice-2025-07-inv_123')
        ->andReturn(response()->make('Invoice PDF', 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="invoice.pdf"',
        ]));

    $this->actingAs($user)
        ->get(route('subscription.invoice.download', ['invoiceId' => 'inv_123']))
        ->assertStatus(200)
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('Content-Disposition', 'attachment; filename="invoice.pdf"');
});

test('downloadUserInvoice returns 404 for non-existent invoice', function () {
    $user = createUser();

    // Mock the user's findInvoice method
    $user->shouldReceive('findInvoice')
        ->once()
        ->with('inv_999')
        ->andReturn(null);

    $this->actingAs($user)
        ->get(route('subscription.invoice.download', ['invoiceId' => 'inv_999']))
        ->assertStatus(404);
});

test('success sends confirmation email and redirects', function () {
    Mail::fake();

    $user = createUser();

    // Create a mock subscription
    $subscription = Mockery::mock(Subscription::class);
    $subscription->last_subscription_created_email_sent_at = null;

    $subscription->shouldReceive('update')
        ->once()
        ->andReturn(true);

    // Mock the user's subscription method
    $user->shouldReceive('subscription')
        ->once()
        ->with('default')
        ->andReturn($subscription);

    $this->actingAs($user)
        ->get(route('checkout-success'))
        ->assertRedirect(route('settings.subscription'))
        ->assertSessionHas('info');

    Mail::assertSent(SubscriptionConfirmed::class, function ($mail) use ($user, $subscription) {
        return $mail->hasTo($user->email) &&
               $mail->user->is($user) &&
               $mail->subscription === $subscription;
    });
});

test('success does not send email if one was recently sent', function () {
    Mail::fake();

    $user = createUser();

    // Create a mock subscription with recent email
    $subscription = Mockery::mock(Subscription::class);
    $subscription->last_subscription_created_email_sent_at = Carbon::now()->subMinute();

    // Mock the user's subscription method
    $user->shouldReceive('subscription')
        ->once()
        ->with('default')
        ->andReturn($subscription);

    $this->actingAs($user)
        ->get(route('checkout-success'))
        ->assertRedirect(route('settings.subscription'))
        ->assertSessionHas('info');

    Mail::assertNotSent(SubscriptionConfirmed::class);
});

test('cancel redirects to pricing page', function () {
    $user = createUser();

    $this->actingAs($user)
        ->get(route('checkout-cancel'))
        ->assertRedirect(route('pricing.index'))
        ->assertSessionHas('info');
});
