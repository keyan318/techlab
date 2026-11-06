<?php

namespace Tests\Unit;

use App\Services\CourseModuleSummaryService;
use Tests\TestCase;

/**
 * The module summaries in the faculty quiz builder are read straight from the lesson files.
 */
class CourseModuleSummaryServiceTest extends TestCase
{
    public function test_a_lesson_gives_its_goal_and_key_terms_with_their_sentences(): void
    {
        $summary = app(CourseModuleSummaryService::class)->forCourse('networking');
        $lesson = collect($summary[1]['lessons'])->firstWhere('label', 'Lesson 2.2');

        $this->assertSame('Protocols & Standards', $lesson['title']);
        $this->assertStringContainsString('tell TCP from UDP', $lesson['objective']);

        $terms = collect($lesson['terms'])->keyBy('term');
        $this->assertStringStartsWith('TCP sets up a connection', $terms['TCP']['explains']);
        $this->assertStringStartsWith('UDP just sends', $terms['UDP']['explains']);
        // One sentence per term, not the whole paragraph.
        $this->assertSame('A standard is a rule everyone agrees on, so devices from different makers can work together.', $terms['standard']['explains']);
    }

    public function test_the_lesson_parser_ignores_bold_words_outside_the_simple_explanation(): void
    {
        $html = '<h1 class="lesson-heading">Lesson 1.1: Hello</h1>'
            .'<p><strong>Learning Objective:</strong> Say hello.</p>'
            .'<h2 class="section-heading">Simple Explanation</h2><p>A <strong>greeting</strong> opens a talk. It is polite.</p>'
            .'<h2 class="section-heading">Rivet Says</h2><p><strong>Rivet</strong> waves.</p>';

        $lesson = app(CourseModuleSummaryService::class)->lesson($html);

        $this->assertSame(['label' => 'Lesson 1.1', 'title' => 'Hello', 'objective' => 'Say hello.', 'terms' => [
            ['term' => 'greeting', 'explains' => 'A greeting opens a talk.'],
        ]], $lesson);
    }

    public function test_a_course_with_no_lessons_has_no_summary(): void
    {
        $this->assertSame([], app(CourseModuleSummaryService::class)->forCourse('no-such-course'));
    }
}
