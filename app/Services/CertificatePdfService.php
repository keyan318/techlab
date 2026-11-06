<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\File;

/**
 * The certificate of completion as a PDF file: a clean white A4 page. Self-paced certificates
 * are issued by TechLab; a class's is signed by its faculty member (CourseCertificateController).
 */
class CertificatePdfService
{
    /** The PDF's bytes. */
    public function render(array $data): string
    {
        // Dompdf keeps the metrics of the certificate's fonts here instead of inside vendor/.
        File::ensureDirectoryExists(storage_path('fonts'));

        $options = new Options;
        $options->setDefaultFont('Poppins');
        $options->setIsRemoteEnabled(false);
        $options->setFontDir(storage_path('fonts'));
        $options->setFontCache(storage_path('fonts'));
        // Fonts, the logo and a faculty member's signature are read straight from disk.
        $options->setChroot([resource_path('fonts/certificate'), public_path(), storage_path('app/public')]);

        $pdf = new Dompdf($options);
        $pdf->loadHtml(view('student.course.certificate-pdf', $data)->render());
        $pdf->setPaper('A4');
        $pdf->render();

        return $pdf->output();
    }

    /** "TechLab Certificate - CC102 - Jane Cruz.pdf" */
    public static function fileName(array $data): string
    {
        $name = preg_replace('/[^\pL\pN \-]+/u', '', (string) $data['studentName']);

        return trim('TechLab Certificate - '.($data['courseCode'] ?? $data['courseTitle']).' - '.$name).'.pdf';
    }
}
