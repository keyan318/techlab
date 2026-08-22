<?php

namespace Tests\Unit;

use App\Services\ImageGeneration\LocalComicProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * The default (offline) analogy illustrator. It must always produce a valid,
 * on-brand SVG that reflects the EXACT elements passed in — and never break on
 * special characters in the model's output.
 */
class LocalComicProviderTest extends TestCase
{
    public function test_generates_an_svg_with_the_exact_elements(): void
    {
        Storage::fake('public');

        $provider = new LocalComicProvider();
        $url = $provider->generate([
            'title' => 'An API is like a waiter',
            'elements' => [
                ['name' => 'Customer', 'role' => 'asks for food'],
                ['name' => 'Waiter', 'role' => 'carries the order'],
                ['name' => 'Kitchen', 'role' => 'cooks the food'],
            ],
            'flow' => ['orders', 'delivers order'],
            'caption' => 'The waiter (API) connects the customer and the kitchen.',
        ]);

        $path = Str::after(parse_url($url, PHP_URL_PATH), '/storage/');
        Storage::disk('public')->assertExists($path);

        $svg = Storage::disk('public')->get($path);

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('An API is like a waiter', $svg);
        $this->assertStringContainsString('Customer', $svg);
        $this->assertStringContainsString('Waiter', $svg);
        $this->assertStringContainsString('Kitchen', $svg);
        $this->assertStringContainsString('Astro', $svg);
    }

    public function test_escapes_special_characters_in_element_names(): void
    {
        Storage::fake('public');

        $provider = new LocalComicProvider();
        $url = $provider->generate([
            'title' => 'A <b>tag</b> & an "attribute"',
            'elements' => [
                ['name' => '<script>alert(1)</script>', 'role' => 'x > y & z'],
            ],
            'flow' => [],
            'caption' => 'A & B "quoted"',
        ]);

        $path = Str::after(parse_url($url, PHP_URL_PATH), '/storage/');
        $svg = Storage::disk('public')->get($path);

        // The dangerous markup must be escaped, not injected raw.
        $this->assertStringNotContainsString('<script>alert(1)</script>', $svg);
        $this->assertStringContainsString('&lt;script&gt;', $svg);
        $this->assertStringContainsString('&amp;', $svg);
        $this->assertStringContainsString('&quot;', $svg);
    }

    public function test_handles_empty_elements_gracefully(): void
    {
        Storage::fake('public');

        $provider = new LocalComicProvider();
        $url = $provider->generate([
            'title' => 'No analogy here',
            'elements' => [],
            'flow' => [],
            'caption' => '',
        ]);

        $path = Str::after(parse_url($url, PHP_URL_PATH), '/storage/');
        $svg = Storage::disk('public')->get($path);

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('No analogy here', $svg);
    }
}
