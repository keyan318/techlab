<?php

namespace App\Http\Controllers;

use App\Models\CourseModule;
use App\Models\Crew;
use App\Models\ModuleMaterial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Faculty-authored course content: modules and the files (docx, pptx, pdf, ...) inside them.
 * Files live on the private local disk and are only served through download(), which
 * checks the requester belongs to the module's crew.
 */
class CourseModuleController extends Controller
{
    private const MAX_KB = 51200; // 50 MB per file

    private function ownCrew(Crew $crew): Crew
    {
        abort_unless(Auth::check() && (Auth::user()->role ?? 'student') === 'faculty' && $crew->teacher_id === Auth::id(), 403);

        return $crew;
    }

    private function ownModule(CourseModule $module): CourseModule
    {
        $this->ownCrew($module->crew);

        return $module;
    }

    public function store(Request $request, Crew $crew): RedirectResponse
    {
        $this->ownCrew($crew);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $module = $crew->modules()->create($data + ['position' => ($crew->modules()->max('position') ?? 0) + 1]);

        return redirect(route('faculty.course', $crew).'#module-'.$module->id);
    }

    public function update(Request $request, CourseModule $module): RedirectResponse
    {
        $this->ownModule($module)->update($request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]));

        return redirect(route('faculty.course', $module->crew_id).'#module-'.$module->id);
    }

    public function destroy(CourseModule $module): RedirectResponse
    {
        $this->ownModule($module);
        $crewId = $module->crew_id;
        Storage::disk('local')->deleteDirectory($this->dir($module));
        $module->delete();

        return redirect(route('faculty.course', $crewId).'#modules');
    }

    public function storeMaterials(Request $request, CourseModule $module): RedirectResponse
    {
        $this->ownModule($module);
        $request->validate([
            'files' => ['required', 'array', 'max:20'],
            'files.*' => ['file', 'max:'.self::MAX_KB, 'extensions:'.implode(',', array_keys(ModuleMaterial::KINDS))],
        ], [
            'files.*.extensions' => 'Unsupported file type. Use PDF, Word, PowerPoint, Excel, images, ZIP or text.',
            'files.*.max' => 'Each file must be 50 MB or smaller.',
        ]);

        foreach ($request->file('files') as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            $module->materials()->create([
                'name' => mb_substr($file->getClientOriginalName(), 0, 255),
                'ext' => $ext,
                'path' => $file->store($this->dir($module), 'local'),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        return redirect(route('faculty.course', $module->crew_id).'#module-'.$module->id);
    }

    public function destroyMaterial(ModuleMaterial $material): RedirectResponse
    {
        $module = $this->ownModule($material->module);
        Storage::disk('local')->delete($material->path);
        $material->delete();

        return redirect(route('faculty.course', $module->crew_id).'#module-'.$module->id);
    }

    /** Faculty or a member of the module's crew may download. */
    public function download(ModuleMaterial $material): StreamedResponse
    {
        $user = Auth::user();
        abort_unless($user && $user->belongsToCrew($material->module->crew_id), 403);
        abort_unless(Storage::disk('local')->exists($material->path), 404);

        return Storage::disk('local')->download($material->path, $material->name);
    }

    private function dir(CourseModule $module): string
    {
        return 'course-materials/'.$module->crew_id.'/'.$module->id;
    }
}
