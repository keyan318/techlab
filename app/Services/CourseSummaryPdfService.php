<?php

namespace App\Services;

use App\Models\Crew;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;

/**
 * The course summary as a PDF file, for the faculty quiz builder: every module's lessons, goals
 * and key terms (from CourseModuleSummaryService), laid out like a handout the faculty reads
 * beside the form while writing a quiz or the final exam.
 *
 * Rendering takes a moment, so each PDF is kept on disk and only rebuilt when a lesson changes.
 */
class CourseSummaryPdfService
{
    /** Bump when the PDF's layout changes, so saved copies are rebuilt. */
    private const VERSION = 2;

    private const DIR = 'course-summaries';

    public function __construct(private CourseModuleSummaryService $summaries) {}

    /** Whether this crew's course has any lessons to summarise. */
    public function has(Crew $crew): bool
    {
        return $this->summaries->forCrew($crew) !== [];
    }

    /** The course's display name, e.g. "Networking 2". */
    public function title(Crew $crew): string
    {
        $course = config("course-catalog.{$crew->planet}.courses.{$crew->course_slug}", []);

        return $course['title'] ?? (string) $crew->course_slug;
    }

    /** Absolute path of the PDF, built first if this version of the course hasn't been yet. */
    public function path(Crew $crew): ?string
    {
        $modules = $this->summaries->forCrew($crew);
        if (! $modules) {
            return null;
        }

        $key = CourseProgressService::keyFor((string) $crew->planet, $crew->course_slug);
        $course = config("course-catalog.{$crew->planet}.courses.{$crew->course_slug}", []);
        $data = [
            'title' => $this->title($crew),
            'code' => $course['code'] ?? null,
            'modules' => $modules,
        ];

        $disk = Storage::disk('local');
        $file = self::DIR.'/'.$key.'-'.md5(self::VERSION.json_encode($data)).'.pdf';

        if (! $disk->exists($file)) {
            // Older copies of this course's summary are stale now.
            foreach ($disk->files(self::DIR) as $old) {
                if (str_starts_with(basename($old), $key.'-')) {
                    $disk->delete($old);
                }
            }
            $disk->put($file, $this->render($data));
        }

        return $disk->path($file);
    }

    private function render(array $data): string
    {
        $options = new Options;
        $options->setDefaultFont('DejaVu Sans');
        $options->setIsRemoteEnabled(false);

        $pdf = new Dompdf($options);
        $pdf->loadHtml(view('faculty.course-summary-pdf', $data)->render());
        $pdf->setPaper('A4');
        $pdf->render();

        // "Page 3 of 12" in the bottom margin of every page.
        $canvas = $pdf->getCanvas();
        $font = $pdf->getFontMetrics()->getFont('DejaVu Sans');
        $canvas->page_text($canvas->get_width() - 110, $canvas->get_height() - 34, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, 8, [0.45, 0.48, 0.58]);

        return $pdf->output();
    }
}
