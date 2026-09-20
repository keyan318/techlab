<?php

namespace Tests\Feature;

use App\Services\AttachmentService;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PdfBrokenPageTreeTest extends TestCase
{
    public function test_pdf_whose_page_tree_is_unreadable_still_yields_text(): void
    {
        // A valid Page object whose /Pages parent is a string, so the parser can't walk the tree
        // (this is what a macOS Quartz re-save of a calibre PDF looks like to smalot/pdfparser).
        $stream = 'BT /F1 12 Tf 10 100 Td (Hello focus) Tj ET';
        $objs = [
            "1 0 obj\n<</Type/Catalog/Pages 2 0 R>>\nendobj\n",
            "2 0 obj\n(broken)\nendobj\n",
            "3 0 obj\n<</Type/Page/Parent 2 0 R/MediaBox[0 0 200 200]/Contents 4 0 R/Resources<</Font<</F1 5 0 R>>>>>>\nendobj\n",
            "4 0 obj\n<</Length ".strlen($stream)."\n>>\nstream\n{$stream}\nendstream\nendobj\n",
            "5 0 obj\n<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>\nendobj\n",
        ];
        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objs as $o) {
            $offsets[] = strlen($pdf);
            $pdf .= $o;
        }
        $xref = strlen($pdf);
        $pdf .= "xref\n0 6\n0000000000 65535 f \n";
        foreach ($offsets as $off) {
            $pdf .= sprintf("%010d 00000 n \n", $off);
        }
        $pdf .= "trailer\n<</Size 6/Root 1 0 R>>\nstartxref\n{$xref}\n%%EOF";

        $path = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($path, $pdf);
        $file = new UploadedFile($path, 'book.pdf', 'application/pdf', null, true);

        $result = app(AttachmentService::class)->process([$file]);

        $this->assertStringContainsString('Hello focus', $result['items'][0]['text']);
    }
}
