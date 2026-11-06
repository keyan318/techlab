<?php

/*
| The student's astronaut: a 24x30 pixel-art animal in a spacesuit, built in onboarding (see App\Services\AvatarService).
|
| A look is a handful of choices (animal + coat, suit + colour, patch, helmet + visor).
| It is drawn by stacking layers: the animal's head, then the suit, then the glass helmet. Each layer is rows of
| characters, '.' = empty; every other character is a colour looked up in this order:
|   ink (fixed colours) → the chosen coat (f, F fur; k, K markings) → suit colour (s, S) → patch (p).
| Helmet glass uses the visor colour: 'v' = glass, 'V' = the glass rim, 'x' = a highlight.
| With the helmet on, anything of the head outside the glass (ears, whiskers) is left out.
| Animals and suits marked 'captain' => true are only for faculty (the Captain's own builder); students get the rest.
| A student animal or suit with 'xp' is bought once with XP in the Shop (a ShopPurchase row) before it can be worn;
| the cat and the Explorer suit are free.
| Rows not listed are empty. Keep every row exactly 24 characters (AvatarOnboardingTest checks it).
*/

return [
    'width' => 24,
    'height' => 30,

    'ink' => [
        'o' => '#1b1750', // outline, eyes
        'e' => '#ffffff', // eye shine
        'w' => '#eef1ff', // suit white
        'W' => '#b9c2ea', // suit white shade, whiskers
        'g' => '#8f98c9', // gloves, boots, neck ring
        'G' => '#646c9e',
        'y' => '#f5c04a', // gold trim
        'c' => '#ff9db0', // cheeks, inner ears
        'm' => '#b8465f', // mouth
        'n' => '#e0607e', // nose, tongue
        'b' => '#ffab2e', // beak
        'B' => '#d9822b',
        'u' => '#ffc4cf', // cow muzzle
        'U' => '#e58fa2', // nostrils
        'q' => '#f3e3b5', // horns
        'Q' => '#cdb37a',
        'r' => '#eef1ff', // helmet ring
        'R' => '#b9c2ea',
        'x' => '#ffffff', // helmet highlight
    ],

    // Each animal has its own head and its own coats (fur f/F, markings k/K: stripes, patches, spots, belly, snout).
    'animals' => [
        'cat' => ['label' => 'Cat', 'coats' => [
            'ginger' => ['label' => 'Ginger', 'f' => '#f5a04a', 'F' => '#d9822b', 'k' => '#c4651b', 'K' => '#9e4f12'],
            'grey' => ['label' => 'Grey', 'f' => '#aab0c4', 'F' => '#878da3', 'k' => '#6d7389', 'K' => '#555a6e'],
            'cream' => ['label' => 'Cream', 'f' => '#f6e3c3', 'F' => '#dcc39c', 'k' => '#d2a76e', 'K' => '#b48a52'],
            'midnight' => ['label' => 'Midnight', 'f' => '#5a5370', 'F' => '#463f5c', 'k' => '#3a3449', 'K' => '#2c2738'],
        ], 'rows' => [
            3 => '.......o........o.......',
            4 => '......oco......oco......',
            5 => '......ofcoooooocFo......',
            6 => '......offkfkkfkfFo......',
            7 => '......offfffffffFo......',
            8 => '......offfffffffFo......',
            9 => '......offeffffefFo......',
            10 => '......offoffffofFo......',
            11 => '....WWocfffnnfffcoWW....',
            12 => '......offmffffmfFo......',
            13 => '.....WWoffmmmmfFoWW.....',
            14 => '........oooooooo........',
        ]],
        'dog' => ['label' => 'Dog', 'xp' => 200, 'coats' => [
            'golden' => ['label' => 'Golden', 'f' => '#e8b05c', 'F' => '#c98d3c', 'k' => '#8a5a2b', 'K' => '#6e4420'],
            'chocolate' => ['label' => 'Chocolate', 'f' => '#9a6240', 'F' => '#7a4a2e', 'k' => '#4e2c1d', 'K' => '#3a2015'],
            'spotty' => ['label' => 'Spotty', 'f' => '#f4f4f8', 'F' => '#cfd3e2', 'k' => '#2b2234', 'K' => '#17121e'],
            'husky' => ['label' => 'Husky', 'f' => '#aab3c6', 'F' => '#8a93a8', 'k' => '#4b5266', 'K' => '#373c4d'],
        ], 'rows' => [
            4 => '........oooooooo........',
            5 => '....oooofffffffFoooo....',
            6 => '....okkofffffffFokko....',
            7 => '....okkofffffffFokko....',
            8 => '....okkofeffffeFokko....',
            9 => '....okkofoffffoFokko....',
            10 => '....okkofffooffFokko....',
            11 => '.....okoffcmmcfFoko.....',
            12 => '......ooffmnnmfFoo......',
            13 => '.......offfnnffFo.......',
            14 => '........oooooooo........',
        ]],
        'cow' => ['label' => 'Cow', 'xp' => 400, 'coats' => [
            'classic' => ['label' => 'Classic', 'f' => '#f6f6f8', 'F' => '#cfd3e2', 'k' => '#2b2234', 'K' => '#17121e'],
            'brown' => ['label' => 'Brown', 'f' => '#f6f6f8', 'F' => '#cfd3e2', 'k' => '#7a4a2a', 'K' => '#5a361e'],
            'highland' => ['label' => 'Highland', 'f' => '#c9763a', 'F' => '#a85d29', 'k' => '#8a4a1f', 'K' => '#6c3815'],
        ], 'rows' => [
            3 => '......qq........qq......',
            4 => '.......qooooooooq.......',
            5 => '.......ofkkffkkFo.......',
            6 => '....oooofkkffkkFoooo....',
            7 => '....okkofffffffFokko....',
            8 => '....oooofeffffeFoooo....',
            9 => '.......ofoffffoFo.......',
            10 => '......ouuuuuuuuuuo......',
            11 => '......ouuUuuuuUuuo......',
            12 => '......ouuuuuuuuuuo......',
            13 => '.......ouuuuuuuuo.......',
            14 => '........oooooooo........',
        ]],
        'bird' => ['label' => 'Bird', 'xp' => 300, 'coats' => [
            'sky' => ['label' => 'Sky', 'f' => '#4aa8ff', 'F' => '#2f7fd6', 'k' => '#e3f2ff', 'K' => '#bcd9f5'],
            'sunny' => ['label' => 'Sunny', 'f' => '#ffd23f', 'F' => '#e0ad1c', 'k' => '#fff4c2', 'K' => '#f2dd8a'],
            'cardinal' => ['label' => 'Cardinal', 'f' => '#e8443a', 'F' => '#bd2f27', 'k' => '#ffd6cc', 'K' => '#f0b2a4'],
            'parrot' => ['label' => 'Parrot', 'f' => '#46c35a', 'F' => '#2f9a43', 'k' => '#f5e663', 'K' => '#d9c63e'],
        ], 'rows' => [
            2 => '..........oooo..........',
            3 => '.........okkkko.........',
            4 => '.......oookkkkooo.......',
            5 => '......offfffffffFo......',
            6 => '.....offfffffffffFo.....',
            7 => '.....offfffffffffFo.....',
            8 => '.....offeoffffoefFo.....',
            9 => '.....offooffffoofFo.....',
            10 => '.....ocfffbbbbfffco.....',
            11 => '.....offkkkBBkkkfFo.....',
            12 => '......ofkkkkkkkkFo......',
            13 => '.......offkkkkfFo.......',
            14 => '........oooooooo........',
        ]],
        'pig' => ['label' => 'Pig', 'xp' => 400, 'coats' => [
            'pink' => ['label' => 'Pink', 'f' => '#ffb6c8', 'F' => '#ec8fa9', 'k' => '#f57f9f', 'K' => '#b8465f'],
            'rosy' => ['label' => 'Rosy', 'f' => '#ffcfd9', 'F' => '#f3a9bb', 'k' => '#ff97b2', 'K' => '#c45470'],
            'muddy' => ['label' => 'Muddy', 'f' => '#b9805a', 'F' => '#9a6542', 'k' => '#e88aa0', 'K' => '#8e3e52'],
            'night' => ['label' => 'Night', 'f' => '#4f4660', 'F' => '#3d354c', 'k' => '#f58aa8', 'K' => '#b8465f'],
        ], 'rows' => [
            3 => '......oo........oo......',
            4 => '......oFFooooooFFo......',
            5 => '......oFFffffffFFo......',
            6 => '......offfffffffFo......',
            7 => '......offfffffffFo......',
            8 => '......offeffffefFo......',
            9 => '......ocfoffffofco......',
            10 => '......offkkkkkkfFo......',
            11 => '......offkKkkKkfFo......',
            12 => '......offkkkkkkfFo......',
            13 => '.......offfffffFo.......',
            14 => '........oooooooo........',
        ]],
        // Captain-only animals (faculty pick these in their own onboarding; students never see them).
        // mane k/K around the face f/F, cream muzzle
        'lion' => ['label' => 'Lion', 'captain' => true, 'coats' => [
            'golden' => ['label' => 'Golden', 'f' => '#f5c063', 'F' => '#d99a3a', 'k' => '#c4651b', 'K' => '#9e4f12'],
            'shadow' => ['label' => 'Shadow mane', 'f' => '#e8b05c', 'F' => '#c98d3c', 'k' => '#4e2c1d', 'K' => '#3a2015'],
            'snow' => ['label' => 'Snow', 'f' => '#f6efe0', 'F' => '#d8ccb4', 'k' => '#e3cf9e', 'K' => '#bfa36c'],
        ], 'rows' => [
            2 => '........oooooooo........',
            3 => '......ookkkkkkkkKo......',
            4 => '.....okkocoooocokKo.....',
            5 => '....okkkoffffffokKKo....',
            6 => '....okkofffffffFokKo....',
            7 => '....okkokkffffkkokKo....',
            8 => '....okkofeffffeFokKo....',
            9 => '....okkofoffffoFokKo....',
            10 => '....okkofqqnnqqfokko....',
            11 => '....okkoqqmqqmqqokko....',
            12 => '....okkooqqmmqqookKo....',
            13 => '.....okkoqqqqqqokKo.....',
            14 => '......okkkooookKko......',
        ]],
        // fur f/F, face k/K under a heavy brow
        'gorilla' => ['label' => 'Gorilla', 'captain' => true, 'coats' => [
            'silverback' => ['label' => 'Silverback', 'f' => '#3a3449', 'F' => '#2c2738', 'k' => '#8a8099', 'K' => '#5d5570'],
            'mountain' => ['label' => 'Mountain', 'f' => '#4e3a30', 'F' => '#3a2a22', 'k' => '#a07e68', 'K' => '#6e5444'],
            'snow' => ['label' => 'Snow', 'f' => '#e6e9f2', 'F' => '#bfc5d6', 'k' => '#8f98c9', 'K' => '#646c9e'],
        ], 'rows' => [
            2 => '.........oooooo.........',
            3 => '.......ooffffffoo.......',
            4 => '......offffffffFFo......',
            5 => '.....offffffffffFFo.....',
            6 => '....offfkkkkkkkkfFFo....',
            7 => '....offKKKKKKKKKKFFo....',
            8 => '...okfkkkekkkkekkkFko...',
            9 => '...okfkkkokkkkokkkFko...',
            10 => '....ofkkkkkKKkkkkkFo....',
            11 => '....ofkkkoKKKKokkkFo....',
            12 => '....offkkkooookkkFFo....',
            13 => '.....offkkkkkkkkFFo.....',
            14 => '......oooooooooooo......',
        ]],
        // head feathers f/F, brown body k/K, hooked beak
        'eagle' => ['label' => 'Eagle', 'captain' => true, 'coats' => [
            'bald' => ['label' => 'Bald', 'f' => '#f6f6f8', 'F' => '#cfd3e2', 'k' => '#7a4a2a', 'K' => '#5a361e'],
            'golden' => ['label' => 'Golden', 'f' => '#d9a14a', 'F' => '#b07a2c', 'k' => '#6e4420', 'K' => '#4e2c1d'],
            'harpy' => ['label' => 'Harpy', 'f' => '#aab0c4', 'F' => '#878da3', 'k' => '#3a3449', 'K' => '#2c2738'],
        ], 'rows' => [
            2 => '.........oooooo.........',
            3 => '.......ooffffffoo.......',
            4 => '......offfffffffFo......',
            5 => '.....offfffffffffFo.....',
            6 => '....offfffffffffffFo....',
            7 => '....ofoooffffffoooFo....',
            8 => '....offeoofbbfooefFo....',
            9 => '....offoofbbbbfoofFo....',
            10 => '....offffbbbbbbfffFo....',
            11 => '....okfffbbBBbbfffKo....',
            12 => '....okkkkoBBBBokkkKo....',
            13 => '.....okkkkoBBokkkKo.....',
            14 => '......oooooooooooo......',
        ]],
    ],

    'suit_colors' => [
        'blue' => ['s' => '#2f7de1', 'S' => '#1f5fb4'],
        'red' => ['s' => '#e24a4a', 'S' => '#b23434'],
        'orange' => ['s' => '#ff8a2b', 'S' => '#d36a14'],
        'green' => ['s' => '#34c27a', 'S' => '#239058'],
        'violet' => ['s' => '#8a5cf6', 'S' => '#6b3fd6'],
        'pink' => ['s' => '#ff6fae', 'S' => '#d1478a'],
        'gold' => ['s' => '#f5c04a', 'S' => '#c99a2e'],
        'midnight' => ['s' => '#3a3f5c', 'S' => '#262a40'],
    ],

    // The chest patch: one per planet, plus a plain gold star.
    'patches' => [
        'star' => ['label' => 'Gold star', 'p' => '#f5c04a'],
        'programming' => ['label' => 'Programming', 'p' => '#7bf06a'],
        'networking' => ['label' => 'Networking', 'p' => '#5be1ff'],
        'cybersecurity' => ['label' => 'Cybersecurity', 'p' => '#b48bff'],
    ],

    'visors' => [
        'sky' => '#3aa0ff',
        'clear' => '#cfe9ff',
        'gold' => '#f5c04a',
        'mint' => '#5df2a7',
        'rose' => '#ff8fc7',
    ],

    // How see-through each helmet glass character is (0–1).
    'glass' => ['v' => 0.15, 'V' => 0.47, 'x' => 0.86],

    // A new astronaut before anything is picked.
    'default' => [
        'animal' => 'cat', 'coat' => 'ginger', 'suit' => 'explorer', 'suit_color' => 'blue',
        'patch' => 'star', 'helmet' => true, 'visor' => 'sky',
    ],

    // A new captain (faculty) before anything is picked.
    'captain_default' => [
        'animal' => 'lion', 'coat' => 'golden', 'suit' => 'admiral', 'suit_color' => 'midnight',
        'patch' => 'star', 'helmet' => false, 'visor' => 'gold',
    ],

    // Suit prices in XP (bought in the Shop). Explorer is free.
    'suits' => [
        'explorer' => ['label' => 'Explorer', 'rows' => [
            15 => '........oggggggo........',
            16 => '.....oowwwwwwwwwwoo.....',
            17 => '....owwwwsssssswwwwo....',
            18 => '....owWwwsppsswwWwwo....',
            19 => '....owWwwsppsswwWwwo....',
            20 => '....owWwwSSSSSSwWwwo....',
            21 => '....oggoyyyyyyyyoggo....',
            22 => '....ogGowwwwwwwwoGgo....',
            23 => '.....ooowwwWWwwwooo.....',
            24 => '.......owwwoowwwo.......',
            25 => '.......owwWooWwwo.......',
            26 => '.......owwwoowwwo.......',
            27 => '.......ogggoogggo.......',
            28 => '......ogGggooggGgo......',
            29 => '......oooooooooooo......',
        ]],
        'pilot' => ['label' => 'Pilot', 'xp' => 300, 'rows' => [
            15 => '........oggggggo........',
            16 => '.....oossssssssssoo.....',
            17 => '....oyysssswwssssyyo....',
            18 => '....osSspsswwssssSso....',
            19 => '....osSsssswwssssSso....',
            20 => '....osSsssswwssssSso....',
            21 => '....oggoSSSyySSSoggo....',
            22 => '....ogGossssssssoGgo....',
            23 => '.....ooosssSSsssooo.....',
            24 => '.......osssoossso.......',
            25 => '.......ossSooSsso.......',
            26 => '.......owwwoowwwo.......',
            27 => '.......ogggoogggo.......',
            28 => '......ogGggooggGgo......',
            29 => '......oooooooooooo......',
        ]],
        'engineer' => ['label' => 'Engineer', 'xp' => 600, 'rows' => [
            15 => '........oggggggo........',
            16 => '.....oowsswwwwsswoo.....',
            17 => '....owwwsswwwwsswwwo....',
            18 => '....owWwssSppSsswWwo....',
            19 => '....owWwssSSSSsswWwo....',
            20 => '....owWwsssssssswWwo....',
            21 => '....oggoyygyygyyoggo....',
            22 => '....ogGossssssssoGgo....',
            23 => '.....ooosssSSsssooo.....',
            24 => '.......osssoossso.......',
            25 => '.......osSsoosSso.......',
            26 => '.......osssoossso.......',
            27 => '.......ogggoogggo.......',
            28 => '......ogGggooggGgo......',
            29 => '......oooooooooooo......',
        ]],
        'commander' => ['label' => 'Commander', 'xp' => 900, 'rows' => [
            15 => '........oyyyyyyo........',
            16 => '.....ossswwwwwwssso.....',
            17 => '....osssswwyywwsssso....',
            18 => '.....owSwwwppwwwSwo.....',
            19 => '.....owSwwywwywwSwo.....',
            20 => '.....owSwwywwywwSwo.....',
            21 => '.....oggossssssoggo.....',
            22 => '.....ogGosswwssoGgo.....',
            23 => '.....oossswWWwsssoo.....',
            24 => '.....osowwwoowwwoso.....',
            25 => '......oowwWooWwwoo......',
            26 => '.......owwwoowwwo.......',
            27 => '.......ogggoogggo.......',
            28 => '......ogGggooggGgo......',
            29 => '......oooooooooooo......',
        ]],
        // Captain-only suits: wider shoulders, gold braid, more commanding than the cadet suits.
        // gold epaulettes with fringe, medals, gold hem
        'admiral' => ['label' => 'Admiral', 'captain' => true, 'rows' => [
            15 => '........oyyyyyyo........',
            16 => '....ooyyssswwsssyyoo....',
            17 => '..ooyyyyysswwssyyyyyoo..',
            18 => '..oysysyssywwyssysysyo..',
            19 => '....ospnpsswwssssSso....',
            20 => '....osysysywwysssSso....',
            21 => '....oyyoSSSyySSSoyyo....',
            22 => '....ogGossySSyssoGgo....',
            23 => '.....osssssSSssssso.....',
            24 => '.....osssssSSssssso.....',
            25 => '.....oyyyyyooyyyyyo.....',
            26 => '.......oSSSooSSSo.......',
            27 => '.......ogggoogggo.......',
            28 => '......ogGggooggGgo......',
            29 => '......oooooooooooo......',
        ]],
        // a cape (suit shade) behind epaulettes
        'marshal' => ['label' => 'Marshal', 'captain' => true, 'rows' => [
            15 => '........oyyyyyyo........',
            16 => '...oooosssswwssssoooo...',
            17 => '..oSoyyyssswwsssyyyoSo..',
            18 => '.oSSoyyssswwwwsssyyoSSo.',
            19 => '.oSSosSssswwwwsssSsoSSo.',
            20 => '.oSSosSsssswwssssSsoSSo.',
            21 => '.oSSoggoyyyyyyyyoggoSSo.',
            22 => 'oSSSogGossssssssoGgoSSSo',
            23 => 'oSSSSooossSSSSssoooSSSSo',
            24 => 'oSSSSSoosssSSsssooSSSSSo',
            25 => 'oSSSSSSosSsoosSsoSSSSSSo',
            26 => '.ooSSSSosssoosssoSSSSoo.',
            27 => '...oooSogggoogggoSooo...',
            28 => '......ogGggooggGgo......',
            29 => '......oooooooooooo......',
        ]],
        // long greatcoat with a sash in the patch colour
        'commodore' => ['label' => 'Commodore', 'captain' => true, 'rows' => [
            15 => '........oyyyyyyo........',
            16 => '.....ooppssssssyyoo.....',
            17 => '....oyysppsssssssyyo....',
            18 => '....osSssppssssssSso....',
            19 => '....osSssssppssssSso....',
            20 => '....osSssssssppssSso....',
            21 => '....oggoSSSyySppoggo....',
            22 => '....ogGosssSSsssoGgo....',
            23 => '.....osssssSSssssso.....',
            24 => '.....osssssSSssssso.....',
            25 => '.....oSSSSSooSSSSSo.....',
            26 => '.......oSSSooSSSo.......',
            27 => '.......oGGGooGGGo.......',
            28 => '......ogGGGooGGGgo......',
            29 => '......oooooooooooo......',
        ]],
    ],

    'helmet' => [
        1 => '........oooooooo........',
        2 => '......ooVVVVVVVVoo......',
        3 => '.....oVVvvvvvvvvVVo.....',
        4 => '....oVxxvvvvvvvvvvVo....',
        5 => '....oVxvvvvvvvvvvvVo....',
        6 => '....oVxvvvvvvvvvvvVo....',
        7 => '....oVvvvvvvvvvvvvVo....',
        8 => '....oVvvvvvvvvvvvvVo....',
        9 => '....oVvvvvvvvvvvvvVo....',
        10 => '....oVvvvvvvvvvvvvVo....',
        11 => '....oVvvvvvvvvvvvxVo....',
        12 => '....oVvvvvvvvvvvvxVo....',
        13 => '....ooVvvvvvvvvvvVoo....',
        14 => '.....orrrrrrrrrrrro.....',
        15 => '.....oRRRRRRRRRRRRo.....',
    ],

    // Codename ideas for the dice button: a space word + something about the animal + two digits ("CosmicPaws42").
    'codenames' => [
        'adjectives' => ['Cosmic', 'Turbo', 'Pixel', 'Nova', 'Rocket', 'Laser', 'Stellar', 'Comet', 'Galaxy', 'Neon',
            'Lunar', 'Solar', 'Hyper', 'Byte', 'Cyber', 'Sonic', 'Mega', 'Orbit', 'Meteor', 'Zero'],
        'nouns' => [
            'cat' => ['Paws', 'Whiskers', 'Purr', 'Meow', 'Kitty', 'Tabby'],
            'dog' => ['Pup', 'Woof', 'Barker', 'Fetch', 'Doggo', 'Rover'],
            'cow' => ['Moo', 'Hooves', 'Calf', 'Moozy', 'Bull', 'Milky'],
            'bird' => ['Wing', 'Tweet', 'Feather', 'Chirp', 'Beak', 'Birdie'],
            'pig' => ['Oink', 'Piggy', 'Snout', 'Trotter', 'Piglet', 'Truffle'],
        ],
    ],
];
