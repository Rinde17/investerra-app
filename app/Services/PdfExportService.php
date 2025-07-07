<?php

namespace App\Services;

use App\Models\Terrain;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class PdfExportService
{
    /**
     * Generate a PDF report for the terrain analysis.
     *
     * @param Terrain $terrain The terrain with its analysis
     * @param User $user The current user
     * @return RedirectResponse The PDF response or a redirect
     */
    public function generatePdf(Terrain $terrain, User $user): RedirectResponse
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

        // Determine a template type based on user subscription
        $templateType = $this->determineTemplateType($user);

        // Configure PDF options
        $options = $this->configurePdfOptions();

        // Create Dompdf instance
        $dompdf = new Dompdf($options);

        // Generate HTML content
        $html = $this->generateHtmlContent($terrain, $user, $templateType);

        // Load HTML and render PDF
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Generate filename
        $filename = $this->generateFilename($terrain);

        // PDF as a download
        $dompdf->stream($filename, [
            'Attachment' => true,
        ]);

        exit();
    }

    /**
     * Determine the template type based on user subscription.
     *
     * @param User $user The current user
     * @return string The template type (basic, pro, or expert)
     */
    private function determineTemplateType(User $user): string
    {
        $templateType = 'basic';

        if ($user->canAccess('pdf_expert')) {
            $templateType = 'expert';
        } elseif ($user->canAccess('pdf_pro')) {
            $templateType = 'pro';
        }

        return $templateType;
    }

    /**
     * Configure PDF options.
     *
     * @return Options The configured options
     */
    private function configurePdfOptions(): Options
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        return $options;
    }

    /**
     * Generate HTML content for the PDF.
     *
     * @param Terrain $terrain The terrain with its analysis
     * @param User $user The current user
     * @param string $templateType The template type
     * @return string The generated HTML
     */
    private function generateHtmlContent(Terrain $terrain, User $user, string $templateType): string
    {
        return view("pdf.terrain-analysis-{$templateType}", [
            'terrain' => $terrain,
            'analysis' => $terrain->analysis,
            'user' => $user,
        ])->render();
    }

    /**
     * Generate a filename for the PDF.
     *
     * @param Terrain $terrain The terrain
     * @return string The generated filename
     */
    private function generateFilename(Terrain $terrain): string
    {
        return "terrain-analysis-{$terrain->id}-" . date('Y-m-d') . ".pdf";
    }
}
