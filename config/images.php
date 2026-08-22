<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Image Generation Provider
    |--------------------------------------------------------------------------
    |
    | The "Draw Analogy" feature renders an illustration of the analogy Astro
    | used. This selects which provider produces the image. The default is a
    | fully offline SVG comic renderer (no API key required).
    |
    | Available providers:
    |   local_comic  - App\Services\ImageGeneration\LocalComicProvider
    |                  (offline, branded SVG; works with zero credentials)
    |
    | To use a hosted image model later, implement
    | App\Services\ImageGeneration\ImageProviderInterface and add it to the
    | match() in App\Services\ImageGenerationService, then set this value.
    |
    */
    'provider' => env('IMAGE_PROVIDER', 'local_comic'),

    /*
    |--------------------------------------------------------------------------
    | Disk used to store generated illustrations
    |--------------------------------------------------------------------------
    |
    | Must be a disk with a public URL (the "public" disk by default, served via
    | the storage:link symlink). Generated SVGs are written under the
    | "analogies" subdirectory.
    |
    */
    'disk' => env('IMAGE_DISK', 'public'),

];
