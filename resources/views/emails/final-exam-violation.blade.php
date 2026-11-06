<x-mail::layout>
<x-slot:header>
<x-mail::header :url="config('app.url')">
TechLab
</x-mail::header>
</x-slot:header>

# Hi {{ $facultyName }},

@if ($miniBoss)
**{{ $studentName }}** left the Module {{ $miniBoss }} mini boss (**{{ $examTitle }}**) for {{ $courseTitle }} before finishing it, so TechLab ended it as a fail with no XP.
@else
**{{ $studentName }}** left the **{{ $examTitle }}** final exam for {{ $courseTitle }} before finishing it, so TechLab ended the exam and marked it as failed.
@endif

<x-mail::panel>
**What happened:** {{ $reason }}<br>
**When:** {{ $when }}<br>
@if ($miniBoss)
@if ($selfPaced)
They can try again after a wait, once they've studied the module.
@else
They move on to the next module; their result stays recorded.
@endif
@elseif ($banned)
**Strikes:** 2 of 2. This was their second time, so they are **banned** from the final exam.
@elseif ($cheated)
**Strikes:** 1 of 2. They redo the course; if they're caught again, they're banned.
@endif
</x-mail::panel>

@if ($miniBoss)
@elseif ($banned)
They can't take the final again unless you lift the ban from the course page.
@else
If it was an honest mistake (a dead battery, a pop-up), you can let them try again from the course page.
@endif

<x-mail::button :url="$url">
{{ $miniBoss ? 'Open the mini boss results' : 'Open the final exam results' }}
</x-mail::button>

Astro and the TechLab crew

<x-slot:footer>
<x-mail::footer>
@if ($selfPaced)
You got this email because you are a TechLab admin, and admins look after {{ $courseTitle }}'s self-paced learners.
@else
You got this email because you are the faculty captain of {{ $courseTitle }} on TechLab.
@endif
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
