<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terrain Analysis Report - Pro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #4f46e5;
        }
        .report-title {
            font-size: 22px;
            margin: 10px 0;
        }
        .report-subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 5px;
        }
        .report-date {
            font-size: 14px;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 5px;
            color: #4f46e5;
        }
        .terrain-info {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .terrain-info-item {
            width: 50%;
            margin-bottom: 10px;
        }
        .terrain-info-label {
            font-weight: bold;
            display: block;
        }
        .analysis-results {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .analysis-results th, .analysis-results td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .analysis-results th {
            background-color: #4f46e5;
            color: white;
        }
        .analysis-results tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .profitability {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
        }
        .excellent {
            background-color: #d1fae5;
            color: #065f46;
        }
        .good {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .average {
            background-color: #fef3c7;
            color: #92400e;
        }
        .poor {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .watermark {
            position: fixed;
            bottom: 10px;
            right: 10px;
            opacity: 0.1;
            font-size: 100px;
            transform: rotate(-45deg);
            z-index: -1;
        }
        .chart-container {
            width: 100%;
            margin: 20px 0;
            text-align: center;
        }
        .bar-chart {
            width: 100%;
            height: 20px;
            background-color: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 5px;
        }
        .bar {
            height: 100%;
            background-color: #4f46e5;
            border-radius: 10px;
        }
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .comparison-table th, .comparison-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .comparison-table th {
            background-color: #4f46e5;
            color: white;
        }
        .comparison-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .comparison-table .metric {
            text-align: left;
            font-weight: bold;
        }
        .comparison-table .better {
            color: #065f46;
            font-weight: bold;
        }
        .comparison-table .worse {
            color: #b91c1c;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="watermark">INVESTERRA PRO</div>

    <div class="header">
        <div class="logo">INVESTERRA</div>
        <div class="report-title">Terrain Analysis Report</div>
        <div class="report-subtitle">Professional Edition</div>
        <div class="report-date">Generated on: {{ date('F j, Y') }}</div>
    </div>

    <div class="section">
        <div class="section-title">Terrain Information</div>
        <div class="terrain-info">
            <div class="terrain-info-item">
                <span class="terrain-info-label">Title:</span>
                {{ $terrain->title }}
            </div>
            <div class="terrain-info-item">
                <span class="terrain-info-label">Location:</span>
                {{ $terrain->city }}, {{ $terrain->zip_code }}
            </div>
            <div class="terrain-info-item">
                <span class="terrain-info-label">Surface Area:</span>
                {{ number_format($terrain->surface_m2, 2) }} m²
            </div>
            <div class="terrain-info-item">
                <span class="terrain-info-label">Price:</span>
                {{ number_format($terrain->price, 2) }} €
            </div>
            <div class="terrain-info-item">
                <span class="terrain-info-label">Status:</span>
                {{ $terrain->viabilised ? 'Viabilised' : 'Not Viabilised' }}
            </div>
            <div class="terrain-info-item">
                <span class="terrain-info-label">Price per m²:</span>
                {{ number_format($analysis->price_m2, 2) }} €/m²
            </div>
            @if($terrain->latitude && $terrain->longitude)
            <div class="terrain-info-item">
                <span class="terrain-info-label">Coordinates:</span>
                {{ $terrain->latitude }}, {{ $terrain->longitude }}
            </div>
            @endif
            @if($terrain->source_platform)
            <div class="terrain-info-item">
                <span class="terrain-info-label">Source:</span>
                {{ $terrain->source_platform }}
            </div>
            @endif
        </div>
    </div>

    <div class="section">
        <div class="section-title">Analysis Results</div>
        <table class="analysis-results">
            <tr>
                <th>Metric</th>
                <th>Value</th>
                <th>Comparison to Market</th>
            </tr>
            <tr>
                <td>Price per m²</td>
                <td>{{ number_format($analysis->price_m2, 2) }} €/m²</td>
                <td>
                    @if($analysis->price_m2 < $analysis->market_price_m2)
                        <span style="color: #065f46;">{{ number_format(($analysis->market_price_m2 - $analysis->price_m2) / $analysis->market_price_m2 * 100, 1) }}% below market</span>
                    @elseif($analysis->price_m2 > $analysis->market_price_m2)
                        <span style="color: #b91c1c;">{{ number_format(($analysis->price_m2 - $analysis->market_price_m2) / $analysis->market_price_m2 * 100, 1) }}% above market</span>
                    @else
                        At market price
                    @endif
                </td>
            </tr>
            <tr>
                <td>Market Price per m²</td>
                <td>{{ number_format($analysis->market_price_m2, 2) }} €/m²</td>
                <td>Reference value</td>
            </tr>
            <tr>
                <td>Price Difference</td>
                <td>{{ $analysis->price_difference_percentage > 0 ? '+' : '' }}{{ number_format($analysis->price_difference_percentage, 2) }}%</td>
                <td>
                    @if($analysis->price_difference_percentage < -10)
                        Significantly below market (excellent)
                    @elseif($analysis->price_difference_percentage < 0)
                        Below market (good)
                    @elseif($analysis->price_difference_percentage <= 5)
                        At market price (fair)
                    @elseif($analysis->price_difference_percentage <= 15)
                        Above market (caution)
                    @else
                        Significantly above market (poor)
                    @endif
                </td>
            </tr>
            <tr>
                <td>Viability Cost</td>
                <td>{{ number_format($analysis->viability_cost, 2) }} €</td>
                <td>{{ number_format($analysis->viability_cost / $terrain->price * 100, 1) }}% of purchase price</td>
            </tr>
            <tr>
                <td>Possible Lots</td>
                <td>{{ $analysis->lots_possible }}</td>
                <td>{{ number_format($terrain->surface_m2 / $analysis->lots_possible, 2) }} m² per lot</td>
            </tr>
            <tr>
                <td>Resale Estimate Range</td>
                <td>{{ number_format($analysis->resale_estimate_min, 2) }} € - {{ number_format($analysis->resale_estimate_max, 2) }} €</td>
                <td>Spread of {{ number_format(($analysis->resale_estimate_max - $analysis->resale_estimate_min) / $analysis->resale_estimate_min * 100, 1) }}%</td>
            </tr>
            <tr>
                <td>Net Margin Estimate</td>
                <td>{{ number_format($analysis->net_margin_estimate, 2) }} €</td>
                <td>{{ number_format($analysis->net_margin_estimate / ($terrain->price + $analysis->viability_cost) * 100, 1) }}% of total investment</td>
            </tr>
            <tr>
                <td>AI Score</td>
                <td>{{ number_format($analysis->ai_score, 1) }} / 100</td>
                <td>
                    <div class="bar-chart">
                        <div class="bar" style="width: {{ $analysis->ai_score }}%;"></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Profitability</td>
                <td>
                    <span class="profitability {{ strtolower($analysis->profitability_label) }}">
                        {{ $analysis->profitability_label }}
                    </span>
                </td>
                <td>
                    @if($analysis->profitability_label == 'Excellent')
                        Top 10% of investments
                    @elseif($analysis->profitability_label == 'Good')
                        Top 30% of investments
                    @elseif($analysis->profitability_label == 'Average')
                        Middle 40% of investments
                    @else
                        Bottom 20% of investments
                    @endif
                </td>
            </tr>
            <tr>
                <td>Overall Risk</td>
                <td>{{ ucfirst($analysis->overall_risk) }}</td>
                <td>
                    @if($analysis->overall_risk == 'low')
                        Minimal risk factors identified
                    @elseif($analysis->overall_risk == 'medium')
                        Some risk factors present
                    @else
                        Significant risk factors present
                    @endif
                </td>
            </tr>
            <tr>
                <td>Recommendation</td>
                <td>{{ $analysis->overall_recommendation === 'strong_buy' ? 'Strong Buy' : ucfirst(str_replace('_', ' ', $analysis->overall_recommendation)) }}</td>
                <td>
                    @if($analysis->overall_recommendation == 'strong_buy')
                        Exceptional investment opportunity
                    @elseif($analysis->overall_recommendation == 'buy')
                        Good investment opportunity
                    @elseif($analysis->overall_recommendation == 'neutral')
                        Average investment opportunity
                    @elseif($analysis->overall_recommendation == 'caution')
                        Proceed with caution
                    @else
                        Not recommended
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Investment Summary</div>
        <table class="analysis-results">
            <tr>
                <th>Metric</th>
                <th>Value</th>
                <th>Notes</th>
            </tr>
            <tr>
                <td>Purchase Price</td>
                <td>{{ number_format($terrain->price, 2) }} €</td>
                <td>Initial investment</td>
            </tr>
            <tr>
                <td>Viability Cost</td>
                <td>{{ number_format($analysis->viability_cost, 2) }} €</td>
                <td>{{ $terrain->viabilised ? 'Already viabilised' : 'Required for development' }}</td>
            </tr>
            <tr>
                <td>Total Investment</td>
                <td>{{ number_format($terrain->price + $analysis->viability_cost, 2) }} €</td>
                <td>Purchase + Viability</td>
            </tr>
            <tr>
                <td>Average Resale Estimate</td>
                <td>{{ number_format(($analysis->resale_estimate_min + $analysis->resale_estimate_max) / 2, 2) }} €</td>
                <td>Expected market value after development</td>
            </tr>
            <tr>
                <td>Net Profit</td>
                <td>{{ number_format($analysis->net_margin_estimate, 2) }} €</td>
                <td>Before taxes and fees</td>
            </tr>
            <tr>
                <td>Return on Investment</td>
                <td>{{ number_format($analysis->profit_margin_percentage, 2) }}%</td>
                <td>
                    @if($analysis->profit_margin_percentage > 30)
                        Excellent ROI
                    @elseif($analysis->profit_margin_percentage > 15)
                        Good ROI
                    @elseif($analysis->profit_margin_percentage > 5)
                        Average ROI
                    @else
                        Poor ROI
                    @endif
                </td>
            </tr>
            <tr>
                <td>Estimated Time to Develop</td>
                <td>{{ $terrain->viabilised ? '3-6 months' : '6-12 months' }}</td>
                <td>{{ $terrain->viabilised ? 'Faster timeline due to existing viability' : 'Includes viability process' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Market Comparison</div>
        <p>This terrain compared to average properties in {{ $terrain->city }}, {{ $terrain->zip_code }}:</p>

        <table class="comparison-table">
            <tr>
                <th>Metric</th>
                <th>This Terrain</th>
                <th>Market Average</th>
                <th>Difference</th>
            </tr>
            <tr>
                <td class="metric">Price per m²</td>
                <td>{{ number_format($analysis->price_m2, 2) }} €</td>
                <td>{{ number_format($analysis->market_price_m2, 2) }} €</td>
                <td class="{{ $analysis->price_m2 < $analysis->market_price_m2 ? 'better' : 'worse' }}">
                    {{ number_format(abs(($analysis->price_m2 - $analysis->market_price_m2) / $analysis->market_price_m2 * 100), 1) }}%
                    {{ $analysis->price_m2 < $analysis->market_price_m2 ? 'cheaper' : 'more expensive' }}
                </td>
            </tr>
            <tr>
                <td class="metric">Surface Area</td>
                <td>{{ number_format($terrain->surface_m2, 2) }} m²</td>
                <td>1,000.00 m²</td>
                <td class="{{ $terrain->surface_m2 > 1000 ? 'better' : '' }}">
                    {{ number_format(abs(($terrain->surface_m2 - 1000) / 1000 * 100), 1) }}%
                    {{ $terrain->surface_m2 > 1000 ? 'larger' : 'smaller' }}
                </td>
            </tr>
            <tr>
                <td class="metric">ROI Potential</td>
                <td>{{ number_format($analysis->profit_margin_percentage, 2) }}%</td>
                <td>15.00%</td>
                <td class="{{ $analysis->profit_margin_percentage > 15 ? 'better' : 'worse' }}">
                    {{ number_format(abs(($analysis->profit_margin_percentage - 15) / 15 * 100), 1) }}%
                    {{ $analysis->profit_margin_percentage > 15 ? 'better' : 'worse' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>This report was generated by Investerra Pro. For more information, visit investerra.com</p>
        <p>© {{ date('Y') }} Investerra. All rights reserved.</p>
        <p>Pro Plan Report</p>
    </div>
</body>
</html>
