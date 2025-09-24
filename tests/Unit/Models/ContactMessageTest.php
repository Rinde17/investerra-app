<?php

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('contact message can be created with attributes', function () {
    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Test Subject',
        'message' => 'This is a test message',
        'is_read' => false,
    ];

    $message = createContactMessage($data);

    expect($message)->toBeInstanceOf(ContactMessage::class)
        ->and($message->name)->toBe($data['name'])
        ->and($message->email)->toBe($data['email'])
        ->and($message->subject)->toBe($data['subject'])
        ->and($message->message)->toBe($data['message'])
        ->and($message->is_read)->toBe($data['is_read']);
});

test('contact message can be created with factory', function () {
    $message = ContactMessage::factory()->create();

    expect($message)->toBeInstanceOf(ContactMessage::class)
        ->and($message->exists)->toBeTrue();
});

test('contact message can be marked as read', function () {
    $message = createContactMessage(['is_read' => false]);

    $message->is_read = true;
    $message->save();

    $message->refresh();
    expect($message->is_read)->toBeTrue();
});
