<?php

namespace App\Http\Controllers;

use App\Models\Terrain;
use App\Services\AIAnalysisService;
use App\Services\PdfExportService;
use App\Services\CsvExportService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TerrainAnalysisController extends Controller
{
    use AuthorizesRequests;

    /**
     * @var PdfExportService
     */
    protected PdfExportService $pdfExportService;

    /**
     * @var CsvExportService
     */
    protected CsvExportService $csvExportService;

    /**
     * @var AIAnalysisService
     */
    protected AIAnalysisService $aiAnalysisService;

    /**
     * TerrainAnalysisController constructor.
     *
     * @param PdfExportService $pdfExportService
     * @param CsvExportService $csvExportService
     * @param AIAnalysisService $aiAnalysisService
     */
    public function __construct(
        PdfExportService $pdfExportService,
        CsvExportService $csvExportService,
        AIAnalysisService $aiAnalysisService
    ) {
        $this->pdfExportService = $pdfExportService;
        $this->csvExportService = $csvExportService;
        $this->aiAnalysisService = $aiAnalysisService;
    }

    /**
     * Display the analysis for the specified terrain.
     * @throws AuthorizationException
     */
    public function show(Terrain $terrain): Response
    {
        $this->authorize('view', $terrain);

        $terrain->load('analysis');

        return Inertia::render('Terrains/Analysis/Show', [
            'terrain' => $terrain,
            'analysis' => $terrain->analysis,
        ]);
    }

    /**
     * Generate a PDF report for the terrain analysis.
     * @throws AuthorizationException
     */
    public function generatePdf(Terrain $terrain): RedirectResponse
    {
        $this->authorize('view', $terrain);

        $user = $this->user();

        // Use the PDF export service to generate the PDF
        return $this->pdfExportService->generatePdf($terrain, $user);
    }

    /**
     * Export the terrain analysis as CSV.
     * @throws AuthorizationException
     */
    public function exportCsv(Terrain $terrain): StreamedResponse
    {
        $this->authorize('view', $terrain);

        // Use the CSV export service to generate the CSV
        return $this->csvExportService->exportCsv($terrain);
    }
}
