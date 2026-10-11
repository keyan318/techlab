<x-mail::layout>
<x-slot:header>
<x-mail::header :url="config('app.url')">
TechLab
</x-mail::header>
</x-slot:header>

# Hi {{ $facultyName }},

**{{ $studentName }}** left the **{{ $examTitle }}** final exam for {{ $courseTitle }} before finishing it, so TechLab ended the exam and marked it as failed.

<x-mail::panel>
**What happened:** {{ $reason }}<br>
**When:** {{ $when }}
</x-mail::panel>

If it was an honest mistake (a dead battery, a pop-up), you can let them try again from the course page.

<x-mail::button :url="$url">
Open the final exam results
</x-mail::button>

Astro and the TechLab crew

<x-slot:footer>
<x-mail::footer>
You got this email because you are the faculty captain of {{ $courseTitle }} on TechLab.
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
