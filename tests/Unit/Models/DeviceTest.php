<?php

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('device has a user relationship', function () {
    $user = createUser();
    $device = createDevice([], $user);

    expect($device->user)->toBeInstanceOf(User::class)
        ->and($device->user->id)->toBe($user->id);
});

test('device casts last_used_at to datetime', function () {
    $date = now();
    $device = createDevice(['last_used_at' => $date]);

    expect($device->last_used_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class)
        ->and($device->last_used_at->toDateTimeString())->toBe($date->toDateTimeString());
});

test('device casts is_trusted to boolean', function () {
    $device = createDevice(['is_trusted' => true]);

    expect($device->is_trusted)->toBeTrue()
        ->and($device->is_trusted)->toBeBool();
});
