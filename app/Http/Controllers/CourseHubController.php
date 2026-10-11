<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use App\Models\CrewQuizAttempt;
use App\Models\ModuleMaterial;
use App\Services\CourseOverviewService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * A course's hub — the page a student lands on after picking a course, before the lessons.
 *
 * Four tabs share one header: Home (the chapters), Materials (whatever the faculty uploaded),
 * Quiz (the faculty's quizzes) and Crew (the captain and the roster). Membership is already
 * enforced by the `course.member` middleware, so these methods only build what each tab shows.
 */
class CourseHubController extends Controller
{
    public function home(string $slug, string $course): View|RedirectResponse
    {
        return $this->tab('home', $slug, $course, fn (?Crew $crew) => []);
    }

    public function materials(string $slug, string $course): View|RedirectResponse
    {
        return $this->tab('materials', $slug, $course, function (?Crew $crew) {
            $modules = $crew ? $this->modules($crew) : [];

            // Looking at the tab is what marks the materials as seen, so the badge clears.
            if ($crew) {
                DB::table('crew_members')
                    ->where('crew_id', $crew->id)->where('user_id', Auth::id())
                    ->update(['materials_seen_at' => now()]);
            }

            return ['modules' => $modules, 'materialsBadge' => 0];
        });
    }

    public function quiz(string $slug, string $course): View|RedirectResponse
    {
        return $this->tab('quiz', $slug, $course, function (?Crew $crew, array $seen) {
            $quizzes = $crew ? $this->quizzes($crew, $seen['quizzes']) : [];

            // Looking at the tab is what marks the quizzes as seen, so the badge clears.
            if ($crew) {
                DB::table('crew_members')
                    ->where('crew_id', $crew->id)->where('user_id', Auth::id())
                    ->update(['quizzes_seen_at' => now()]);
            }

            return ['quizzes' => $quizzes, 'quizBadge' => 0];
        });
    }

    public function crew(string $slug, string $course): View|RedirectResponse
    {
        return $this->tab('crew', $slug, $course, fn (?Crew $crew) => [
            'captain' => $crew?->faculty,
            'roster' => $crew
                ? $crew->roster()->wherePivot('role', 'student')->orderBy('name')->get()
                : collect(),
        ]);
    }

    /**
     * Every tab shares the same header, so they share the same build: the course data from
     * CourseOverviewService, the crew behind it, and whatever else that one tab needs.
     */
    private function tab(string $tab, string $slug, string $course, callable $extra): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect(route('login'));
        }
        if ((Auth::user()->role ?? 'student') === 'faculty') {
            return redirect(route('faculty.dashboard'));
        }

        $data = CourseOverviewService::build(Auth::user(), $slug, $course);

        abort_if($data === null, 404);

        // The course.member middleware already found the crew for a student; anyone else looks it up.
        // The captain (faculty) loads only on the tabs that show it.
        $crew = request()->attributes->get('crew')
            ?? Crew::where('planet', $slug)->where('course_slug', $course)->first();
        $seen = $crew ? self::seenAt($crew, Auth::id()) : ['quizzes' => null, 'materials' => null];

        // The tab's own badge comes from $extra (0, it was just looked at), so it wins over badges().
        return view("student.course.{$tab}", $data + $extra($crew, $seen) + [
            'tab' => $tab,
            'slug' => $slug,
            'course' => $course,
            'crew' => $crew,
        ] + ($crew ? self::badges($crew, Auth::id(), $seen) : ['quizBadge' => 0, 'materialsBadge' => 0]));
    }

    /** The faculty's learning modules and the files inside them. */
    private function modules(Crew $crew): array
    {
        return $crew->modules()->with('materials')->get()->values()->map(fn ($m, $i) => [
            'number' => str_pad($i + 1, 2, '0', STR_PAD_LEFT),
            'title' => $m->title,
            'description' => $m->description,
            'materials' => $m->materials->map(fn ($f) => [
                'name' => $f->name,
                'ext' => $f->kind,
                'url' => $f->openUrl(),
                'is_link' => $f->isLink(),
                // Everything the in-page preview overlay needs; null for links, which just open directly.
                'previewUrl' => $f->isLink() ? null : $f->previewUrl(),
                'downloadUrl' => $f->isLink() ? null : route('materials.download', $f),
                'googleUrl' => $f->isLink() ? null : $f->googleDocsUrl(),
                'googleEmbedUrl' => $f->isLink() ? null : $f->googleDocsEmbedUrl(),
            ])->all(),
            'material_count' => $m->materials->count(),
        ])->all();
    }

    /** The faculty's published quizzes, with this student's result where they have taken one. */
    private function quizzes(Crew $crew, ?Carbon $seenAt): array
    {
        $attempts = CrewQuizAttempt::where('user_id', Auth::id())->get()->keyBy('crew_quiz_id');

        return $crew->quizzes()->published()->regular()->withCount('questions')->orderBy('id')->get()->values()
            ->map(function ($q, $i) use ($attempts, $seenAt) {
                $a = $attempts->get($q->id);

                return [
                    'id' => $q->id,
                    'number' => str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                    'title' => $q->title,
                    'description' => $q->description,
                    'questions' => $q->questions_count,
                    'minutes' => $q->minutes,
                    'due_at' => $q->due_at,
                    // done: taken it. missed: the deadline passed first. available: still open.
                    'status' => match (true) {
                        $a !== null => 'done',
                        $q->isPastDue() => 'missed',
                        default => 'available',
                    },
                    'is_new' => $a === null && ($seenAt === null || $q->created_at > $seenAt),
                    'score' => $a?->score,
                    'total' => $a?->total ?? $q->questions_count,
                    'percent' => $a?->percent(),
                ];
            })->all();
    }

    /** Published quizzes added since this student last opened the Quiz tab. */
    public static function unseenQuizCount(Crew $crew, int $userId): int
    {
        return self::unseenQuizzes($crew, $userId, self::seenAt($crew, $userId)['quizzes'])->count();
    }

    /** Materials added since this student last opened the Materials tab. */
    public static function unseenMaterialCount(Crew $crew, int $userId): int
    {
        return self::unseenMaterials($crew, self::seenAt($crew, $userId)['materials'])->count();
    }

    /** Both tab badges in one round trip (the database is remote, so every query costs real time). */
    private static function badges(Crew $crew, int $userId, array $seen): array
    {
        $counts = DB::query()
            ->selectSub(self::unseenQuizzes($crew, $userId, $seen['quizzes'])->selectRaw('count(*)'), 'quizzes')
            ->selectSub(self::unseenMaterials($crew, $seen['materials'])->selectRaw('count(*)'), 'materials')
            ->first();

        return ['quizBadge' => (int) $counts->quizzes, 'materialsBadge' => (int) $counts->materials];
    }

    private static function unseenQuizzes(Crew $crew, int $userId, ?Carbon $seenAt): HasMany
    {
        // reorder(): the relation sorts by id, which Postgres rejects inside a bare count(*) subquery.
        return $crew->quizzes()->published()->regular()->reorder()
            ->when($seenAt !== null, fn ($q) => $q->where('crew_quizzes.created_at', '>', $seenAt))
            ->whereNotIn('id', CrewQuizAttempt::where('user_id', $userId)->select('crew_quiz_id'));
    }

    private static function unseenMaterials(Crew $crew, ?Carbon $seenAt): Builder
    {
        return ModuleMaterial::whereHas('module', fn ($q) => $q->where('crew_id', $crew->id))
            ->when($seenAt !== null, fn ($q) => $q->where('module_materials.created_at', '>', $seenAt));
    }

    /** When this student last opened the Quiz and Materials tabs (null = never), in one query. */
    private static function seenAt(Crew $crew, int $userId): array
    {
        $row = DB::table('crew_members')->where('crew_id', $crew->id)->where('user_id', $userId)
            ->first(['quizzes_seen_at', 'materials_seen_at']);

        return [
            'quizzes' => $row?->quizzes_seen_at ? Carbon::parse($row->quizzes_seen_at) : null,
            'materials' => $row?->materials_seen_at ? Carbon::parse($row->materials_seen_at) : null,
        ];
    }
}
