<?php

use App\Mail\ContactFormMail;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('index displays contact page', function () {
    $this->get(route('contact'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Contact')
        );
});

test('store validates form data', function () {
    $this->post(route('contact.store'), [
        'name' => '',
        'email' => 'not-an-email',
        'message' => '',
    ])
        ->assertRedirect()
        ->assertSessionHasErrors(['name', 'email', 'message']);
});

test('store saves message to database and sends email', function () {
    Mail::fake();

    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Test Subject',
        'message' => 'This is a test message',
    ];

    $this->post(route('contact.store'), $data)
        ->assertRedirect()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('contact_messages', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Test Subject',
        'message' => 'This is a test message',
    ]);

    Mail::assertSent(ContactFormMail::class, function ($mail) use ($data) {
        return $mail->hasTo('votre_email_de_reception@exemple.com') &&
               $mail->data['name'] === $data['name'] &&
               $mail->data['email'] === $data['email'] &&
               $mail->data['subject'] === $data['subject'] &&
               $mail->data['message'] === $data['message'];
    });
});

test('store handles database save error', function () {
    Mail::fake();

    // Mock ContactMessage to throw an exception when create is called
    $this->mock(ContactMessage::class, function ($mock) {
        $mock->shouldReceive('create')
            ->andThrow(new \Exception('Database error'));
    });

    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Test Subject',
        'message' => 'This is a test message',
    ];

    $this->post(route('contact.store'), $data)
        ->assertRedirect()
        ->assertSessionHasErrors('error');

    Mail::assertNotSent(ContactFormMail::class);
});

test('store handles email send error', function () {
    // Make Mail::to()->send() throw an exception
    Mail::shouldReceive('to')
        ->once()
        ->andThrow(new \Exception('Email error'));

    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Test Subject',
        'message' => 'This is a test message',
    ];

    $this->post(route('contact.store'), $data)
        ->assertRedirect()
        ->assertSessionHasErrors('email_error');

    $this->assertDatabaseHas('contact_messages', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Test Subject',
        'message' => 'This is a test message',
    ]);
});
