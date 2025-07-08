<?php

namespace App\Services;

use App\Models\Terrain;
use App\Models\TerrainAnalysis;
use App\Services\BieniciMarketPriceM2Service;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;

class AIAnalysisService
{
    /**
     * @var BieniciMarketPriceM2Service
     */
    protected BieniciMarketPriceM2Service $bieniciMarketPriceM2Service;

    /**
     * AIAnalysisService constructor.
     *
     * @param BieniciMarketPriceM2Service $bieniciMarketPriceM2Service
     */
    public function __construct(BieniciMarketPriceM2Service $bieniciMarketPriceM2Service)
    {
        $this->bieniciMarketPriceM2Service = $bieniciMarketPriceM2Service;
    }

    /**
     * Analyze a terrain using AI algorithms and update or create its analysis.
     *
     * @param Terrain $terrain The terrain to analyze
     * @return TerrainAnalysis The updated or created terrain analysis
     * @throws ConnectionException
     */
    public function analyzeTerrain(Terrain $terrain): TerrainAnalysis
    {
        // Load the terrain analysis if it exists
        if (!$terrain->relationLoaded('analysis')) {
            $terrain->load('analysis');
        }

        $marketPriceM2 = $this->estimateMarketPriceM2($terrain);

        // Calculate AI analysis metrics
        $aiScore = $this->calculateAIScore($terrain, $marketPriceM2);
        $analysisDetails = $this->generateAnalysisDetails($terrain, $marketPriceM2);
        $profitabilityLabel = $this->determineProfitabilityLabel($aiScore);

        // Calculate additional metrics for the new fields
        $pricePerM2 = $terrain->getPricePerM2();
        $priceDifference = $marketPriceM2 > 0 ? (($pricePerM2 - $marketPriceM2) / $marketPriceM2) * 100 : 0;
        $priceDifferencePercentage = round($priceDifference, 2);

        $netMarginEstimate = $this->calculateNetMarginEstimate($terrain, $marketPriceM2);
        $profitMarginPercentage = $terrain->price > 0 ? round(($netMarginEstimate / $terrain->price) * 100, 2) : 0;

        // Extract overall risk and recommendation from analysis details
        $overallRisk = $analysisDetails['risk_assessment']['overall_risk'] ?? 'medium';
        $overallRecommendation = $analysisDetails['recommendations']['overall_recommendation'] ?? 'neutral';

        // Update or create the terrain analysis
        if ($terrain->analysis) {
            $terrain->analysis->update([
                'price_m2' => $pricePerM2,
                'market_price_m2' => $marketPriceM2,
                'price_difference_percentage' => $priceDifferencePercentage,
                'viability_cost' => $this->estimateViabilityCost($terrain),
                'lots_possible' => $this->estimateLotsPossible($terrain),
                'resale_estimate_min' => $this->calculateResaleEstimateMin($terrain, $marketPriceM2),
                'resale_estimate_max' => $this->calculateResaleEstimateMax($terrain, $marketPriceM2),
                'net_margin_estimate' => $netMarginEstimate,
                'profit_margin_percentage' => $profitMarginPercentage,
                'ai_score' => $aiScore,
                'profitability_label' => $profitabilityLabel,
                'overall_risk' => $overallRisk,
                'overall_recommendation' => $overallRecommendation,
                'analysis_details' => $analysisDetails,
                'analyzed_at' => now(),
            ]);
            return $terrain->analysis;
        } else {
            // Create a new analysis with basic metrics and AI analysis
            return TerrainAnalysis::create([
                'terrain_id' => $terrain->id,
                'price_m2' => $pricePerM2,
                'market_price_m2' => $marketPriceM2,
                'price_difference_percentage' => $priceDifferencePercentage,
                'viability_cost' => $this->estimateViabilityCost($terrain),
                'lots_possible' => $this->estimateLotsPossible($terrain),
                'resale_estimate_min' => $this->calculateResaleEstimateMin($terrain, $marketPriceM2),
                'resale_estimate_max' => $this->calculateResaleEstimateMax($terrain, $marketPriceM2),
                'net_margin_estimate' => $netMarginEstimate,
                'profit_margin_percentage' => $profitMarginPercentage,
                'ai_score' => $aiScore,
                'profitability_label' => $profitabilityLabel,
                'overall_risk' => $overallRisk,
                'overall_recommendation' => $overallRecommendation,
                'analysis_details' => $analysisDetails,
                'analyzed_at' => now(),
            ]);
        }
    }

    /**
     * Calculate the AI score for a terrain based on various factors.
     *
     * @param Terrain $terrain The terrain to analyze
     * @return float The AI score (0-100)
     */
    private function calculateAIScore(Terrain $terrain, float $marketPriceM2): float
    {
        // Initialize base score
        $score = 50.0;

        // Price per m² factor (lower is better)
        $pricePerM2 = $terrain->getPricePerM2();

        if ($marketPriceM2 > 0) {
            $priceRatio = $pricePerM2 / $marketPriceM2;
            // If price is below market, increase score
            if ($priceRatio < 0.9) {
                $score += 15 * (1 - $priceRatio);
            }
            // If price is above market, decrease score
            else if ($priceRatio > 1.1) {
                $score -= 10 * ($priceRatio - 1);
            }
        }

        // Surface area factor (larger plots generally better for development)
        if ($terrain->surface_m2 > 2000) {
            $score += 10;
        } else if ($terrain->surface_m2 > 1000) {
            $score += 5;
        }

        // Viability factor
        if ($terrain->viabilised) {
            $score += 10;
        } else {
            // Estimate viability cost impact
            $viabilityCost = $this->estimateViabilityCost($terrain);
            $viabilityCostRatio = $viabilityCost / $terrain->price;
            $score -= min(10, $viabilityCostRatio * 100);
        }

        // Profitability factor
        $netMargin = $this->calculateNetMarginEstimate($terrain, $marketPriceM2);
        $profitMarginRatio = $terrain->price > 0 ? $netMargin / $terrain->price : 0;
        if ($profitMarginRatio > 0.3) {
            $score += 15;
        } else if ($profitMarginRatio > 0.2) {
            $score += 10;
        } else if ($profitMarginRatio > 0.1) {
            $score += 5;
        } else if ($profitMarginRatio < 0) {
            $score -= 20;
        }

        // Ensure score is within 0-100 range
        return max(0, min(100, $score));
    }

    /**
     * Generate detailed analysis information for a terrain.
     *
     * @param Terrain $terrain The terrain to analyze
     * @return array The detailed analysis information
     */
    private function generateAnalysisDetails(Terrain $terrain, float $marketPriceM2): array
    {
        $pricePerM2 = $terrain->getPricePerM2();
        $viabilityCost = $this->estimateViabilityCost($terrain);
        $lotsPossible = $this->estimateLotsPossible($terrain);
        $resaleEstimateMin = $this->calculateResaleEstimateMin($terrain, $marketPriceM2);
        $resaleEstimateMax = $this->calculateResaleEstimateMax($terrain, $marketPriceM2);
        $netMarginEstimate = $this->calculateNetMarginEstimate($terrain, $marketPriceM2);

        $priceAnalysis = $this->analyzePriceCompetitiveness($pricePerM2, $marketPriceM2);
        $developmentPotential = $this->analyzeDevelopmentPotential($terrain, $lotsPossible);
        $profitabilityAnalysis = $this->analyzeProfitability($terrain->price, $netMarginEstimate);
        $riskAssessment = $this->assessRisks($terrain, $viabilityCost, $marketPriceM2);
        $recommendations = $this->generateRecommendations($priceAnalysis, $developmentPotential, $profitabilityAnalysis, $riskAssessment);

        return [
            'price_analysis' => $priceAnalysis,
            'development_potential' => $developmentPotential,
            'profitability_analysis' => $profitabilityAnalysis,
            'risk_assessment' => $riskAssessment,
            'recommendations' => $recommendations,
            'resale_estimate_min' => $resaleEstimateMin,
            'resale_estimate_max' => $resaleEstimateMax,
        ];
    }

    /**
     * Analyze the price competitiveness of a terrain.
     *
     * @param float $pricePerM2 The price per m² of the terrain
     * @param float $marketPriceM2 The estimated market price per m²
     * @return array The price analysis
     */
    private function analyzePriceCompetitiveness(float $pricePerM2, float $marketPriceM2): array
    {
        $priceDifference = $marketPriceM2 > 0 ? (($pricePerM2 - $marketPriceM2) / $marketPriceM2) * 100 : 0;
        $rating = 'neutral';
        $comment = '';

        if ($priceDifference <= -15) {
            $rating = 'excellent';
            $comment = 'The price is significantly below market value, representing an excellent opportunity.';
        } else if ($priceDifference <= -5) {
            $rating = 'good';
            $comment = 'The price is below market value, representing a good opportunity.';
        } else if ($priceDifference <= 5) {
            $rating = 'fair';
            $comment = 'The price is in line with market value.';
        } else if ($priceDifference <= 15) {
            $rating = 'poor';
            $comment = 'The price is above market value, which may impact profitability.';
        } else {
            $rating = 'very_poor';
            $comment = 'The price is significantly above market value, which will likely make this investment unprofitable.';
        }

        return [
            'price_per_m2' => $pricePerM2,
            'market_price_m2' => $marketPriceM2,
            'price_difference_percentage' => round($priceDifference, 2),
            'rating' => $rating,
            'comment' => $comment,
        ];
    }

    /**
     * Analyze the development potential of a terrain.
     *
     * @param Terrain $terrain The terrain to analyze
     * @param int $lotsPossible The estimated number of lots possible
     * @return array The development potential analysis
     */
    private function analyzeDevelopmentPotential(Terrain $terrain, int $lotsPossible): array
    {
        $rating = 'neutral';
        $comment = '';

        if ($lotsPossible >= 5) {
            $rating = 'excellent';
            $comment = 'The terrain has excellent development potential with multiple lots possible.';
        } else if ($lotsPossible >= 3) {
            $rating = 'good';
            $comment = 'The terrain has good development potential with several lots possible.';
        } else if ($lotsPossible >= 1) {
            $rating = 'fair';
            $comment = 'The terrain has fair development potential with limited lots possible.';
        } else {
            $rating = 'poor';
            $comment = 'The terrain has poor development potential with very limited or no lots possible.';
        }

        // Add viability information
        if ($terrain->viabilised) {
            $comment .= ' The terrain is already viabilised, which is a significant advantage.';
        } else {
            $comment .= ' The terrain is not viabilised, which will require additional investment.';
        }

        return [
            'surface_m2' => $terrain->surface_m2,
            'lots_possible' => $lotsPossible,
            'viabilised' => $terrain->viabilised,
            'rating' => $rating,
            'comment' => $comment,
        ];
    }

    /**
     * Analyze the profitability of a terrain.
     *
     * @param float $purchasePrice The purchase price of the terrain
     * @param float $netMarginEstimate The estimated net margin
     * @return array The profitability analysis
     */
    private function analyzeProfitability(float $purchasePrice, float $netMarginEstimate): array
    {
        $profitMarginPercentage = $purchasePrice > 0 ? ($netMarginEstimate / $purchasePrice) * 100 : 0;
        $rating = 'neutral';
        $comment = '';

        if ($profitMarginPercentage >= 30) {
            $rating = 'excellent';
            $comment = 'The estimated profit margin is excellent, suggesting a highly profitable investment.';
        } else if ($profitMarginPercentage >= 20) {
            $rating = 'good';
            $comment = 'The estimated profit margin is good, suggesting a profitable investment.';
        } else if ($profitMarginPercentage >= 10) {
            $rating = 'fair';
            $comment = 'The estimated profit margin is fair, suggesting a moderately profitable investment.';
        } else if ($profitMarginPercentage >= 0) {
            $rating = 'poor';
            $comment = 'The estimated profit margin is poor, suggesting a marginally profitable investment.';
        } else {
            $rating = 'very_poor';
            $comment = 'The estimated profit margin is negative, suggesting an unprofitable investment.';
        }

        return [
            'purchase_price' => $purchasePrice,
            'net_margin_estimate' => $netMarginEstimate,
            'profit_margin_percentage' => round($profitMarginPercentage, 2),
            'rating' => $rating,
            'comment' => $comment,
        ];
    }

    /**
     * Assess the risks associated with a terrain investment.
     *
     * @param Terrain $terrain The terrain to analyze
     * @param float $viabilityCost The estimated viability cost
     * @return array The risk assessment
     */
    private function assessRisks(Terrain $terrain, float $viabilityCost, float $marketPriceM2): array
    {
        $risks = [];
        $overallRisk = 'low';

        // Price risk
        $pricePerM2 = $terrain->getPricePerM2();
        if ($pricePerM2 > $marketPriceM2 * 1.15) {
            $risks[] = [
                'type' => 'price',
                'level' => 'high',
                'description' => 'The price is significantly above market value, which increases the financial risk.',
            ];
            $overallRisk = 'high';
        } else if ($pricePerM2 > $marketPriceM2 * 1.05) {
            $risks[] = [
                'type' => 'price',
                'level' => 'medium',
                'description' => 'The price is above market value, which presents some financial risk.',
            ];
            $overallRisk = max($overallRisk, 'medium');
        }

        // Viability risk
        if (!$terrain->viabilised) {
            $viabilityCostRatio = $viabilityCost / $terrain->price;
            if ($viabilityCostRatio > 0.3) {
                $risks[] = [
                    'type' => 'viability',
                    'level' => 'high',
                    'description' => 'The terrain is not viabilised and the estimated viability cost is very high relative to the purchase price.',
                ];
                $overallRisk = 'high';
            } else if ($viabilityCostRatio > 0.15) {
                $risks[] = [
                    'type' => 'viability',
                    'level' => 'medium',
                    'description' => 'The terrain is not viabilised and the estimated viability cost is significant relative to the purchase price.',
                ];
                $overallRisk = max($overallRisk, 'medium');
            } else {
                $risks[] = [
                    'type' => 'viability',
                    'level' => 'low',
                    'description' => 'The terrain is not viabilised but the estimated viability cost is reasonable relative to the purchase price.',
                ];
            }
        }

        // Size risk
        if ($terrain->surface_m2 < 500) {
            $risks[] = [
                'type' => 'size',
                'level' => 'medium',
                'description' => 'The terrain is relatively small, which may limit development options.',
            ];
            $overallRisk = max($overallRisk, 'medium');
        }

        return [
            'overall_risk' => $overallRisk,
            'specific_risks' => $risks,
        ];
    }

    /**
     * Generate recommendations based on the analysis.
     *
     * @param array $priceAnalysis The price analysis
     * @param array $developmentPotential The development potential analysis
     * @param array $profitabilityAnalysis The profitability analysis
     * @param array $riskAssessment The risk assessment
     * @return array The recommendations
     */
    private function generateRecommendations(array $priceAnalysis, array $developmentPotential, array $profitabilityAnalysis, array $riskAssessment): array
    {
        $recommendations = [];

        // Overall recommendation
        $overallRecommendation = 'neutral';
        $overallComment = '';

        if ($priceAnalysis['rating'] === 'excellent' && $profitabilityAnalysis['rating'] === 'excellent' && $riskAssessment['overall_risk'] === 'low') {
            $overallRecommendation = 'strong_buy';
            $overallComment = 'This terrain represents an excellent investment opportunity with high potential returns and low risk.';
        } else if (($priceAnalysis['rating'] === 'excellent' || $priceAnalysis['rating'] === 'good') &&
                  ($profitabilityAnalysis['rating'] === 'excellent' || $profitabilityAnalysis['rating'] === 'good') &&
                  $riskAssessment['overall_risk'] !== 'high') {
            $overallRecommendation = 'buy';
            $overallComment = 'This terrain represents a good investment opportunity with solid potential returns and manageable risk.';
        } else if ($profitabilityAnalysis['rating'] === 'very_poor' || $riskAssessment['overall_risk'] === 'high') {
            $overallRecommendation = 'avoid';
            $overallComment = 'This terrain represents a poor investment opportunity with significant risks or inadequate returns.';
        } else if ($priceAnalysis['rating'] === 'poor' || $profitabilityAnalysis['rating'] === 'poor') {
            $overallRecommendation = 'caution';
            $overallComment = 'This terrain should be approached with caution as it presents some concerns regarding price or profitability.';
        } else {
            $overallRecommendation = 'neutral';
            $overallComment = 'This terrain represents a moderate investment opportunity with balanced risk and return potential.';
        }

        // Specific recommendations
        $specificRecommendations = [];

        // Negotiation recommendation
        if ($priceAnalysis['rating'] === 'poor' || $priceAnalysis['rating'] === 'very_poor') {
            $specificRecommendations[] = 'Consider negotiating the purchase price to improve the investment potential.';
        }

        // Development recommendation
        if ($developmentPotential['lots_possible'] >= 3) {
            $specificRecommendations[] = 'The terrain has good potential for subdivision into multiple lots, which could maximize returns.';
        }

        // Viability recommendation
        if (!$developmentPotential['viabilised']) {
            $specificRecommendations[] = 'Factor in the cost and time required for viabilisation when evaluating this investment.';
        }

        // Risk mitigation recommendations
        foreach ($riskAssessment['specific_risks'] as $risk) {
            if ($risk['level'] === 'high') {
                $specificRecommendations[] = 'Take steps to mitigate the ' . $risk['type'] . ' risk: ' . $risk['description'];
            }
        }

        return [
            'overall_recommendation' => $overallRecommendation,
            'overall_comment' => $overallComment,
            'specific_recommendations' => $specificRecommendations,
        ];
    }

    /**
     * Determine the profitability label based on the AI score.
     *
     * @param float $aiScore The AI score
     * @return string The profitability label
     */
    private function determineProfitabilityLabel(float $aiScore): string
    {
        if ($aiScore >= 80) {
            return 'excellent';
        } else if ($aiScore >= 65) {
            return 'good';
        } else if ($aiScore >= 50) {
            return 'fair';
        } else if ($aiScore >= 35) {
            return 'poor';
        } else {
            return 'very_poor';
        }
    }

    /**
     * Estimate the market price per m² for a terrain.
     *
     * @param Terrain $terrain The terrain to analyze
     * @return float The estimated market price per m²
     * @throws ConnectionException
     */
    private function estimateMarketPriceM2(Terrain $terrain): float
    {
        // Use BieniciMarketPriceM2Service to get the market price per m²
        $marketPriceM2 = $this->bieniciMarketPriceM2Service->getMarketPriceM2($terrain->city, $terrain->zip_code);

        // If the service returns null, use a default value
        if ($marketPriceM2 === null) {
            // Default value based on terrain location
            $marketPriceM2 = 100.0;

            // Adjust based on zip code (simplified example for fallback)
            $zipCode = (int) $terrain->zip_code;
            if ($zipCode >= 75000 && $zipCode < 76000) {
                // Paris area
                $marketPriceM2 = 500.0;
            } else if ($zipCode >= 13000 && $zipCode < 14000) {
                // Marseille area
                $marketPriceM2 = 300.0;
            }

            // Log that we're using a fallback value
            Log::warning("Using fallback market price for terrain {$terrain->id} in {$terrain->city} ({$terrain->zip_code})");
        }

        return (float) $marketPriceM2;
    }

    /**
     * Estimate the viability cost for a terrain.
     *
     * @param Terrain $terrain The terrain to analyze
     * @return float The estimated viability cost
     */
    private function estimateViabilityCost(Terrain $terrain): float
    {
        if ($terrain->viabilised) {
            return 0.0; // Already viabilised
        }

        // Static value of 10000 for viability cost
        return 10000.0;
    }

    /**
     * Estimate the number of lots possible for a terrain.
     *
     * @param Terrain $terrain The terrain to analyze
     * @return int The estimated number of lots possible
     */
    private function estimateLotsPossible(Terrain $terrain): int
    {
        // Simplified estimation based on terrain size
        // Assuming an average lot size of 500m²
        $averageLotSize = 500.0;

        // Calculate raw number of lots
        $rawLots = $terrain->surface_m2 / $averageLotSize;

        // Account for roads, common areas, etc. (typically 20-30%)
        $adjustedLots = $rawLots * 0.75;

        // Round down to get a whole number of lots
        return max(1, floor($adjustedLots));
    }

    /**
     * Calculate the minimum resale estimate for a terrain.
     *
     * @param Terrain $terrain The terrain to analyze
     * @return float The minimum resale estimate
     */
    private function calculateResaleEstimateMin(Terrain $terrain, float $marketPriceM2): float
    {
        $lotsPossible = $this->estimateLotsPossible($terrain);
        $averageLotSize = $terrain->surface_m2 / max(1, $lotsPossible);

        // Conservative estimate (90% of market price)
        $conservativePriceM2 = $marketPriceM2 * 0.9;

        return $conservativePriceM2 * $terrain->surface_m2;
    }

    /**
     * Calculate the maximum resale estimate for a terrain.
     *
     * @param Terrain $terrain The terrain to analyze
     * @return float The maximum resale estimate
     */
    private function calculateResaleEstimateMax(Terrain $terrain, float $marketPriceM2): float
    {
        $lotsPossible = $this->estimateLotsPossible($terrain);
        $averageLotSize = $terrain->surface_m2 / max(1, $lotsPossible);

        // Optimistic estimate (110% of market price)
        $optimisticPriceM2 = $marketPriceM2 * 1.1;

        return $optimisticPriceM2 * $terrain->surface_m2;
    }

    /**
     * Calculate the net margin estimate for a terrain.
     *
     * @param Terrain $terrain The terrain to analyze
     * @return float The net margin estimate
     */
    private function calculateNetMarginEstimate(Terrain $terrain, float $marketPriceM2): float
    {
        $viabilityCost = $this->estimateViabilityCost($terrain);
        $averageResaleEstimate = ($this->calculateResaleEstimateMin($terrain, $marketPriceM2) + $this->calculateResaleEstimateMax($terrain, $marketPriceM2)) / 2;

        // Additional costs (marketing, legal, etc.) - simplified as 5% of purchase price
        $additionalCosts = $terrain->price * 0.05;

        // Calculate net margin
        $totalCosts = $terrain->price + $viabilityCost + $additionalCosts;
        return $averageResaleEstimate - $totalCosts;
    }
}
