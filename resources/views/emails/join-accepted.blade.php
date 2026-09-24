<x-mail::layout>
<x-slot:header>
<x-mail::header :url="config('app.url')">
TechLab
</x-mail::header>
</x-slot:header>

# You're in, {{ $studentName }}!

{{ $facultyName }} accepted you into **{{ $courseTitle }}**. Here is your course code:

<x-mail::panel>
<span style="font-family: 'Courier New', monospace; font-size: 26px; font-weight: 700; letter-spacing: 6px;">{{ $code }}</span>
</x-mail::panel>

Open TechLab, go to **{{ $planetTitle }}**, and type this code on the {{ $courseTitle }} card to unlock the course.

<x-mail::button :url="$url">
Open {{ $planetTitle }}
</x-mail::button>

See you on board,<br>
Astro and the TechLab crew

<x-slot:footer>
<x-mail::footer>
You got this email because you asked to join {{ $courseTitle }} on TechLab.
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
