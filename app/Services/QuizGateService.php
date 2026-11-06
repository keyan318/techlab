<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\QuizAnswer;
use App\Models\QuizReview;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * A lesson's hands-on stage ("Code it yourself", "Configure it yourself", "Defend it yourself")
 * opens only once its Knowledge Check is passed: every question answered, at most MAX_MISTAKES wrong.
 *
 * A student who fails reviews their mistakes with Astro (a chat that opens with Astro's answer
 * already written, by the system from the lesson's answer key, not by the AI), then may retry the questions they missed. Retries never earn XP: the first
 * answer's XP is what counts, so the quiz can't be farmed.
 *
 * Lessons finished before this rule existed stay open, so nobody loses progress.
 */
class QuizGateService
{
    public const MAX_MISTAKES = 2;

    /** A retried question is stored with this empty choice until it is answered again. */
    public const REOPENED = '';

    public function __construct(
        protected LessonSourceService $lessonSources,
    ) {}

    /**
     * @return array{required: bool, allowed: bool, total: int, answered: int, mistakes: int, maxMistakes: int, failed: bool, reviewed: bool, conversationId: ?int}
     */
    public function state(User $user, string $course, string $module, string $lesson, ?bool $completed = null): array
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        $html = LessonQuizService::source($module, $lesson, $course);
        $total = $html === null ? 0 : count(LessonQuizService::key($html));

        $state = ['required' => false, 'allowed' => true, 'total' => $total, 'answered' => 0, 'mistakes' => 0,
            'maxMistakes' => self::MAX_MISTAKES, 'failed' => false, 'reviewed' => false, 'conversationId' => null];
        if ($total === 0) {
            return $state;
        }

        // Answers to questions a faculty member has since removed from the quiz don't count.
        $answers = $this->answers($user, $course, $module, $lesson)->where('question', '<=', $total);
        $answered = $answers->count();
        $mistakes = $answers->where('is_correct', false)->count();
        $finished = $answered >= $total;

        $failed = $finished && $mistakes > self::MAX_MISTAKES;

        // The review only matters once the quiz is failed (Astro's review, then the retry), so it is only
        // looked up then. $completed can be passed in by a caller that already knows it (one query fewer).
        $review = $failed ? QuizReview::where(['user_id' => $user->id, 'course' => $course, 'module' => $module, 'lesson' => $lesson])->first() : null;
        $reviewed = $review && $review->mistakes_hash === self::mistakesHash($answers);
        $required = ! ($completed ?? CourseProgressService::isCompleted($user, $module, $lesson, $course));

        return array_merge($state, [
            'required' => $required,
            'allowed' => ! $required || ($finished && $mistakes <= self::MAX_MISTAKES),
            'answered' => $answered,
            'mistakes' => $mistakes,
            'failed' => $failed,
            'reviewed' => $reviewed,
            'conversationId' => $review?->conversation_id,
        ]);
    }

    public function allows(User $user, string $course, string $module, string $lesson): bool
    {
        return $this->state($user, $course, $module, $lesson)['allowed'];
    }

    /**
     * Start (or reopen) the student's mistake review with Astro. Astro's answer is written before
     * the chat opens. Returns the conversation id, or null when there is nothing to review.
     */
    public function review(User $user, string $course, string $module, string $lesson): ?int
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        $state = $this->state($user, $course, $module, $lesson);
        if (! $state['failed']) {
            return null;
        }

        // Same mistakes as last time, and that chat still exists: just reopen it.
        if ($state['reviewed'] && $state['conversationId'] && Conversation::where('user_id', $user->id)->whereKey($state['conversationId'])->exists()) {
            return $state['conversationId'];
        }

        $html = (string) LessonQuizService::source($module, $lesson, $course);
        $key = LessonQuizService::key($html);
        $questions = LessonQuizService::questions($html);
        $answers = $this->answers($user, $course, $module, $lesson)->where('question', '<=', $state['total']);
        $wrong = $answers->where('is_correct', false)->sortBy('question');

        $title = preg_match('/<h1 class="lesson-heading">(.*?)<\/h1>/s', $html, $m) ? trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES)) : '';
        $number = ((int) preg_replace('/\D/', '', $module)).'.'.((int) preg_replace('/\D/', '', $lesson));
        $title = preg_replace('/^Lesson\s+[\d.]+\s*[:\-]\s*/i', '', $title);   // the heading may already start with "Lesson 1.1:"
        $lessonName = 'Lesson '.$number.($title !== '' ? ' ("'.$title.'")' : '');

        // What the student "says": a clear list of what they missed, so the chat makes sense on
        // its own and Astro still has the details for any follow-up question.
        $lines = ["I just took the Knowledge Check for {$lessonName} and got {$wrong->count()} of {$state['total']} wrong. Can you help me understand my mistakes?"];
        $mistakes = [];
        foreach ($wrong as $a) {
            $q = $questions[$a->question] ?? ['prompt' => 'Question '.$a->question, 'options' => []];
            $right = $key[$a->question]['correct'] ?? '?';
            $lines[] = '';
            $lines[] = $q['prompt'];
            $lines[] = '- My answer: '.$a->choice.'. '.($q['options'][$a->choice] ?? '');
            $lines[] = '- Right answer: '.$right.'. '.($q['options'][$right] ?? '');

            $mistakes[] = [
                'prompt' => $q['prompt'],
                'picked' => $a->choice.'. '.($q['options'][$a->choice] ?? ''),
                'right' => $right.'. '.($q['options'][$right] ?? ''),
                'explanation' => trim(html_entity_decode(strip_tags($key[$a->question]['explanation'] ?? ''), ENT_QUOTES)),
            ];
        }
        $userMessage = implode("\n", $lines);

        // Astro's reply is written by the system from the lesson's own answer key, not by the AI:
        // it is instant and always works, however long the lesson is.
        $answer = self::astroReview($mistakes, $state['total'], $user->id + crc32($course.$module.$lesson));

        $sources = $this->lessonSources->resolve([[
            'planet' => $course,
            'module' => 'M'.((int) preg_replace('/\D/', '', $module)),
            'lesson' => 'lesson-'.str_pad((string) (int) preg_replace('/\D/', '', $lesson), 2, '0', STR_PAD_LEFT),
        ]]);

        return DB::transaction(function () use ($user, $course, $module, $lesson, $answers, $userMessage, $answer, $number, $sources) {
            $conversation = Conversation::create(['user_id' => $user->id, 'title' => 'Quiz review: Lesson '.$number]);
            Message::create(['conversation_id' => $conversation->id, 'role' => 'user', 'content' => $userMessage]);
            Message::create(['conversation_id' => $conversation->id, 'role' => 'assistant', 'content' => $answer,
                'sources' => array_map(fn ($s) => ['kind' => 'lesson', 'title' => $s['label']], $sources) ?: null]);

            QuizReview::updateOrCreate(
                ['user_id' => $user->id, 'course' => $course, 'module' => $module, 'lesson' => $lesson],
                ['conversation_id' => $conversation->id, 'mistakes_hash' => self::mistakesHash($answers)],
            );

            return $conversation->id;
        });
    }

    /**
     * Astro's mistake review, in Astro's voice: for each miss, what the student picked, the right
     * answer and the lesson's explanation of it, then a tip. The wording varies (picked by $seed,
     * so reopening the same review reads the same) so it feels written for this student.
     *
     * @param  array<int, array{prompt: string, picked: string, right: string, explanation: string}>  $mistakes
     */
    public static function astroReview(array $mistakes, int $total, int $seed = 0): string
    {
        $pick = fn (array $options, int $i) => $options[abs($seed + $i) % count($options)];
        $n = count($mistakes);

        $out = [$pick([
            "Thanks for showing me, astronaut! I read through your answers. You got {$n} of {$total} wrong, so let's fix them one at a time. 🚀",
            "Good call asking me! I checked your Knowledge Check: {$n} of {$total} slipped past you. Let's go through each one together.",
            "Okay astronaut, I looked at every answer. {$n} of {$total} need another look, and that's totally fine. Mistakes are how we learn the route!",
        ], 0), ''];

        $openers = [
            'You picked **%s**, which sounds close, but it\'s not the one.',
            'You went with **%s**. I see why it looks right, but it doesn\'t fit here.',
            'Your answer was **%s**. That one is a trap a lot of astronauts fall into!',
            '**%s** was your pick, but it misses the key idea.',
        ];
        $rights = [
            'The right answer is **%s**.',
            'The one we want is **%s**.',
            'The correct choice is **%s**.',
        ];

        foreach (array_values($mistakes) as $i => $m) {
            $out[] = '**'.($i + 1).'. '.$m['prompt'].'**';
            $out[] = '';
            $out[] = sprintf($pick($openers, $i), $m['picked']).' '.sprintf($pick($rights, $i), $m['right'])
                .($m['explanation'] !== '' ? ' '.$m['explanation'] : '');
            $out[] = '';
        }

        $out[] = '💡 **Quick tip:** '.$pick([
            'before you pick, cover the choices and try to answer in your own words first. Then find the choice that matches.',
            'read the question twice and underline the key word. Most traps hide in one tiny word.',
            'if two choices look alike, ask yourself what makes them different. That difference is usually the answer.',
        ], $n);
        $out[] = '';
        $out[] = $pick([
            "Read those parts of the lesson again, then hit the **Retry** button. You've got this! 💪",
            "When you're ready, go back and retry the questions you missed. I believe in you, astronaut! ✨",
            "Give the lesson one more quick read, then retry the ones you missed. You're closer than you think! 🌟",
        ], $n + 1);

        return implode("\n", $out);
    }

    /**
     * Reopen the questions the student got wrong, once they have reviewed them with Astro.
     * Returns how many were reopened (0 when a retry isn't allowed yet).
     */
    public function retake(User $user, string $course, string $module, string $lesson): int
    {
        [$module, $lesson] = CourseProgressService::normalize($module, $lesson);
        $state = $this->state($user, $course, $module, $lesson);
        if (! $state['failed'] || ! $state['reviewed']) {
            return 0;
        }

        return QuizAnswer::where(['user_id' => $user->id, 'course' => $course, 'module' => $module, 'lesson' => $lesson, 'is_correct' => false])
            ->where('choice', '!=', self::REOPENED)
            ->update(['choice' => self::REOPENED]);
    }

    /** The student's current answers (reopened questions don't count until answered again). */
    private function answers(User $user, string $course, string $module, string $lesson)
    {
        return QuizAnswer::where(['user_id' => $user->id, 'course' => $course, 'module' => $module, 'lesson' => $lesson])
            ->where('choice', '!=', self::REOPENED)
            ->get(['question', 'choice', 'is_correct']);
    }

    /** Which questions were wrong, and with what answer: a new set of mistakes needs a new review. */
    private static function mistakesHash($answers): string
    {
        return hash('sha256', $answers->where('is_correct', false)->sortBy('question')
            ->map(fn ($a) => $a->question.':'.$a->choice)->implode(','));
    }
}
