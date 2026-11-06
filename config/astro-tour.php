<?php

/*
|--------------------------------------------------------------------------
| Astro's tour
|--------------------------------------------------------------------------
| A step-by-step guide, narrated by Astro (resources/views/components/astro-tour.blade.php).
| `chapters` are the students' tour, `faculty_chapters` the captains' (their keys start with
| "captain-" so the two never mix in users.tour_seen). One chapter per page. A chapter plays by
| itself the first time someone opens one of its `routes`; after that it plays again only from
| the Tour button (sidebar, lesson header).
|
| Each step:
|   target    CSS selector to spotlight (pages mark theirs with data-tour="..."). null = Astro in the middle.
|   title     short heading in the bubble
|   text      what Astro says. {name} becomes the student's codename (or first name); a captain's
|             first name. **bold** works.
|   missing   what to do when the target isn't on the page (or is hidden): 'skip' (default) or 'center'
|             (say it anyway, in the middle). Pages differ per student (no crew yet, sources panel shut...).
| The last step may have `next`: a button to the page with the next chapter (it opens with ?tour=1).
|
| Students are "astronaut", never "cadet"; faculty are "Captain". Keep the words simple: short
| sentences, everyday words, as if talking to a curious 15-year-old. Keep the tone plain and
| professional: no emojis, no exclamation-mark hype.
*/

return [

    'chapters' => [

        'home' => [
            'label' => 'Home',
            'routes' => ['student.dashboard'],
            'steps' => [
                [
                    'target' => null,
                    'title' => 'Welcome, {name}',
                    'text' => "I'm Astro, your guide in TechLab. This short tour shows where everything is.\n\nPress **Next** or the → key to continue.",
                ],
                [
                    'target' => '[data-tour="home-hero"]',
                    'title' => 'Your next mission',
                    'text' => 'This card shows your current course and your next lesson. Press the **blue button** to continue where you left off.',
                ],
                [
                    'target' => '[data-tour="home-profile"]',
                    'title' => 'Your profile',
                    'text' => "Your progress at a glance:\n\n**XP** is earned by finishing lessons, quizzes and labs.\n**Gems** are earned by finishing a module.\n**Streak** counts the days in a row you have studied.",
                ],
                [
                    'target' => '[data-tour="home-avatar"]',
                    'title' => 'Your avatar',
                    'text' => 'Click your avatar to change your suit or codename. Some suits cost gems.',
                ],
                [
                    'target' => '[data-tour="home-leaderboard"]',
                    'title' => 'Leaderboard',
                    'text' => 'See how you rank against your **crew**, the classmates in your course. Rankings are based on XP.',
                ],
                [
                    'target' => '[data-tour="nav-home"]',
                    'title' => 'Navigation',
                    'text' => 'The sidebar is available on every page. The **home** icon brings you back here.',
                ],
                [
                    'target' => '[data-tour="nav-chat"]',
                    'title' => 'Chat',
                    'text' => 'Open **Chat** to ask questions about programming, networking or cybersecurity.',
                ],
                [
                    'target' => '[data-tour="nav-planets"]',
                    'title' => 'The Planets',
                    'text' => 'Courses are grouped into three subjects: **Programming**, **Networking** and **Cybersecurity**. Choose a course here.',
                ],
                [
                    'target' => '[data-tour="nav-shop"]',
                    'title' => 'The Shop',
                    'text' => 'Spend your XP on frames, animals and suits for your avatar, or trade gems for XP. Everything you buy is yours to keep.',
                ],
                [
                    'target' => '[data-tour="nav-controls"]',
                    'title' => 'Display and sound',
                    'text' => 'Switch between light and dark mode, turn sound and music on or off, and expand the sidebar to show labels.',
                ],
                [
                    'target' => '[data-tour="nav-account"]',
                    'title' => 'Your account',
                    'text' => "Open your account menu for **Settings** — your profile, password and more — to change your avatar, or to log out.",
                ],
                [
                    'target' => '[data-tour="nav-tour"]',
                    'title' => 'Replay the tour',
                    'text' => 'Press **Tour** on any page to see the guide for that page.',
                ],
                [
                    'target' => null,
                    'title' => "You're all set",
                    'text' => 'Next, go to the Planets to find your first course.',
                    'next' => ['label' => 'Go to Planets', 'route' => 'student.planets'],
                ],
            ],
        ],

        'planets' => [
            'label' => 'Planets',
            'routes' => ['student.planets'],
            'steps' => [
                [
                    'target' => '[data-tour="planet-card"]',
                    'title' => 'Your planet',
                    'text' => "This is your planet. Click it to see its courses. The bar shows your progress.\n\nThe next step shows how to join a course.",
                    'missing' => 'center',
                ],
            ],
        ],

        'courses' => [
            'label' => 'Joining a course',
            'routes' => ['student.planet', 'student.planet.course'],
            'steps' => [
                [
                    'target' => '[data-tour="course-list"]',
                    'title' => 'Courses',
                    'text' => 'These are the courses on this planet. Each course is led by a **Captain**, your teacher.',
                    'missing' => 'center',
                ],
                [
                    'target' => '[data-tour="course-locked"]',
                    'title' => 'A course you have not started',
                    'text' => 'Each course is led by a **Captain**, your teacher, or you can learn on your own. The next step shows your two options.',
                ],
                [
                    'target' => '[data-tour="course-enroll"]',
                    'title' => 'Self-paced or a class',
                    'text' => "**Self-paced**: start right now, on your own, and still earn a TechLab certificate. The course before it must be finished first.\n\n**Join Class**: pick your Captain. They accept or decline your request; once in, their course carries their own signed certificate.\n\nChoose carefully: once you pick a track for a course, it is final.",
                ],
                [
                    'target' => '[data-tour="course-open"]',
                    'title' => 'Open courses',
                    'text' => 'You are enrolled in this course. Click the card to open it. The bar shows your progress.',
                ],
                [
                    'target' => null,
                    'title' => 'Choose a course',
                    'text' => 'Click a course to open it.',
                ],
            ],
        ],

        'course' => [
            'label' => 'Course page',
            'routes' => ['student.course.overview', 'student.course.materials', 'student.course.crew'],
            'steps' => [
                [
                    'target' => '[data-tour="course-hero"]',
                    'title' => 'Course overview',
                    'text' => 'This is the course overview. The **blue button** opens your next lesson.',
                ],
                [
                    'target' => '[data-tour="course-tabs"]',
                    'title' => 'Tabs',
                    'text' => "**Home** lists your lessons.\n**Materials** contains files shared by your Captain. A red badge marks new items.\n**Crew** shows your Captain and classmates.",
                ],
                [
                    'target' => '[data-tour="course-chapters"]',
                    'title' => 'Chapters and lessons',
                    'text' => 'The course is divided into chapters. Each circle fills as you complete lessons. Lessons unlock in order.',
                ],
                [
                    'target' => '[data-tour="course-rail"]',
                    'title' => 'Course details',
                    'text' => 'View the **syllabus**, your progress and the gems earned in this course.',
                ],
                [
                    'target' => '[data-tour="course-boss"]',
                    'title' => 'Mini bosses and the Final Boss',
                    'text' => "Every module ends with a **Mini Boss**, a short exam on what you just learned. Self-paced: pass it to unlock the next module. A class: you move on either way, and your Captain sees your score.\n\nAfter the last module, the **Final Boss** unlocks. Pass it to earn your certificate.\n\nBoth run full screen, against the clock. Leaving the exam counts as a fail, so start only when you are ready.",
                ],
                [
                    'target' => null,
                    'title' => 'Get started',
                    'text' => 'Press the blue button at the top to open your next lesson.',
                ],
            ],
        ],

        'lesson' => [
            'label' => 'Lessons',
            'routes' => ['student.planet.module.lesson', 'student.planet.play'],
            'steps' => [
                [
                    'target' => null,
                    'title' => 'Lessons',
                    'text' => 'Every lesson follows the same layout. Here is a quick overview.',
                ],
                [
                    'target' => '.programming-sidebar',
                    'title' => 'Course outline',
                    'text' => 'The outline lists every module and lesson. Completed lessons are checked. Click an unlocked lesson to open it.',
                ],
                [
                    'target' => '.sidebar-tab[data-sidebar-tab="resources"]',
                    'title' => 'Resources',
                    'text' => 'Additional reading and the syllabus are in this tab.',
                ],
                [
                    'target' => '#lesson-stage',
                    'title' => 'Lesson sections',
                    'text' => "Work through each section in order:\n\n**Mission Brief**: the lesson goals\n**Astro Says**: the main explanation\n**Lab**: a worked example\n**Knowledge Check**: a short quiz (your first attempt earns XP)\n**Hands-on**: a practical exercise",
                ],
                [
                    'target' => null,
                    'title' => 'Hands-on exercise',
                    'text' => "Each lesson ends with a practical exercise: write code in the editor, which runs in your browser, or build a network in the simulator.\n\nComplete it to finish the lesson and unlock the next one. If you get stuck, use a **hint** (costs XP) or ask in Chat.",
                ],
                [
                    'target' => '.tl-stats',
                    'title' => 'Your score',
                    'text' => 'Your XP, gems and streak are always shown here.',
                ],
                [
                    'target' => '.nav-arrows',
                    'title' => 'Next and back',
                    'text' => 'Move to the previous or next lesson.',
                ],
                [
                    'target' => '.fs-btn:not(.tour-btn)',
                    'title' => 'Focus mode',
                    'text' => 'Switch to full screen for fewer distractions.',
                ],
                [
                    'target' => '.home-btn',
                    'title' => 'Back home',
                    'text' => 'Return to your Home page.',
                ],
                [
                    'target' => null,
                    'title' => "You're all set",
                    'text' => 'Press the **?** button at the top to replay this tour at any time.',
                ],
            ],
        ],

        'chat' => [
            'label' => 'Chat',
            'routes' => ['student.chat'],
            'steps' => [
                [
                    'target' => null,
                    'title' => 'Chat',
                    'text' => 'Ask Astro questions about your lessons at any time. Here is how it works.',
                ],
                [
                    'target' => '[data-tour="chat-input"]',
                    'title' => 'Ask a question',
                    'text' => 'Type your question and press **Enter**. For example: "What is a variable?" You can also paste code and ask what is wrong with it.',
                ],
                [
                    'target' => '[data-tour="chat-attach"]',
                    'title' => 'Attachments',
                    'text' => 'Use **+** to attach a file or image, such as a screenshot of an error.',
                ],
                [
                    'target' => '[data-tour="chat-voice"]',
                    'title' => 'Voice input',
                    'text' => 'Press the microphone to ask your question out loud.',
                ],
                [
                    'target' => '[data-tour="chat-sources"]',
                    'title' => 'Sources',
                    'text' => 'Open **Sources** to add lessons or files from your Captain\'s Materials. Answers will be based on what you select.',
                ],
                [
                    'target' => '[data-tour="chat-new"]',
                    'title' => 'New chat',
                    'text' => 'Start a new chat for each new topic. Previous chats are saved under **Chat** in the sidebar.',
                ],
                [
                    'target' => '[data-tour="chat-xp"]',
                    'title' => 'Message limits',
                    'text' => 'You get a number of **free messages each day**. After that, each message costs a small amount of XP.',
                    'missing' => 'center',
                ],
                [
                    'target' => null,
                    'title' => 'Try it',
                    'text' => 'Ask your first question to get started.',
                ],
            ],
        ],

        'shop' => [
            'label' => 'The Shop',
            'routes' => ['student.shop'],
            'steps' => [
                [
                    'target' => null,
                    'title' => 'The Shop',
                    'text' => 'Spend the XP you earn from lessons, quizzes and labs on how your avatar looks. Only XP you **earn by learning** ever counts toward your level.',
                ],
                [
                    'target' => '[data-tour="shop-you"]',
                    'title' => 'You',
                    'text' => 'Your rank badge, your level and how much XP you have left to spend, plus your gems.',
                ],
                [
                    'target' => '[data-tour="shop-tabs"]',
                    'title' => 'Frames, animals, suits and gems',
                    'text' => "**Frames** go around your avatar. **Animals** and **Suits** change your look. **Gem exchange** trades gems for XP to spend here.",
                ],
                [
                    'target' => '[data-tour="shop-frames"]',
                    'title' => 'Frames',
                    'text' => 'Every rank has a free frame and two to buy. A frame goes on sale once you reach its rank, and stays yours for good.',
                ],
                [
                    'target' => '[data-tour="shop-gems"]',
                    'title' => 'Gem exchange',
                    'text' => "You earn gems by finishing a module. Trade them here for XP to spend in the Shop. It will not raise your level, since levels only count XP you earn by learning.",
                    'missing' => 'center',
                ],
                [
                    'target' => null,
                    'title' => 'Wear it',
                    'text' => 'Buy something and press **Wear** to show it off. Change your mind any time.',
                ],
            ],
        ],

        'settings' => [
            'label' => 'Settings',
            'routes' => ['settings'],
            'steps' => [
                [
                    'target' => null,
                    'title' => 'Settings',
                    'text' => 'This is where you change your profile, your password, and how TechLab looks and sounds.',
                ],
                [
                    'target' => '[data-tour="set-profile"]',
                    'title' => 'Profile',
                    'text' => "Your name, program and year level. Your email cannot be changed here, since it's how you log in.",
                ],
                [
                    'target' => '[data-tour="set-look"]',
                    'title' => 'Your astronaut',
                    'text' => 'Change your animal, suit and codename. Classmates see you by your codename, not your real name.',
                ],
                [
                    'target' => '[data-tour="set-prefs"]',
                    'title' => 'Preferences',
                    'text' => 'Sound and music, and light or dark mode. These are saved in this browser only.',
                ],
                [
                    'target' => '[data-tour="set-password"]',
                    'title' => 'Password',
                    'text' => 'Change your password any time.',
                ],
                [
                    'target' => '[data-tour="set-delete"]',
                    'title' => 'Delete account',
                    'text' => "This removes your progress, XP, chats and gems for good. There's no undo.",
                    'missing' => 'center',
                ],
                [
                    'target' => null,
                    'title' => "That's Settings",
                    'text' => 'Press **Tour** on any page to see its guide again.',
                ],
            ],
        ],

    ],

    /*
    | The captains' tour: the faculty side. Same rules as above.
    */
    'faculty_chapters' => [

        'captain-home' => [
            'label' => 'Dashboard',
            'routes' => ['faculty.dashboard'],
            'steps' => [
                [
                    'target' => null,
                    'title' => 'Welcome, Captain {name}',
                    'text' => "I'm Astro, your guide in TechLab. This short tour shows how to run your courses and follow your students.\n\nPress **Next** or the → key to continue.",
                ],
                [
                    'target' => '[data-tour="fd-filters"]',
                    'title' => 'Filters',
                    'text' => 'Pick one course or **All courses**, and a time range. Every number, chart and list on this page follows what you pick.',
                ],
                [
                    'target' => '[data-tour="fd-tiles"]',
                    'title' => 'At a glance',
                    'text' => 'How many students you have, how many were active, the lessons they completed, their average progress, and how many passed the final exam. The line under a number compares it with the period before.',
                ],
                [
                    'target' => '[data-tour="fd-activity"]',
                    'title' => 'Activity',
                    'text' => 'What your students did each day. Switch between **Lessons completed**, **Active time** and **Active students**. Hover a day to see all three.',
                ],
                [
                    'target' => '[data-tour="fd-charts"]',
                    'title' => 'Progress and final exam',
                    'text' => "**Course progress** groups students by how much of the course they finished.\n**Final exam** shows who is still on lessons, who is ready, who passed and who failed.\n\nClick a bar or a row to list those students below.",
                ],
                [
                    'target' => '[data-tour="fd-students"]',
                    'title' => 'Students',
                    'text' => 'Every student, with their progress, lessons and when they were last active. Search by name, sort any column, or press **Needs a nudge** to find students who have been away for 7 days or more.',
                ],
                [
                    'target' => '[data-tour="fd-courses"]',
                    'title' => 'Your courses',
                    'text' => "The courses an admin gave you. Press **Generate code** and share the code with your students so they can ask to join. **Manage course** opens its crew, materials and quizzes.\n\nA yellow warning means the course has no live final exam yet, so nobody can earn a certificate.",
                ],
                [
                    'target' => '[data-tour="fd-alerts"]',
                    'title' => 'Caught leaving the exam',
                    'text' => 'Students caught leaving their final exam (another tab, leaving full screen, a screenshot) show up here. Press **Review** to see their result and, if you choose, allow a retake.',
                ],
                [
                    'target' => '[data-tour="fd-requests"]',
                    'title' => 'Waiting to join',
                    'text' => 'Students who asked to join one of your courses. Press **Accept** to enroll them right away, or **Decline**.',
                ],
                [
                    'target' => '[data-tour="nav-captain"]',
                    'title' => 'Navigation',
                    'text' => "The sidebar is on every page.\n\n**Courses**: open a course and edit its lessons.\n**Crew**: your students and their progress.\n**Materials**: files, links and the final exam.",
                ],
                [
                    'target' => '[data-tour="nav-account"]',
                    'title' => 'Your account',
                    'text' => "Open your account menu for **Settings** — your captain avatar, password and more — or to log out.",
                ],
                [
                    'target' => '[data-tour="nav-tour"]',
                    'title' => 'Replay the tour',
                    'text' => 'Press **Tour** on any page to see the guide for that page.',
                ],
                [
                    'target' => null,
                    'title' => "You're all set",
                    'text' => 'Next, open your courses to see them the way your students do.',
                    'next' => ['label' => 'Go to Courses', 'route' => 'faculty.courses.index'],
                ],
            ],
        ],

        'captain-courses' => [
            'label' => 'Courses',
            'routes' => ['faculty.courses.index'],
            'steps' => [
                [
                    'target' => '[data-tour="fc-card"]',
                    'title' => 'Your courses',
                    'text' => 'These are your courses, shown as the same cards your students see. Click one to open it and edit its lessons.',
                ],
                [
                    'target' => '[data-tour="fc-edit"]',
                    'title' => 'Edit the card',
                    'text' => 'Change the cover image, title, subtitle and level that students see on this card.',
                ],
                [
                    'target' => null,
                    'title' => 'Editing lessons',
                    'text' => "Inside a course, every lesson has **Edit** buttons. You can change the text, add images, edit the quiz and the lab, and rename or reorder modules and lessons.\n\nChanges go live for your students as soon as you save, and any change can be reset to the original.",
                ],
            ],
        ],

        'captain-crew' => [
            'label' => 'Crew',
            'routes' => ['faculty.crew', 'faculty.course.roster'],
            'steps' => [
                [
                    'target' => '[data-tour="captain-code"]',
                    'title' => 'Class code',
                    'text' => 'Your class\'s ID. Students never type it: they pick you under **Join Class** and ask to join, and you accept them on your **Dashboard**.',
                ],
                [
                    'target' => '[data-tour="captain-tabs"]',
                    'title' => 'Course tabs',
                    'text' => "**Crew**: the students in this course.\n**Materials**: files and links.\n**Lesson quizzes**: the short quiz at the end of each lesson.\n**Exams**: the mini boss after every module, and the Final Boss.",
                ],
                [
                    'target' => '[data-tour="crew-list"]',
                    'title' => 'Your crew',
                    'text' => 'Every student in the course, with their progress, XP, gems and day streak. The students furthest along are at the top.',
                    'missing' => 'center',
                ],
                [
                    'target' => null,
                    'title' => 'More detail',
                    'text' => 'For charts, daily activity and who needs a nudge, go to your **Dashboard**.',
                ],
            ],
        ],

        'captain-materials' => [
            'label' => 'Materials',
            'routes' => ['faculty.course'],
            'steps' => [
                [
                    'target' => '[data-tour="captain-tabs"]',
                    'title' => 'Course tabs',
                    'text' => "**Crew**: the students in this course.\n**Materials**: files and links.\n**Lesson quizzes**: the short quiz at the end of each lesson.\n**Exams**: the mini boss after every module, and the Final Boss.",
                ],
                [
                    'target' => '[data-tour="final-warn"]',
                    'title' => 'No final exam yet',
                    'text' => 'Students earn their certificate only by passing the final exam. Until you create and publish it, students who finish every lesson have to wait — manage it from the **Exams** tab.',
                ],
                [
                    'target' => '[data-tour="lm-head"]',
                    'title' => 'Learning modules',
                    'text' => "Group your files and links into modules. Students see them right away in the course's **Materials** tab.\n\nAstro reads PDF, Word, PowerPoint and text files, so it can answer your students' questions from them.",
                ],
                [
                    'target' => '[data-tour="lm-upload"]',
                    'title' => 'Add to a module',
                    'text' => 'Press **Upload files** or **Add link**. Under each file, a short note tells you if Astro can read it.',
                ],
                [
                    'target' => '[data-tour="lm-add"]',
                    'title' => 'New module',
                    'text' => 'Give the module a title, then add its files now or later.',
                ],
                [
                    'target' => null,
                    'title' => 'Exams',
                    'text' => "Open the **Exams** tab for the **Final Boss** — students unlock it after every lesson and must pass it to earn their certificate — and the mini boss after each module. Each student's result shows there, with **Allow retake** for one who failed, or **Lift ban** for one caught leaving the exam twice.",
                ],
                [
                    'target' => null,
                    'title' => 'Lesson quizzes',
                    'text' => 'Open the **Lesson quizzes** tab to change the short quiz at the end of each lesson.',
                ],
            ],
        ],

        'captain-quizzes' => [
            'label' => 'Lesson quizzes',
            'routes' => ['faculty.lesson-quizzes.index', 'faculty.lesson-quizzes.edit'],
            'steps' => [
                [
                    'target' => '[data-tour="lq-list"]',
                    'title' => 'Lesson quizzes',
                    'text' => 'Every lesson ends with a short quiz, the **Knowledge Check**. Each one comes ready-made.',
                ],
                [
                    'target' => '[data-tour="lq-edit"]',
                    'title' => 'Edit a quiz',
                    'text' => 'Press **Edit** to change the questions, the choices or the right answer. A tag shows which quizzes you changed.',
                ],
                [
                    'target' => '[data-tour="lqe-bar"]',
                    'title' => 'Save your changes',
                    'text' => 'Add questions here, then press **Save quiz**. Changes apply to every student in the course.',
                ],
                [
                    'target' => '[data-tour="lqe-q"]',
                    'title' => 'A question',
                    'text' => "Write the question, tick the right answer, and add a short explanation that students see after they answer.\n\nKeep the choices a similar length, so the right one is not easy to guess.",
                ],
                [
                    'target' => null,
                    'title' => 'Reset any time',
                    'text' => 'You can always reset a quiz to its original questions. Students who already answered keep their results.',
                ],
            ],
        ],

        'captain-builder' => [
            'label' => 'Final exam builder',
            'routes' => ['faculty.quizzes.build', 'faculty.quizzes.edit'],
            'steps' => [
                [
                    'target' => null,
                    'title' => 'Exam builder',
                    'text' => 'This is where you write the final exam. It works like a form: one card per question.',
                ],
                [
                    'target' => '[data-tour="gf-tabs"]',
                    'title' => 'Questions and settings',
                    'text' => "**Questions**: write the exam.\n**Settings**: the time limit, a deadline and the pass mark.",
                ],
                [
                    'target' => '[data-tour="gf-head"]',
                    'title' => 'Title and instructions',
                    'text' => 'Give the exam a title and, if you like, instructions for your students.',
                ],
                [
                    'target' => '[data-tour="gf-tools"]',
                    'title' => 'Tools',
                    'text' => '**+** adds a question. The book opens the **course summary** and the paper clip opens **your materials**, so you can read them while you write.',
                ],
                [
                    'target' => '[data-tour="gf-actions"]',
                    'title' => 'Save or publish',
                    'text' => '**Save draft** keeps it hidden from students. **Publish** makes it live. Once a student has taken it, it can no longer be changed.',
                ],
                [
                    'target' => null,
                    'title' => 'Before you publish',
                    'text' => 'Set a time limit in **Settings**. Students must reach the pass mark to earn their certificate.',
                ],
            ],
        ],

        'captain-lesson' => [
            'label' => 'Editing lessons',
            'routes' => ['student.planet.module.lesson', 'student.planet.play'],
            'steps' => [
                [
                    'target' => null,
                    'title' => 'Editing your course',
                    'text' => 'You see the course the way your students do, plus editing tools. Changes go live as soon as you save, and any change can be reset to the original.',
                ],
                [
                    'target' => '[data-tour="ce-outline"]',
                    'title' => 'Modules and lessons',
                    'text' => 'Rename, reorder, hide or add modules and lessons. Hidden ones disappear for students but keep their progress, so you can bring them back.',
                ],
                [
                    'target' => '.ce-bar',
                    'title' => 'Editing mode',
                    'text' => "At the top of each lesson, the **Editing mode** bar tells you if the lesson is the original or edited by you.\n\n**Stages** adds, renames, reorders or removes the lesson's sections. **Reset lesson** brings back the original.",
                    'missing' => 'center',
                ],
                [
                    'target' => '.ce-pen',
                    'title' => 'Edit buttons',
                    'text' => "Press **Edit** on the lesson title or any section to change its text, or add images, links and code.\n\nParts students interact with, like the exercise, show as locked blocks you can move. The quiz and the lab have their own **Edit** buttons.",
                    'missing' => 'center',
                ],
                [
                    'target' => '.nav-arrows',
                    'title' => 'Next and back',
                    'text' => 'Move to the previous or next lesson.',
                ],
                [
                    'target' => '.home-btn',
                    'title' => 'Back to Dashboard',
                    'text' => 'Return to your Dashboard.',
                ],
                [
                    'target' => null,
                    'title' => "You're all set",
                    'text' => 'Press the **?** button at the top to replay this tour at any time.',
                ],
            ],
        ],

        'captain-mini-bosses' => [
            'label' => 'Exams',
            'routes' => ['faculty.exams.index'],
            'steps' => [
                [
                    'target' => null,
                    'title' => 'Exams',
                    'text' => "This tab holds every exam: the Final Boss at the top, and a mini boss for each module below it — a short exam your students take right after finishing that module's lessons.",
                ],
                [
                    'target' => '[data-tour="mb-lead"]',
                    'title' => 'Ready-made',
                    'text' => "Each one comes with at least 30 questions from TechLab. Students take it once, full screen and against the clock, and move on to the next module whether they pass or not. Leaving the exam counts as a fail, and you get an email.",
                    'missing' => 'center',
                ],
                [
                    'target' => '[data-tour="mb-module"] .mrow',
                    'title' => 'Edit or reset',
                    'text' => "Press **Edit** to change the questions, the time or the pass mark. Changes reach only your class. **Reset to default** brings back TechLab's questions; your students keep their results.",
                ],
                [
                    'target' => '[data-tour="mb-module"] .final-rows',
                    'title' => 'Scores',
                    'text' => 'Every student who has taken this mini boss shows here, with their score and the XP they earned.',
                    'missing' => 'center',
                ],
                [
                    'target' => '#final-exam',
                    'title' => 'The Final Boss',
                    'text' => 'Above the mini bosses is the Final Boss, the exam students pass to earn their certificate.',
                    'missing' => 'center',
                ],
            ],
        ],

        'captain-settings' => [
            'label' => 'Settings',
            'routes' => ['settings'],
            'steps' => [
                [
                    'target' => null,
                    'title' => 'Settings',
                    'text' => 'This is where you change your profile, your password, and how TechLab looks and sounds.',
                ],
                [
                    'target' => '[data-tour="set-profile"]',
                    'title' => 'Profile',
                    'text' => "Your name. Your email cannot be changed here, since it's how you log in.",
                ],
                [
                    'target' => '[data-tour="set-look"]',
                    'title' => 'Your captain',
                    'text' => 'Make or change your captain avatar. Your students see it wherever your name shows up.',
                ],
                [
                    'target' => '[data-tour="set-prefs"]',
                    'title' => 'Preferences',
                    'text' => 'Sound and music, and light or dark mode. These are saved in this browser only.',
                ],
                [
                    'target' => '[data-tour="set-password"]',
                    'title' => 'Password',
                    'text' => 'Change your password any time.',
                ],
                [
                    'target' => '[data-tour="set-delete"]',
                    'title' => 'Delete account',
                    'text' => "Hand off your crews and courses first. There's no undo.",
                    'missing' => 'center',
                ],
                [
                    'target' => null,
                    'title' => "That's Settings",
                    'text' => 'Press **Tour** on any page to see its guide again.',
                ],
            ],
        ],

    ],

];
