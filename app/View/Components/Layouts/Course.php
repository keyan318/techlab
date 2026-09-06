<?php

namespace App\View\Components\Layouts;

use Illuminate\View\Component;
use Illuminate\View\View;

class Course extends Component
{
    /**
     * The course data
     */
    public $course;

    /**
     * The course slug
     */
    public $slug;

    /**
     * Create a new component instance.
     */
    public function __construct($course, $slug)
    {
        $this->course = $course;
        $this->slug = $slug;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.course');
    }
}
