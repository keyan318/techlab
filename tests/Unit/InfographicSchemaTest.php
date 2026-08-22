<?php

namespace Tests\Unit;

use App\Services\Infographic\InfographicSchema;
use Tests\TestCase;

/**
 * The schema is the gate that NEVER trusts raw model JSON. These tests pin
 * down coercion, cover injection, slide dropping, and the unusable-payload case.
 */
class InfographicSchemaTest extends TestCase
{
    private function schema(): InfographicSchema
    {
        return new InfographicSchema();
    }

    public function test_normalizes_a_valid_plan(): void
    {
        $raw = [
            'title' => 'Laravel MVC',
            'subtitle' => 'Mission Control',
            'theme' => 'space_mission_control',
            'source_summary' => 'Separates concerns.',
            'slides' => [
                ['type' => 'concept', 'title' => 'What is MVC', 'summary' => 's', 'keyPoints' => ['a', 'b']],
                ['type' => 'summary', 'title' => 'Takeaways', 'keyPoints' => ['x']],
            ],
        ];

        $plan = $this->schema()->normalize($raw);

        $this->assertIsArray($plan);
        $this->assertSame('Laravel MVC', $plan['title']);
        $this->assertSame(3, $plan['slide_count']); // cover injected + 2
        $this->assertSame('cover', $plan['slides'][0]['type']);
        $this->assertSame('concept', $plan['slides'][1]['type']);
    }

    public function test_invalid_slide_type_is_coerced_to_concept(): void
    {
        $raw = [
            'title' => 'T',
            'slides' => [['type' => 'banana', 'title' => 'Weird']],
        ];

        $plan = $this->schema()->normalize($raw);

        $this->assertSame('concept', $plan['slides'][1]['type']);
    }

    public function test_slide_without_a_title_is_dropped(): void
    {
        $raw = [
            'title' => 'T',
            'slides' => [
                ['type' => 'concept', 'title' => 'Good'],
                ['type' => 'concept'], // no title
            ],
        ];

        $plan = $this->schema()->normalize($raw);

        // cover + the one valid slide
        $this->assertCount(2, $plan['slides']);
        $this->assertSame('Good', $plan['slides'][1]['title']);
    }

    public function test_cover_is_injected_when_missing(): void
    {
        $raw = [
            'title' => 'My Topic',
            'slides' => [['type' => 'concept', 'title' => 'First']],
        ];

        $plan = $this->schema()->normalize($raw);

        $this->assertSame('cover', $plan['slides'][0]['type']);
        $this->assertSame('My Topic', $plan['slides'][0]['title']);
    }

    public function test_empty_title_gets_a_default(): void
    {
        $raw = ['slides' => [['type' => 'concept', 'title' => 'Only']]];

        $plan = $this->schema()->normalize($raw);

        $this->assertSame('Your Visual Lesson', $plan['title']);
    }

    public function test_unusable_payload_returns_null(): void
    {
        $this->assertNull($this->schema()->normalize(null));
        $this->assertNull($this->schema()->normalize([]));
        $this->assertNull($this->schema()->normalize(['title' => 'x'])); // no slides
        $this->assertNull($this->schema()->normalize(['slides' => [['type' => 'concept']]])); // no title
    }

    public function test_visual_object_is_normalized(): void
    {
        $raw = [
            'title' => 'T',
            'slides' => [['type' => 'analogy', 'title' => 'A', 'visual' => ['required' => true, 'type' => 'illustration', 'prompt' => 'p']]],
        ];

        $plan = $this->schema()->normalize($raw);
        $this->assertSame(true, $plan['slides'][1]['visual']['required']);
        $this->assertSame('illustration', $plan['slides'][1]['visual']['type']);
    }
}
