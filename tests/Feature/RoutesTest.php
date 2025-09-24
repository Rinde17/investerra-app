<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('public routes are accessible', function () {
    // Home page
    $this->get('/')
        ->assertStatus(200);

    // Pricing page
    $this->get('/pricing')
        ->assertStatus(200);

    // Contact page
    $this->get('/contact')
        ->assertStatus(200);
});

test('auth routes redirect unauthenticated users to login', function () {
    // Dashboard
    $this->get('/dashboard')
        ->assertRedirect('/login');

    // Projects
    $this->get('/projects')
        ->assertRedirect('/login');

    // Terrains
    $this->get('/terrains')
        ->assertRedirect('/login');

    // Settings
    $this->get('/settings/profile')
        ->assertRedirect('/login');
});

test('admin routes are protected from non-admin users', function () {
    $user = createUser(['is_admin' => false]);

    $this->actingAs($user)
        ->get('/admin/dashboard')
        ->assertStatus(403);
});

test('admin routes are accessible to admin users', function () {
    $admin = createUser(['is_admin' => true]);

    $this->actingAs($admin)
        ->get('/admin/dashboard')
        ->assertStatus(200);
});

test('project routes work correctly for authenticated users', function () {
    $user = createUser();

    // Index
    $this->actingAs($user)
        ->get('/projects')
        ->assertStatus(200);

    // Create
    $this->actingAs($user)
        ->get('/projects/create')
        ->assertStatus(200);

    // Store
    $this->actingAs($user)
        ->post('/projects', [
            'name' => 'Test Project',
            'description' => 'Test Description',
        ])
        ->assertRedirect();

    // The project should now exist in the database
    $project = \App\Models\Project::where('name', 'Test Project')->first();
    $this->assertNotNull($project);

    // Show
    $this->actingAs($user)
        ->get("/projects/{$project->id}")
        ->assertStatus(200);

    // Edit
    $this->actingAs($user)
        ->get("/projects/{$project->id}/edit")
        ->assertStatus(200);

    // Update
    $this->actingAs($user)
        ->put("/projects/{$project->id}", [
            'name' => 'Updated Project',
            'description' => 'Updated Description',
        ])
        ->assertRedirect();

    // Delete
    $this->actingAs($user)
        ->delete("/projects/{$project->id}")
        ->assertRedirect();
});

test('terrain routes work correctly for authenticated users', function () {
    $user = createUser();

    // Mock the GeocodingService
    $this->mock(\App\Services\GeocodingService::class, function ($mock) {
        $mock->shouldReceive('getCoordinates')
            ->andReturn([
                'latitude' => 48.8566,
                'longitude' => 2.3522,
            ]);
    });

    // Mock the AIAnalysisService
    $this->mock(\App\Services\AIAnalysisService::class, function ($mock) {
        $mock->shouldReceive('analyzeTerrain')
            ->andReturn(\App\Models\TerrainAnalysis::factory()->make());
    });

    // Index
    $this->actingAs($user)
        ->get('/terrains')
        ->assertStatus(200);

    // Create
    $this->actingAs($user)
        ->get('/terrains/create')
        ->assertStatus(200);

    // Store
    $this->actingAs($user)
        ->post('/terrains', [
            'title' => 'Test Terrain',
            'description' => 'Test Description',
            'surface_m2' => 1000,
            'price' => 100000,
            'city' => 'Paris',
            'zip_code' => '75001',
            'viabilised' => true,
        ])
        ->assertRedirect();

    // The terrain should now exist in the database
    $terrain = \App\Models\Terrain::where('title', 'Test Terrain')->first();
    $this->assertNotNull($terrain);

    // Show
    $this->actingAs($user)
        ->get("/terrains/{$terrain->id}")
        ->assertStatus(200);

    // Edit
    $this->actingAs($user)
        ->get("/terrains/{$terrain->id}/edit")
        ->assertStatus(200);

    // Update
    $this->actingAs($user)
        ->put("/terrains/{$terrain->id}", [
            'title' => 'Updated Terrain',
            'description' => 'Updated Description',
            'surface_m2' => 1500,
            'price' => 150000,
            'city' => 'Paris',
            'zip_code' => '75001',
            'viabilised' => true,
        ])
        ->assertRedirect();

    // Delete
    $this->actingAs($user)
        ->delete("/terrains/{$terrain->id}")
        ->assertRedirect();
});

test('settings routes work correctly for authenticated users', function () {
    $user = createUser();

    // Profile
    $this->actingAs($user)
        ->get('/settings/profile')
        ->assertStatus(200);

    // Team
    $this->actingAs($user)
        ->get('/settings/team')
        ->assertStatus(200);

    // Subscription
    // Mock the user's subscription method for the subscription settings page
    $user->shouldReceive('subscription')
        ->andReturn(null);

    $user->shouldReceive('hasStripeId')
        ->andReturn(false);

    $this->actingAs($user)
        ->get('/settings/subscription')
        ->assertStatus(200);
});

test('contact form submission works', function () {
    // Mock the Mail facade
    \Illuminate\Support\Facades\Mail::fake();

    $this->post('/contact', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Test Subject',
        'message' => 'This is a test message',
    ])
        ->assertRedirect()
        ->assertSessionHas('success');

    // The message should now exist in the database
    $this->assertDatabaseHas('contact_messages', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Test Subject',
        'message' => 'This is a test message',
    ]);

    // The email should have been sent
    \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\ContactFormMail::class);
});
