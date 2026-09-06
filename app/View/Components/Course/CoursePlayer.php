<?php

namespace App\View\Components\Course;

use Illuminate\View\Component;
use Illuminate\View\View;

class CoursePlayer extends Component
{
    public array $course;

    public function __construct(array $course)
    {
        $this->course = $course;
    }

    public function render(): View
    {
        return view('components.course-player');
    }
}
