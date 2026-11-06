<?php

/*
| Ranks and avatar frames (App\Services\LevelService).
|
| A student's level comes from the XP they have earned (spending never lowers it). Every 10 levels is a new rank;
| 100 and up is Galaxy Legend, which counts a star for every level past 99.
| Colours run dark → base → light → glow. `motion` (0–3) is how lively the rank's badge and frames are.
|
| Every rank has three frames: its free default, and two to buy with XP in the Shop (`a` and `b`), on sale from the
| moment a student reaches that rank. Frame A costs 100 × (rank + 2) XP, frame B 150 × (rank + 2).
*/

return [
    'tiers' => [
        ['key' => 'stardust', 'name' => 'Stardust', 'glyph' => 'star', 'motion' => 0,
            'colors' => ['#2b3357', '#8f9cc4', '#dfe6ff', '#b9c6ff'], 'frames' => ['Twinkle Wreath', 'Sparkle Crown']],
        ['key' => 'comet', 'name' => 'Comet', 'glyph' => 'comet', 'motion' => 1,
            'colors' => ['#0d3a63', '#3fa4f0', '#c4ecff', '#6fd0ff'], 'frames' => ['Ice Trail', 'Tailblazer']],
        ['key' => 'meteor', 'name' => 'Meteor', 'glyph' => 'meteor', 'motion' => 1,
            'colors' => ['#5a1a06', '#f0702a', '#ffd19a', '#ff8a3d'], 'frames' => ['Ember Shard', 'Firefall']],
        ['key' => 'moonwalker', 'name' => 'Moonwalker', 'glyph' => 'moon', 'motion' => 1,
            'colors' => ['#2e3448', '#b8c2d8', '#ffffff', '#dbe4ff'], 'frames' => ['Crescent Guard', 'Lunar Throne']],
        ['key' => 'orbiter', 'name' => 'Orbiter', 'glyph' => 'orbit', 'motion' => 2,
            'colors' => ['#053f43', '#1fc8b8', '#b5fff3', '#45f0d8'], 'frames' => ['Satellite Ring', 'Gravity Well']],
        ['key' => 'nova', 'name' => 'Nova', 'glyph' => 'burst', 'motion' => 2,
            'colors' => ['#5c3a02', '#f5c04a', '#fff1b8', '#ffd45c'], 'frames' => ['Solar Crest', 'Starburst Crown']],
        ['key' => 'nebula', 'name' => 'Nebula', 'glyph' => 'cloud', 'motion' => 2,
            'colors' => ['#3a0f5c', '#b45cf0', '#ffc8f2', '#e07bff'], 'frames' => ['Stardrift Veil', 'Cosmic Bloom']],
        ['key' => 'pulsar', 'name' => 'Pulsar', 'glyph' => 'pulsar', 'motion' => 3,
            'colors' => ['#03384d', '#22d8ff', '#d4fbff', '#5be1ff'], 'frames' => ['Beacon Sweep', 'Neutron Spire']],
        ['key' => 'quasar', 'name' => 'Quasar', 'glyph' => 'quasar', 'motion' => 3,
            'colors' => ['#4d0838', '#ff4fb8', '#fff0fa', '#ff7fd0'], 'frames' => ['Twin Halo', 'Event Horizon']],
        ['key' => 'supernova', 'name' => 'Supernova', 'glyph' => 'supernova', 'motion' => 3,
            'colors' => ['#5c2a00', '#ffb02e', '#fffbe8', '#ffe27a'], 'frames' => ['Flare Wings', 'Stellar Phoenix']],
        ['key' => 'galaxy', 'name' => 'Galaxy Legend', 'glyph' => 'galaxy', 'motion' => 3,
            'colors' => ['#1a0b4d', '#8a6bff', '#f0e8ff', '#7fb8ff'], 'frames' => ['Spiral Throne', 'Infinity Crown']],
    ],

    'levels_per_tier' => 10,

    // What frames A and B cost: price_per_tier × (rank index + 2).
    'frame_prices' => ['a' => 100, 'b' => 150],

    // What one gem is worth in XP at the Shop's exchange. A module has about 5 lessons (172 lessons over 33
    // modules), and each pays 100 XP for finishing it, so a module's lessons pay about 500 XP. Its 10 gems
    // are worth the same again: 10 x 50 = 500 XP, enough for most suits (200-900 XP) or a low-rank frame.
    'xp_per_gem' => 50,
];
