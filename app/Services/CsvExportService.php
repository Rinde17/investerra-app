<?php

namespace App\Services;

use App\Models\Terrain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExportService
{
    /**
     * Export the terrain analysis as CSV.
     *
     * @param Terrain $terrain The terrain with its analysis
     * @return StreamedResponse|RedirectResponse The CSV response or a redirect
     */
    public function exportCsv(Terrain $terrain): StreamedResponse|RedirectResponse
    {
        // Ensure analysis is loaded
        if (!$terrain->relationLoaded('analysis')) {
            $terrain->load('analysis');
        }

        // Check if analysis exists
        if (!$terrain->analysis) {
            return Redirect::route('terrains.show', $terrain)
                ->with('error', 'No analysis exists for this terrain.');
        }

        // Prepare CSV data
        $data = $this->prepareData($terrain);

        // Generate CSV
        $callback = $this->generateCsvCallback($data);

        // Generate a filename
        $filename = $this->generateFilename($terrain);

        // Return the CSV as a download
        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Prepare data for CSV export.
     *
     * @param Terrain $terrain The terrain with its analysis
     * @return array The prepared data
     */
    private function prepareData(Terrain $terrain): array
    {
        $analysis = $terrain->analysis;

        return [
            $terrain->id,
            $terrain->title,
            $terrain->surface_m2,
            $terrain->price,
            $terrain->city,
            $terrain->zip_code,
            $terrain->viabilised ? 'Yes' : 'No',
            $analysis->price_m2,
            $analysis->market_price_m2,
            $analysis->viability_cost,
            $analysis->lots_possible,
            $analysis->resale_estimate_min,
            $analysis->resale_estimate_max,
            $analysis->net_margin_estimate,
            $analysis->ai_score,
            $analysis->profitability_label,
            $analysis->analyzed_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Generate a callback function for CSV generation.
     *
     * @param array $data The data to include in the CSV
     * @return \Closure The callback function
     */
    private function generateCsvCallback(array $data): \Closure
    {
        return function() use ($data) {
            $headers = [
                'Terrain ID',
                'Title',
                'Surface (m²)',
                'Price',
                'City',
                'ZIP Code',
                'Viabilised',
                'Price/m²',
                'Market Price/m²',
                'Viability Cost',
                'Lots Possible',
                'Resale Estimate (Min)',
                'Resale Estimate (Max)',
                'Net Margin Estimate',
                'AI Score',
                'Profitability Label',
                'Analyzed At',
            ];
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, $data);
            fclose($file);
        };
    }

    /**
     * Generate a filename for the CSV.
     *
     * @param Terrain $terrain The terrain
     * @return string The generated filename
     */
    private function generateFilename(Terrain $terrain): string
    {
        return "terrain-analysis-{$terrain->id}-" . date('Y-m-d') . ".csv";
    }
}
