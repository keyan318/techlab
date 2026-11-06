// Student guides for the TechLab user manual: one for Self-paced learners, one for students who join a class.
// Both share the account, lesson, Astro, Shop and settings steps; only the track-specific parts differ.
// Build: node student.cjs (needs the docx package). Writes both .docx files.
const path = require('path');
const kit = require('./lib.cjs');

const CLASS = '../class/'; // Join Class screenshots live in shots/class, next to shots/student

function manual(track) {
  const self = track === 'self';
  const { p, bullet, numbered, note, table, step, part, cover, save } = kit('Student');
  let n = 0;
  const nextPart = (title, intro) => part(++n, title, intro);

  const accountParts = [
    ...nextPart('Create your account', 'Takes about three minutes. You will sign up, agree to the rules, and build your astronaut.'),
    ...step('Open TechLab and start your mission', [
      p('Go to the TechLab home page. Click [[Start your mission]] to make a new account.'),
      p('Already have one? Click [[Log in]] instead and skip to the next part.'),
    ], [['01-home.png', 'The TechLab home page']]),
    ...step('Fill in the sign-up form', [
      ...numbered([
        'Type your **first name** and **last name**. Use your real name: it is printed on your certificate.',
        'Type your **email**. This is what you log in with, and it can\'t be changed later.',
        'Pick a **password** with at least 8 characters, then type it again to confirm.',
        'Choose your **program** (IT, IS, CS, BLIS or EMC) and **year level**.',
        'Pick the planet you want to learn first: **Programming**, **Networking** or **Cybersecurity**. You can only pick one now. The others unlock later.',
      ]),
      p('Click [[Create account]].'),
    ], [self ? ['03-register-filled.png', 'The sign-up form, filled in'] : [CLASS + '01-register.png', 'The sign-up form, filled in']]),
    ...step('Read and agree to the rules', [
      p('TechLab has 7 short rules. Read them, tick **I have read and agree**, then click [[Agree and continue]].'),
      note('Know this early', 'Quizzes give you one try with a timer. In the Final Boss, switching tabs, leaving full screen or taking screenshots fails the exam and gives you a strike. Two strikes and you are banned from that course.', true),
    ], [['04-terms.png', 'The TechLab rules']]),
    ...step('Meet Astro and build your astronaut', [
      p('Astro is your AI guide. Click [[Continue]] to read each message, then [[Make my astronaut]]. Music plays here: use **Music on** in the corner to turn it off.'),
      p('Use the **Animal**, **Suit** and **Helmet** tabs to choose your look. The Cat is free. Other animals cost XP, so you can buy them later in the Shop. Click [[Looks good!]] when you\'re done.'),
    ], [['06-avatar.png', 'The astronaut builder']]),
    ...step('Pick a codename', [
      p('Your classmates see your **codename**, not your real name. Keep it friendly. Stuck? Click [[Give me a codename idea]].'),
      p('Click [[That\'s me!]], then [[Start my mission]].'),
    ], [['07-codename.png', 'Choosing a codename']]),

    ...nextPart('Your dashboard', 'Home base. It shows your planet, your level and the leaderboard.'),
    ...step('Follow Astro\'s tour', [
      p('The first time you arrive, Astro gives a short tour. Click [[Next]] or press the right arrow key to move on. Click [[Skip tour]] to close it.'),
      p('Want it again later? Click the **?** (Tour) button in the left sidebar.'),
    ], [['09-tour.png', 'Astro\'s tour']]),
    ...step('Find your way around', [
      p('The left sidebar is on every page:'),
      bullet('**Home**: this dashboard'),
      bullet('**Chat**: talk to Astro'),
      bullet('**Planets**: all three planets and their courses'),
      bullet('**Shop**: spend XP on frames, animals and suits'),
      bullet('At the bottom: Tour, light/dark theme, sound, and your account menu (your avatar)'),
    ], [['10-dashboard.png', 'The dashboard and sidebar']]),
  ];

  const trackTable = table(['', 'Self-paced', 'Join Class'], [
    ['Who teaches', 'Just you and Astro', 'Your instructor (your Captain)'],
    ['How you start', 'Right away, one click', 'Ask to join; your instructor accepts you'],
    ['Prerequisites', 'Must finish the course before it (e.g. Programming 1 before Programming 2)', 'None: your instructor decides who joins'],
    ['Lessons and quizzes', 'The standard TechLab version', 'Your instructor\'s version, which may be edited'],
    ['Extra files', 'None', 'Supplementary Materials from your instructor'],
    ['Mini Boss you fail', 'Next module stays locked, 0 XP, wait 60 min, try again', 'One try, score saved for your instructor, nothing locks'],
    ['Final exam', 'TechLab\'s standard final', 'The final your instructor writes'],
    ['Who watches your progress', 'TechLab admins', 'Your instructor'],
    ['Certificate', 'Issued by TechLab (Self-paced Learning Program)', 'Signed by your instructor (Class Learning)'],
  ], [2200, 3400, 3426]);

  const selfParts = [
    ...nextPart('Start a course Self-paced', 'Self-paced means you learn on your own, at your own speed, with the standard TechLab course. Here is how it compares to joining a class.'),
    ...trackTable,
    ...step('Click Self-paced', [
      p('On your dashboard\'s planet card, click [[Self-paced]]. There is no waiting: the course opens straight away.'),
      note('Choose carefully', 'Once you pick Self-paced for a course, you can\'t switch to a class for that course later. If you want an instructor, use Join Class instead (see the Join Class student manual).', true),
      note('Prerequisites', 'Some courses need the one before them first. For example, Self-paced Programming 2 opens only after you pass the Programming 1 final exam.'),
    ], [['11-join-class.png', 'The Self-paced and Join Class buttons']]),
    ...step('Look at your course page', [
      p('Your course page lists every **chapter** (module) and its lessons. Lessons unlock in order. The badge at the top says **Self-paced**.'),
      p('The right side shows the syllabus PDF, your progress, the gems you can earn and your certificate box. The **Crew** tab shows the course leaderboard.'),
      p('Click [[Start Learning]].'),
    ], [['12-course-overview.png', 'A Self-paced course page'], ['13-crew.png', 'The Crew tab']]),
    ...step('Find your courses later', [
      p('Every course you have started is listed on **My Courses**, marked **Self-paced**. Open one to get back to it.'),
    ], [['26-courses.png', 'My Courses']]),
  ];

  const classParts = [
    ...nextPart('Join your instructor\'s class', 'Join Class means you learn with an instructor, called your **Captain**. They follow your progress, may change lessons and quizzes for your class, write your final exam and sign your certificate. Here is how it compares to Self-paced.'),
    ...trackTable,
    ...step('Ask to join your instructor\'s class', [
      ...numbered([
        'On your dashboard\'s planet card, click [[Join Class]].',
        'Under **Who is your instructor?**, find your instructor\'s name.',
        'Click [[Ask to join]] next to it.',
      ]),
      p('You don\'t need a code. Just pick the right name. Ask your instructor if you\'re not sure which one is yours.'),
      note('Choose carefully', 'Once you join a class for a course, you can\'t switch to Self-paced or to another instructor\'s class for that course. If you already started a course Self-paced, you can\'t ask to join a class for it.', true),
    ], [[CLASS + '02-join-class.png', 'Picking your instructor under Join Class']]),
    ...step('Wait for your Captain to accept you', [
      p('Your request is sent. The card says **Waiting for (your instructor) to accept you.**'),
      p('Your instructor sees your name under **Waiting to join** on their dashboard and clicks **Accept**. If they decline, TechLab emails you. You can then ask a different instructor.'),
    ], [[CLASS + '03-request-sent.png', 'Waiting for the instructor to accept'], [CLASS + '03b-captain-accepts.png', 'What your instructor sees: Accept or Decline']]),
    ...step('Enter your class', [
      p('Once you are accepted, refresh your dashboard. A green badge says **Accepted into (your instructor)\'s class**. Click [[Enter Programming City]] (or your planet\'s name).'),
      p('Joining a class starts the course fresh, with your instructor\'s version of the lessons.'),
    ], [[CLASS + '04-accepted.png', 'Accepted into the class']]),
    ...step('Look at your class course page', [
      p('The badge at the top says **Class · (your instructor)**. The course page lists every chapter and its lessons. Lessons unlock in order.'),
      p('Click [[Start Learning]] to begin.'),
    ], [[CLASS + '05-course.png', 'A class course page']]),
    ...step('Open your instructor\'s materials', [
      p('Click the **Supplementary Materials** tab. Here you find the files and links your instructor added, grouped by week or module. A red number on the tab means something new was added since you last looked.'),
      p('Astro can also answer questions using your instructor\'s PDF, Word, PowerPoint and text files.'),
    ], [[CLASS + '08-materials.png', 'Supplementary Materials from the instructor']]),
    ...step('Meet your Captain and crew', [
      p('Click the **Crew** tab. It shows your **Captain** (your instructor) and a leaderboard of everyone in your class.'),
    ], [[CLASS + '06-crew.png', 'Your Captain and crew']]),
  ];

  const lessonParts = [
    ...nextPart('Take a lesson', 'Every lesson has the same 5 stages. You unlock each stage by passing a short checkpoint question.' + (self ? '' : ' In a class, your instructor may have changed some lesson text, quizzes or labs.')),
    ...table(['Stage', 'Name', 'What you do'], [
      ['1', 'Mission Brief', 'Read the simple explanation'],
      ['2', 'Picture It', 'See the idea as an everyday example with a picture'],
      ['3', 'Field Diagram', 'Study the diagram and the code example'],
      ['4', 'Knowledge Check', 'Answer the quiz (10 XP per right answer)'],
      ['5', 'Coding Exercise / Lab', 'Write code or do a hands-on lab (100 XP)'],
    ], [1000, 2800, 5226]),
    ...step('Read the lesson screen', [
      bullet('**Left**: the Course Outline. Open a module to see its lessons. The **Resources** tab has files.'),
      bullet('**Top**: your XP, gems and day streak.'),
      bullet('**Right edge**: dots that jump to each stage.'),
      bullet('The expand button (top right) makes the lesson full screen.'),
    ], [self ? ['15-lesson-start.png', 'The start of Lesson 1.1'] : [CLASS + '07-lesson.png', 'The start of Lesson 1.1']]),
    ...step('Read each stage and pass its checkpoint', [
      p('Read the stage. At the bottom, click [[I\'ve read it: start the checkpoint]] and answer the one question. A right answer opens the next stage straight away. A stage you haven\'t unlocked yet shows a lock message.'),
      note('Wrong answer?', 'You have to wait a short time before trying again. The wait gets longer each time you miss, so read carefully.', true),
    ], [['17-checkpoint.png', 'A checkpoint question'], ['18-stage2.png', 'Stage 2: Picture It']]),
    ...step('Answer the Knowledge Check', [
      p('Click one answer per question. You see right away if it was correct, plus a short explanation. Each right answer gives **+10 XP**.'),
      note('Rule', 'You can miss at most 2 questions. Miss more and the coding stage stays locked until you retake the quiz.'),
    ], [['20-quiz-result.png', 'Knowledge Check answers marked Correct']]),
    ...step('Do the coding exercise', [
      p('Click [[Code it yourself]]. The editor opens with the instructions on the left.'),
      ...numbered([
        'Type your code in the editor.',
        'If your program asks for input, type it in **Program Input**, one value per line.',
        'Click [[Run]] to test. The result shows in the **Terminal**.',
        'Click [[Submit answer]] when it looks right.',
      ]),
      p('Stuck? [[Flip to get help from Astro]] costs 50 XP. Networking and Cybersecurity lessons open a simulator lab here instead of a code editor.'),
    ], [['22-code-editor.png', 'The code editor with the program output']]),
    ...step('Finish the lesson', [
      p('If the answer is wrong, you see **Not quite**. Check the terminal output, fix your code and submit again.'),
      p('When it\'s right, Astro cheers and you get **+100 XP**. Click [[Next lesson]] to keep going.'),
    ], [['24-code-wrong.png', 'A wrong answer'], ['23-code-passed.png', 'Challenge complete']]),
  ];

  const bossParts = [
    ...nextPart('Mini Bosses and the Final Boss', 'Big exams that open full screen, against the clock. Read the rules before you start one.'),
    ...step('Beat the Mini Boss at the end of each module', self ? [
      p('After the last lesson of a module, the **Mini Boss** unlocks. It has about 30 questions, a 45-minute timer, and you need **75%** to win. Each right answer is **+10 XP**.'),
      note('Self-paced rule', 'You must win to open the next module. If you lose, you get 0 XP and wait 60 minutes before you can try again.', true),
      p('Finishing a module also earns you a **gem**.'),
    ] : [
      p('After the last lesson of a module, the **Mini Boss** unlocks. It has about 30 questions, a 45-minute timer, and a pass mark of **75%** (your instructor may change these). Each right answer is **+10 XP**.'),
      note('Class rule', 'You get **one try only**. Your score is saved for your instructor, and nothing locks: you move on to the next module either way. Leaving full screen ends it as a fail with no XP, and your instructor is told.', true),
      p('Finishing a module also earns you a **gem**.'),
    ], [self ? ['35-mini-boss.png', 'The Mini Boss at the end of Module 1'] : [CLASS + '09-mini-boss.png', 'The Mini Boss in a class']]),
    ...step('Face the Final Boss', [
      p('Clear every chapter and the **Final Exam** unlocks at the bottom of the course page. ' + (self
        ? 'Self-paced learners take TechLab\'s standard final exam.'
        : 'In a class, the final is written by your instructor. If they haven\'t published it yet, you wait until they do.')),
      note('Strict rules', 'Stay on the exam page the whole time. Switching tabs, leaving full screen or taking a screenshot fails the exam and adds a strike. Two strikes bans you from the final until ' + (self ? 'a TechLab admin lifts it' : 'your instructor allows a retake') + '. Failing the final also resets your course progress, so you redo the course. You only see your score after, not which answers you missed.', true),
    ], [self ? ['36-final-boss.png', 'The Final Boss and Certificate box'] : [CLASS + '10-final.png', 'The Final Boss and Certificate box']]),
    ...step('Open your certificate', [
      p('After you pass the Final Boss, click [[View certificate]] in the **Certificate** box on the course page. It opens as a **PDF** you can save or print.'),
      self
        ? p('Your Self-paced certificate shows your name, the course, the date you passed and a certificate number. It is issued by **TechLab** under the **Self-paced Learning Program**.')
        : p('Your class certificate shows your name, the course, the date and a certificate number, with your instructor\'s **digital signature** and name, under **Class Learning**.'),
      p('The name on the certificate comes from your profile. Check it in **Settings** before you take the Final Boss.'),
      note('Good to know', 'A TechLab certificate shows you finished the course on TechLab. It is not yet recognized by schools or the IT industry, and it does not replace school credit or an official industry certification.'),
    ], [self ? ['39-certificate.png', 'A Self-paced certificate, issued by TechLab', 430] : ['40-class-certificate.png', 'A class certificate, signed by the instructor', 430]]),
  ];

  const commonEnd = [
    ...nextPart('Ask Astro', 'Astro is an AI tutor. Ask it to explain anything you are stuck on.'),
    ...step('Start a chat', [
      p('Click **Chat** in the sidebar. Type your question in **Ask Astro or create something** and press Enter.'),
      p('Click [[+]] to attach a file (PDF, Word, PowerPoint, text or code) or a photo. Up to 4 files, 2 MB each.'),
      p('You have a set number of free messages, shown under the box. Click [[New chat]] to start a fresh topic.'),
      note('Remember', 'Astro can be wrong. Don\'t share passwords or private info. If you see an error, wait a moment and send your message again.'),
    ], [['28-chat-answer.png', 'Astro answering a question']]),

    ...nextPart('Planets, XP and the Shop', 'Learning earns XP and gems. They raise your level and unlock new planets and gear.'),
    ...table(['You do this', 'You get'], [
      ['Log in for the day', '+10 XP'],
      ['Right answer in a lesson quiz or Mini Boss', '+10 XP'],
      ['Pass a coding exercise or lab', '+100 XP'],
      ['Finish a module', '1 gem'],
      ['Trade in the Shop\'s Gem exchange', '1 gem = 50 XP'],
    ], [6026, 3000]),
    ...step('Unlock new planets', [
      p('Click **Planets**. Your first planet is open. The others show what they need to unlock. For example, Networking Nebula needs **500 XP and 3 gems**.'),
    ], [['25-planets.png', 'The three planets']]),
    ...step('Spend XP in the Shop', [
      p('Click **Shop**. Use the tabs to buy **Frames**, **Animals** and **Suits**, or trade gems in **Gem exchange**. Spending XP does not lower your level: your level counts all the XP you ever earned.'),
    ], [['30-shop.png', 'The Shop']]),
    ...step('Check your progress', [
      p('Click [[View progress]] on the dashboard or course page. You see your total XP, day streak, gems, planet rank, active hours and the lesson to continue.' + (self ? '' : ' Your instructor can see your progress too.')),
    ], [['31-progress.png', 'The progress page']]),

    ...nextPart('Settings and logging out', 'Change your details, your look and your preferences.'),
    ...step('Open the account menu and settings', [
      p('Click your **avatar** at the bottom of the sidebar. Choose [[Settings]] or [[Log out]].'),
      bullet('**Profile**: change your name, program or year, then click [[Save profile]].'),
      bullet('**Your astronaut**: click [[Change my astronaut]] to edit your look or codename.'),
      bullet('**Preferences**: sound and light mode (saved on this browser only).'),
      bullet('Further down: change your password, or delete your account.'),
    ], [['37-account-menu.png', 'The account menu'], ['32-settings.png', 'Settings']]),
    ...step('Log back in next time', [
      p('Go to TechLab and click [[Log in]]. Type your email and password, then click [[Sign in]]. Forgot your password? Click [[Forgot password?]] to get a new one by email.'),
    ], [['38-login.png', 'The sign-in page']]),
  ];

  const title = self ? 'TechLab Student Manual: Self-paced' : 'TechLab Student Manual: Join Class';
  const content = [
    ...cover(title,
      self
        ? 'A step-by-step guide for students who learn **Self-paced**: on your own, at your own speed, with the standard TechLab course and a certificate issued by TechLab. Each step has a real screenshot.'
        : 'A step-by-step guide for students who **join an instructor\'s class**: you ask to join, your instructor (your Captain) accepts you, follows your progress, and signs your certificate. Each step has a real screenshot.',
      [self
        ? 'Screenshots use a demo student, **Maria Santos** (codename **StarMaria**), taking **Computer Programming 1** Self-paced. Captured 18 October 2026.'
        : 'Screenshots use a demo student, **Leo Cruz** (codename **MeteorTabby95**), in Captain **Carlo Reyes**\'s class for **Computer Programming 1**. Some shared steps show Maria Santos, another demo student. Captured 18 October 2026.',
      'Learning the other way? See the **' + (self ? 'Join Class' : 'Self-paced') + '** student manual.']),
    ...accountParts,
    ...(self ? selfParts : classParts),
    ...lessonParts,
    ...bossParts,
    ...commonEnd,
  ];

  const file = self ? 'TechLab-Student-Manual-Self-paced.docx' : 'TechLab-Student-Manual-Join-Class.docx';
  return save(content, path.join(__dirname, file), title);
}

manual('self').then(() => manual('class'));
