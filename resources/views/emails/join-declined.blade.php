<x-mail::layout>
<x-slot:header>
<x-mail::header :url="config('app.url')">
TechLab
</x-mail::header>
</x-slot:header>

# Hi {{ $studentName }},

{{ $facultyName }} didn't accept your request to join **{{ $courseTitle }}** this time.

If you think this is a mistake, talk to {{ $facultyName }}, then ask again from the {{ $courseTitle }} card in TechLab.

<x-mail::button :url="$url">
Open {{ $planetTitle }}
</x-mail::button>

Astro and the TechLab crew

<x-slot:footer>
<x-mail::footer>
You got this email because you asked to join {{ $courseTitle }} on TechLab.
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
