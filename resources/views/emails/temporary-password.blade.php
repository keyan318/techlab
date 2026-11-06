<x-mail::layout>
<x-slot:header>
<x-mail::header :url="config('app.url')">
TechLab
</x-mail::header>
</x-slot:header>

# Hi {{ $name }},

Someone (hopefully you) asked for a new TechLab password. Here is your temporary password:

<x-mail::panel>
<span style="font-family: 'Courier New', monospace; font-size: 24px; font-weight: 700; letter-spacing: 3px;">{{ $temporary }}</span>
</x-mail::panel>

Sign in with it in the next **{{ $minutes }} minutes**. Right after, TechLab will ask you to choose your own new password.

<x-mail::button :url="$url">
Sign in
</x-mail::button>

Didn't ask for this? You can ignore this email. Your current password still works.

See you on board,<br>
Astro and the TechLab crew

<x-slot:footer>
<x-mail::footer>
You got this email because someone typed your address on TechLab's "Forgot password?" page.
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
