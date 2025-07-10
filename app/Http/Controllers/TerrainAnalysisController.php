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
use Illuminate\Support\Facades\Auth;

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

    /**
     * Compare multiple terrain analyses.
     * @throws AuthorizationException
     */
    public function compare(Request $request): Response|RedirectResponse
    {
        $user = $this->user();

        // Check if the user has access to the comparator feature
        if (!$user->canAccess('comparator')) {
            return redirect()->route('settings.subscription')
                ->with('info', 'Your current plan does not include the terrain comparison feature. Please upgrade to access it.');
        }

        // Validate the request
        $validated = $request->validate([
            'terrain_ids' => 'required|array|min:2|max:5',
            'terrain_ids.*' => 'exists:terrains,id'
        ]);

        // Get the terrains with their analyses
        $terrains = Terrain::whereIn('id', $validated['terrain_ids'])
            ->with('analysis')
            ->get();

        // Authorize the user to view each terrain
        foreach ($terrains as $terrain) {
            $this->authorize('view', $terrain);
        }

        return Inertia::render('Terrains/Analysis/Compare', [
            'terrains' => $terrains
        ]);
    }
}
