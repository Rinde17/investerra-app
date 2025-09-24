<?php

use App\Models\TerrainAnalysis;
use App\Models\Terrain;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

test('terrain analysis has a terrain relationship', function () {
    $terrain = createTerrain();
    $analysis = TerrainAnalysis::factory()->create(['terrain_id' => $terrain->id]);

    expect($analysis->terrain)->toBeInstanceOf(Terrain::class)
        ->and($analysis->terrain->id)->toBe($terrain->id);
});

test('terrain analysis casts price_m2 to decimal', function () {
    $analysis = TerrainAnalysis::factory()->create(['price_m2' => 150.50]);

    expect($analysis->price_m2)->toEqual(150.50);
});

test('terrain analysis casts market_price_m2 to decimal', function () {
    $analysis = TerrainAnalysis::factory()->create(['market_price_m2' => 200.75]);

    expect($analysis->market_price_m2)->toEqual(200.75);
});

test('terrain analysis casts price_difference_percentage to decimal', function () {
    $analysis = TerrainAnalysis::factory()->create(['price_difference_percentage' => 15.25]);

    expect($analysis->price_difference_percentage)->toEqual(15.25);
});

test('terrain analysis casts viability_cost to decimal', function () {
    $analysis = TerrainAnalysis::factory()->create(['viability_cost' => 5000.50]);

    expect($analysis->viability_cost)->toEqual(5000.50);
});

test('terrain analysis casts lots_possible to integer', function () {
    $analysis = TerrainAnalysis::factory()->create(['lots_possible' => 5]);

    expect($analysis->lots_possible)->toEqual(5);
});

test('terrain analysis casts resale_estimate_min to decimal', function () {
    $analysis = TerrainAnalysis::factory()->create(['resale_estimate_min' => 100000.50]);

    expect($analysis->resale_estimate_min)->toEqual(100000.50);
});

test('terrain analysis casts resale_estimate_max to decimal', function () {
    $analysis = TerrainAnalysis::factory()->create(['resale_estimate_max' => 150000.75]);

    expect($analysis->resale_estimate_max)->toEqual(150000.75);
});

test('terrain analysis casts net_margin_estimate to decimal', function () {
    $analysis = TerrainAnalysis::factory()->create(['net_margin_estimate' => 50000.25]);

    expect($analysis->net_margin_estimate)->toEqual(50000.25);
});

test('terrain analysis casts profit_margin_percentage to decimal', function () {
    $analysis = TerrainAnalysis::factory()->create(['profit_margin_percentage' => 25.50]);

    expect($analysis->profit_margin_percentage)->toEqual(25.50);
});

test('terrain analysis casts ai_score to decimal', function () {
    $analysis = TerrainAnalysis::factory()->create(['ai_score' => 85.75]);

    expect($analysis->ai_score)->toEqual(85.75);
});

test('terrain analysis casts analysis_details to array', function () {
    $details = ['risk' => 'low', 'opportunity' => 'high'];
    $analysis = TerrainAnalysis::factory()->create(['analysis_details' => $details]);

    expect($analysis->analysis_details)->toBe($details)
        ->and($analysis->analysis_details)->toBeArray();
});

test('terrain analysis casts analyzed_at to datetime', function () {
    $date = now();
    $analysis = TerrainAnalysis::factory()->create(['analyzed_at' => $date]);

    expect($analysis->analyzed_at)->toBeInstanceOf(Carbon::class)
        ->and($analysis->analyzed_at->toDateTimeString())->toBe($date->toDateTimeString());
});

test('terrain analysis casts overall_risk to string', function () {
    $analysis = TerrainAnalysis::factory()->create(['overall_risk' => 'medium']);

    expect($analysis->overall_risk)->toBe('medium')
        ->and($analysis->overall_risk)->toBeString();
});

test('terrain analysis casts overall_recommendation to string', function () {
    $analysis = TerrainAnalysis::factory()->create(['overall_recommendation' => 'proceed with caution']);

    expect($analysis->overall_recommendation)->toBe('proceed with caution')
        ->and($analysis->overall_recommendation)->toBeString();
});
