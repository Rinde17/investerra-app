<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terrain Analysis Report - Expert</title>
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
            border-bottom: 3px solid #4338ca;
            padding-bottom: 15px;
        }
        .logo {
            text-align: center;
            margin-bottom: 15px;
        }
        .logo img {
            max-width: 250px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .report-title {
            font-size: 24px;
            margin: 10px 0;
        }
        .report-subtitle {
            font-size: 18px;
            color: #666;
            margin-bottom: 5px;
        }
        .report-date {
            font-size: 14px;
            color: #666;
        }
        .section {
            margin-bottom: 40px;
        }
        .section-title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
            border-bottom: 2px solid #4338ca;
            padding-bottom: 8px;
            color: #4338ca;
        }
        .terrain-info {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .terrain-info-item {
            width: 50%;
            margin-bottom: 15px;
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
            padding: 12px;
            text-align: left;
        }
        .analysis-results th {
            background-color: #4338ca;
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
            background-color: #4338ca;
            border-radius: 10px;
        }
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .comparison-table th, .comparison-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        .comparison-table th {
            background-color: #4338ca;
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
        .fiscal-analysis {
            background-color: #f3f4f6;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .fiscal-analysis h3 {
            color: #4338ca;
            margin-top: 0;
        }
        .fiscal-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .fiscal-table th, .fiscal-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .fiscal-table th {
            background-color: #4338ca;
            color: white;
        }
        .fiscal-table tr:nth-child(even) {
            background-color: #ffffff;
        }
        .risk-assessment {
            margin-top: 20px;
        }
        .risk-item {
            margin-bottom: 15px;
        }
        .risk-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .risk-level {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: bold;
        }
        .risk-low {
            background-color: #d1fae5;
            color: #065f46;
        }
        .risk-medium {
            background-color: #fef3c7;
            color: #92400e;
        }
        .risk-high {
            background-color: #fee2e2;
            color: #b91c1c;
        }
    </style>
</head>
<body>
    <div class="watermark">LANDANALYSIS EXPERT</div>

    <div class="header">
        <div class="logo">
            <img src="{{ asset('assets/logos/full-logo-light-no-bg.png') }}" alt="LandAnalysis Logo">
        </div>
        <div class="report-title">Terrain Analysis Report</div>
        <div class="report-subtitle">Expert Edition</div>
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
        <div class="section-title">Executive Summary</div>
        <p>
            This expert analysis report provides a comprehensive evaluation of the investment potential for the terrain located at {{ $terrain->city }}, {{ $terrain->zip_code }}.
            Based on our advanced algorithms and market data, this terrain has been assigned an AI Score of <strong>{{ number_format($analysis->ai_score, 1) }}</strong> out of 100,
            indicating a <strong>{{ $analysis->profitability_label }}</strong> investment opportunity.
        </p>
        <p>
            The terrain, priced at {{ number_format($terrain->price, 2) }} €, offers a potential net margin of {{ number_format($analysis->net_margin_estimate, 2) }} €,
            representing a return on investment of {{ number_format(($analysis->net_margin_estimate / $terrain->price) * 100, 2) }}%.
            This terrain is {{ $terrain->viabilised ? 'already viabilised' : 'not yet viabilised, requiring an estimated ' . number_format($analysis->viability_cost, 2) . ' € for viability work' }}.
        </p>
        <p>
            The analysis suggests that the terrain could be divided into {{ $analysis->lots_possible }} lots, with an estimated resale value between
            {{ number_format($analysis->resale_estimate_min, 2) }} € and {{ number_format($analysis->resale_estimate_max, 2) }} €.
        </p>
    </div>

    <div class="section">
        <div class="section-title">Detailed Analysis Results</div>
        <table class="analysis-results">
            <tr>
                <th>Metric</th>
                <th>Value</th>
                <th>Comparison to Market</th>
                <th>Assessment</th>
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
                <td>
                    @if($analysis->price_m2 < $analysis->market_price_m2 * 0.9)
                        Excellent value
                    @elseif($analysis->price_m2 < $analysis->market_price_m2)
                        Good value
                    @elseif($analysis->price_m2 <= $analysis->market_price_m2 * 1.1)
                        Fair value
                    @else
                        Overpriced
                    @endif
                </td>
            </tr>
            <tr>
                <td>Market Price per m²</td>
                <td>{{ number_format($analysis->market_price_m2, 2) }} €/m²</td>
                <td>Reference value</td>
                <td>Based on recent sales data</td>
            </tr>
            <tr>
                <td>Price Difference</td>
                <td>{{ $analysis->price_difference_percentage > 0 ? '+' : '' }}{{ number_format($analysis->price_difference_percentage, 2) }}%</td>
                <td>
                    @if($analysis->price_difference_percentage < -10)
                        Significantly below market
                    @elseif($analysis->price_difference_percentage < 0)
                        Below market
                    @elseif($analysis->price_difference_percentage <= 5)
                        At market price
                    @elseif($analysis->price_difference_percentage <= 15)
                        Above market
                    @else
                        Significantly above market
                    @endif
                </td>
                <td>
                    @if($analysis->price_difference_percentage < -10)
                        Excellent purchase opportunity
                    @elseif($analysis->price_difference_percentage < 0)
                        Good purchase opportunity
                    @elseif($analysis->price_difference_percentage <= 5)
                        Fair purchase
                    @elseif($analysis->price_difference_percentage <= 15)
                        Consider negotiating
                    @else
                        Overpriced, negotiate or avoid
                    @endif
                </td>
            </tr>
            <tr>
                <td>Viability Cost</td>
                <td>{{ number_format($analysis->viability_cost, 2) }} €</td>
                <td>{{ number_format($analysis->viability_cost / $terrain->price * 100, 1) }}% of purchase price</td>
                <td>
                    @if($terrain->viabilised)
                        Already viabilised
                    @elseif($analysis->viability_cost / $terrain->price < 0.1)
                        Low viability cost
                    @elseif($analysis->viability_cost / $terrain->price < 0.2)
                        Moderate viability cost
                    @else
                        High viability cost
                    @endif
                </td>
            </tr>
            <tr>
                <td>Possible Lots</td>
                <td>{{ $analysis->lots_possible }}</td>
                <td>{{ number_format($terrain->surface_m2 / $analysis->lots_possible, 2) }} m² per lot</td>
                <td>
                    @if($terrain->surface_m2 / $analysis->lots_possible > 800)
                        Large lots
                    @elseif($terrain->surface_m2 / $analysis->lots_possible > 500)
                        Medium lots
                    @else
                        Small lots
                    @endif
                </td>
            </tr>
            <tr>
                <td>Resale Estimate Range</td>
                <td>{{ number_format($analysis->resale_estimate_min, 2) }} € - {{ number_format($analysis->resale_estimate_max, 2) }} €</td>
                <td>Spread of {{ number_format(($analysis->resale_estimate_max - $analysis->resale_estimate_min) / $analysis->resale_estimate_min * 100, 1) }}%</td>
                <td>
                    @if(($analysis->resale_estimate_max - $analysis->resale_estimate_min) / $analysis->resale_estimate_min < 0.1)
                        Low volatility
                    @elseif(($analysis->resale_estimate_max - $analysis->resale_estimate_min) / $analysis->resale_estimate_min < 0.2)
                        Moderate volatility
                    @else
                        High volatility
                    @endif
                </td>
            </tr>
            <tr>
                <td>Net Margin Estimate</td>
                <td>{{ number_format($analysis->net_margin_estimate, 2) }} €</td>
                <td>{{ number_format($analysis->net_margin_estimate / ($terrain->price + $analysis->viability_cost) * 100, 1) }}% of total investment</td>
                <td>
                    @if($analysis->net_margin_estimate / ($terrain->price + $analysis->viability_cost) > 0.3)
                        Excellent margin
                    @elseif($analysis->net_margin_estimate / ($terrain->price + $analysis->viability_cost) > 0.2)
                        Good margin
                    @elseif($analysis->net_margin_estimate / ($terrain->price + $analysis->viability_cost) > 0.1)
                        Average margin
                    @else
                        Low margin
                    @endif
                </td>
            </tr>
            <tr>
                <td>AI Score</td>
                <td>{{ number_format($analysis->ai_score, 1) }} / 100</td>
                <td>
                    <div class="bar-chart">
                        <div class="bar" style="width: {{ $analysis->ai_score }}%;"></div>
                    </div>
                </td>
                <td>
                    @if($analysis->ai_score >= 80)
                        Top 10% of investments
                    @elseif($analysis->ai_score >= 60)
                        Top 30% of investments
                    @elseif($analysis->ai_score >= 40)
                        Average investment
                    @else
                        Below average investment
                    @endif
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
                <td>
                    @if($analysis->profitability_label == 'Excellent')
                        Highly recommended
                    @elseif($analysis->profitability_label == 'Good')
                        Recommended
                    @elseif($analysis->profitability_label == 'Average')
                        Consider with caution
                    @else
                        Not recommended
                    @endif
                </td>
            </tr>
            <tr>
                <td>Overall Risk</td>
                <td>{{ ucfirst($analysis->overall_risk) }}</td>
                <td>
                    @if($analysis->overall_risk == 'low')
                        Minimal risk factors
                    @elseif($analysis->overall_risk == 'medium')
                        Some risk factors present
                    @else
                        Significant risk factors
                    @endif
                </td>
                <td>
                    @if($analysis->overall_risk == 'low')
                        Safe investment
                    @elseif($analysis->overall_risk == 'medium')
                        Proceed with caution
                    @else
                        Thorough risk mitigation required
                    @endif
                </td>
            </tr>
            <tr>
                <td>Recommendation</td>
                <td>{{ $analysis->overall_recommendation === 'strong_buy' ? 'Strong Buy' : ucfirst(str_replace('_', ' ', $analysis->overall_recommendation)) }}</td>
                <td>
                    @if($analysis->overall_recommendation == 'strong_buy')
                        Exceptional opportunity
                    @elseif($analysis->overall_recommendation == 'buy')
                        Good opportunity
                    @elseif($analysis->overall_recommendation == 'neutral')
                        Average opportunity
                    @elseif($analysis->overall_recommendation == 'caution')
                        Proceed with caution
                    @else
                        Not recommended
                    @endif
                </td>
                <td>
                    @if($analysis->overall_recommendation == 'strong_buy')
                        Act quickly
                    @elseif($analysis->overall_recommendation == 'buy')
                        Recommended purchase
                    @elseif($analysis->overall_recommendation == 'neutral')
                        Consider alternatives
                    @elseif($analysis->overall_recommendation == 'caution')
                        Negotiate or reconsider
                    @else
                        Look for better opportunities
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
            <tr>
                <td>Estimated Annual Return</td>
                <td>{{ number_format($analysis->profit_margin_percentage / ($terrain->viabilised ? 0.375 : 0.75), 2) }}%</td>
                <td>Annualized based on development timeline</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Fiscal Analysis</div>
        <div class="fiscal-analysis">
            <h3>Tax Implications</h3>
            <p>
                This section provides an analysis of the tax implications for this investment based on different holding structures.
                The calculations are estimates and should be verified with a tax professional.
            </p>

            <table class="fiscal-table">
                <tr>
                    <th>Holding Structure</th>
                    <th>Gross Profit</th>
                    <th>Estimated Tax</th>
                    <th>Net Profit After Tax</th>
                    <th>Effective Tax Rate</th>
                </tr>
                <tr>
                    <td>Personal (IR)</td>
                    <td>{{ number_format($analysis->net_margin_estimate, 2) }} €</td>
                    <td>{{ number_format($analysis->net_margin_estimate * 0.3, 2) }} €</td>
                    <td>{{ number_format($analysis->net_margin_estimate * 0.7, 2) }} €</td>
                    <td>30%</td>
                </tr>
                <tr>
                    <td>SCI (IR)</td>
                    <td>{{ number_format($analysis->net_margin_estimate, 2) }} €</td>
                    <td>{{ number_format($analysis->net_margin_estimate * 0.3, 2) }} €</td>
                    <td>{{ number_format($analysis->net_margin_estimate * 0.7, 2) }} €</td>
                    <td>30%</td>
                </tr>
                <tr>
                    <td>SCI + Holding (IS)</td>
                    <td>{{ number_format($analysis->net_margin_estimate, 2) }} €</td>
                    <td>{{ number_format($analysis->net_margin_estimate * 0.25, 2) }} €</td>
                    <td>{{ number_format($analysis->net_margin_estimate * 0.75, 2) }} €</td>
                    <td>25%</td>
                </tr>
                <tr>
                    <td>SARL (IS)</td>
                    <td>{{ number_format($analysis->net_margin_estimate, 2) }} €</td>
                    <td>{{ number_format($analysis->net_margin_estimate * 0.25, 2) }} €</td>
                    <td>{{ number_format($analysis->net_margin_estimate * 0.75, 2) }} €</td>
                    <td>25%</td>
                </tr>
            </table>

            <h3>Recommended Structure</h3>
            <p>
                @if($analysis->net_margin_estimate > 100000)
                    For this high-value investment, a <strong>SCI + Holding</strong> structure is recommended to optimize tax efficiency.
                    This structure allows for better tax planning and potential reinvestment of profits.
                @elseif($analysis->net_margin_estimate > 50000)
                    For this medium-value investment, a <strong>SARL</strong> structure may be appropriate if you plan to reinvest profits.
                    Otherwise, a simple <strong>SCI</strong> structure could be sufficient.
                @else
                    For this smaller investment, a <strong>Personal</strong> or simple <strong>SCI</strong> structure is likely sufficient,
                    as the tax advantages of more complex structures may not outweigh their administrative costs.
                @endif
            </p>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Risk Assessment</div>
        <p>
            This section evaluates potential risks associated with this investment. Each risk is rated on a scale from Low to High.
        </p>

        <div class="risk-assessment">
            <div class="risk-item">
                <div class="risk-title">Market Risk</div>
                <span class="risk-level {{ $analysis->market_price_m2 > $analysis->price_m2 * 1.2 ? 'risk-high' : ($analysis->market_price_m2 > $analysis->price_m2 * 1.1 ? 'risk-medium' : 'risk-low') }}">
                    {{ $analysis->market_price_m2 > $analysis->price_m2 * 1.2 ? 'High' : ($analysis->market_price_m2 > $analysis->price_m2 * 1.1 ? 'Medium' : 'Low') }}
                </span>
                <p>
                    @if($analysis->market_price_m2 > $analysis->price_m2 * 1.2)
                        The current market price is significantly higher than the purchase price, which could indicate potential market volatility or unrealistic resale expectations.
                    @elseif($analysis->market_price_m2 > $analysis->price_m2 * 1.1)
                        The market price is moderately higher than the purchase price, presenting a balanced risk profile.
                    @else
                        The purchase price is close to or below market value, reducing market risk.
                    @endif
                </p>
            </div>

            <div class="risk-item">
                <div class="risk-title">Development Risk</div>
                <span class="risk-level {{ $terrain->viabilised ? 'risk-low' : ($analysis->viability_cost > $terrain->price * 0.2 ? 'risk-high' : 'risk-medium') }}">
                    {{ $terrain->viabilised ? 'Low' : ($analysis->viability_cost > $terrain->price * 0.2 ? 'High' : 'Medium') }}
                </span>
                <p>
                    @if($terrain->viabilised)
                        The terrain is already viabilised, significantly reducing development risks.
                    @elseif($analysis->viability_cost > $terrain->price * 0.2)
                        High viability costs relative to purchase price increase development risk. Careful planning and budgeting are essential.
                    @else
                        Moderate development risk due to viability requirements. Standard due diligence should be sufficient.
                    @endif
                </p>
            </div>

            <div class="risk-item">
                <div class="risk-title">Regulatory Risk</div>
                <span class="risk-level risk-medium">Medium</span>
                <p>
                    Standard regulatory risks apply. We recommend verifying local zoning laws, building permits, and environmental regulations before proceeding.
                </p>
            </div>

            <div class="risk-item">
                <div class="risk-title">Liquidity Risk</div>
                <span class="risk-level {{ $analysis->lots_possible > 3 ? 'risk-low' : ($analysis->lots_possible > 1 ? 'risk-medium' : 'risk-high') }}">
                    {{ $analysis->lots_possible > 3 ? 'Low' : ($analysis->lots_possible > 1 ? 'Medium' : 'High') }}
                </span>
                <p>
                    @if($analysis->lots_possible > 3)
                        Multiple potential lots increase liquidity as they can be sold separately.
                    @elseif($analysis->lots_possible > 1)
                        Limited number of lots presents moderate liquidity risk.
                    @else
                        Single lot presents higher liquidity risk as the entire property must be sold to a single buyer.
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Recommendations</div>
        <p>
            Based on our comprehensive analysis, we provide the following recommendations for this investment:
        </p>

        <ul>
            @if($analysis->profitability_label == 'Excellent' || $analysis->profitability_label == 'Good')
                <li><strong>Investment Decision:</strong> This terrain represents a {{ strtolower($analysis->profitability_label) }} investment opportunity and is recommended for acquisition.</li>
            @else
                <li><strong>Investment Decision:</strong> This terrain represents a {{ strtolower($analysis->profitability_label) }} investment opportunity. Consider negotiating a lower price or exploring alternative investments.</li>
            @endif

            @if(!$terrain->viabilised)
                <li><strong>Development Strategy:</strong> Budget {{ number_format($analysis->viability_cost, 2) }} € for viability work. Consider phasing the development to manage cash flow.</li>
            @endif

            @if($analysis->lots_possible > 1)
                <li><strong>Division Strategy:</strong> Divide the terrain into {{ $analysis->lots_possible }} lots of approximately {{ number_format($terrain->surface_m2 / $analysis->lots_possible, 2) }} m² each to maximize return.</li>
            @endif

            <li><strong>Pricing Strategy:</strong> Target a selling price of {{ number_format(($analysis->resale_estimate_min + $analysis->resale_estimate_max) / 2, 2) }} € ({{ number_format(($analysis->resale_estimate_min + $analysis->resale_estimate_max) / 2 / $terrain->surface_m2, 2) }} €/m²).</li>

            @if($analysis->net_margin_estimate > 100000)
                <li><strong>Tax Strategy:</strong> Consider establishing a SCI + Holding structure to optimize tax efficiency for this high-value investment.</li>
            @endif
        </ul>
    </div>

    <div class="footer">
        <p>This report was generated by LandAnalysis Expert. For more information, visit landanalysis.com</p>
        <p>© {{ date('Y') }} LandAnalysis. All rights reserved.</p>
        <p>Expert Plan Report</p>
    </div>
</body>
</html>
