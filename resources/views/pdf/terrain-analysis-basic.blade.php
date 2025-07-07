<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terrain Analysis Report</title>
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
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4f46e5;
        }
        .report-title {
            font-size: 20px;
            margin: 10px 0;
        }
        .report-date {
            font-size: 14px;
            color: #666;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
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
            padding: 8px;
            text-align: left;
        }
        .analysis-results th {
            background-color: #f2f2f2;
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
    </style>
</head>
<body>
    <div class="watermark">INVESTERRA</div>

    <div class="header">
        <div class="logo">INVESTERRA</div>
        <div class="report-title">Terrain Analysis Report</div>
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
        </div>
    </div>

    <div class="section">
        <div class="section-title">Analysis Results</div>
        <table class="analysis-results">
            <tr>
                <th>Metric</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>Price per m²</td>
                <td>{{ number_format($analysis->price_m2, 2) }} €/m²</td>
            </tr>
            <tr>
                <td>Market Price per m²</td>
                <td>{{ number_format($analysis->market_price_m2, 2) }} €/m²</td>
            </tr>
            <tr>
                <td>Viability Cost</td>
                <td>{{ number_format($analysis->viability_cost, 2) }} €</td>
            </tr>
            <tr>
                <td>Possible Lots</td>
                <td>{{ $analysis->lots_possible }}</td>
            </tr>
            <tr>
                <td>Resale Estimate (Min)</td>
                <td>{{ number_format($analysis->resale_estimate_min, 2) }} €</td>
            </tr>
            <tr>
                <td>Resale Estimate (Max)</td>
                <td>{{ number_format($analysis->resale_estimate_max, 2) }} €</td>
            </tr>
            <tr>
                <td>Net Margin Estimate</td>
                <td>{{ number_format($analysis->net_margin_estimate, 2) }} €</td>
            </tr>
            <tr>
                <td>AI Score</td>
                <td>{{ number_format($analysis->ai_score, 1) }} / 100</td>
            </tr>
            <tr>
                <td>Profitability</td>
                <td>
                    <span class="profitability {{ strtolower($analysis->profitability_label) }}">
                        {{ $analysis->profitability_label }}
                    </span>
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
            </tr>
            <tr>
                <td>Purchase Price</td>
                <td>{{ number_format($terrain->price, 2) }} €</td>
            </tr>
            <tr>
                <td>Viability Cost</td>
                <td>{{ number_format($analysis->viability_cost, 2) }} €</td>
            </tr>
            <tr>
                <td>Total Investment</td>
                <td>{{ number_format($terrain->price + $analysis->viability_cost, 2) }} €</td>
            </tr>
            <tr>
                <td>Average Resale Estimate</td>
                <td>{{ number_format(($analysis->resale_estimate_min + $analysis->resale_estimate_max) / 2, 2) }} €</td>
            </tr>
            <tr>
                <td>Net Profit</td>
                <td>{{ number_format($analysis->net_margin_estimate, 2) }} €</td>
            </tr>
            <tr>
                <td>Return on Investment</td>
                <td>{{ number_format(($analysis->net_margin_estimate / $terrain->price) * 100, 2) }}%</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>This report was generated by Investerra. For more information, visit investerra.com</p>
        <p>© {{ date('Y') }} Investerra. All rights reserved.</p>
        <p>Free Plan Report</p>
    </div>
</body>
</html>
