<?php

namespace Tests\Unit;

use App\Services\Infographic\Providers\LocalInfographicArtProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * The offline space-art generator must always produce a valid, on-brand SVG
 * from a slide's structured data — and never inject raw model text.
 */
class LocalInfographicArtProviderTest extends TestCase
{
    public function test_generates_an_svg_reflecting_the_topic(): void
    {
        Storage::fake('public');

        $provider = new LocalInfographicArtProvider();
        $url = $provider->generate([
            'title' => 'Laravel MVC',
            'theme' => 'space_mission_control',
            'type' => 'architecture',
            'keyPoints' => ['Model', 'View', 'Controller'],
        ]);

        $path = Str::after(parse_url($url, PHP_URL_PATH), '/storage/');
        Storage::disk('public')->assertExists($path);

        $svg = Storage::disk('public')->get($path);
        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('Laravel MVC', $svg);
        $this->assertStringContainsString('infographics/', $url);
    }

    public function test_escapes_dangerous_markup_in_titles(): void
    {
        Storage::fake('public');

        $provider = new LocalInfographicArtProvider();
        $url = $provider->generate([
            'title' => '<script>alert(1)</script>',
            'theme' => 'data_planet',
            'type' => 'concept',
            'keyPoints' => ['a & b', 'c "d"'],
        ]);

        $path = Str::after(parse_url($url, PHP_URL_PATH), '/storage/');
        $svg = Storage::disk('public')->get($path);

        $this->assertStringNotContainsString('<script>alert(1)</script>', $svg);
        $this->assertStringContainsString('&lt;script&gt;', $svg);
        $this->assertStringContainsString('&amp;', $svg);
    }

    public function test_returns_null_without_a_title(): void
    {
        $provider = new LocalInfographicArtProvider();
        $this->assertNull($provider->generate(['title' => '']));
    }
}
