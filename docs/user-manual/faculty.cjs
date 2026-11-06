// Faculty guide for the TechLab user manual. Build: node faculty.cjs (needs the docx package).
const path = require('path');
const kit = require('./lib.cjs');
const { p, bullet, numbered, note, table, step, part, cover, save } = kit('Faculty');

const content = [
  ...cover('TechLab Faculty Manual',
    'A step-by-step guide for faculty (called Captains in TechLab). It covers setting up your account, running your class, and changing lessons, quizzes and exams for your students. Each step has a real screenshot.',
    ['Screenshots use a demo faculty member, **Carlo Reyes**, teaching **Computer Programming 1**, with a demo student, **Leo Cruz**. Captured 18 October 2026.']),

  // PART 1
  ...part(1, 'Create your faculty account', 'Faculty sign up on their own page, with their school email.'),
  ...step('Open the faculty page', [
    p('Go to the TechLab faculty page (**/faculty**). You can also click **Faculty? Sign in here** at the bottom of the main TechLab page.'),
    p('Click [[Create faculty account]]. Already have one? Click [[Faculty sign in]] and skip to Step 6.'),
  ], [['01-faculty-landing.png', 'The faculty welcome page']]),
  ...step('Fill in the sign-up form', [
    ...numbered([
      'Type your **first name** and **last name**.',
      'Type your **school email**. It must end in **@catsu.edu.ph**. Other emails are not accepted.',
      'Pick a **password** with at least 8 characters, then type it again.',
    ]),
    p('Click [[Create account]].'),
  ], [['02-register.png', 'The faculty sign-up form']]),
  ...step('Agree to the rules', [
    p('Read the TechLab rules. Faculty get one extra rule, **Captain rules**: keep student info private, use it only for teaching, and only upload files you are allowed to share.'),
    p('Tick **I have read and agree**, then click [[Agree and continue]].'),
  ], [['03-terms.png', 'The rules, including Captain rules']]),
  ...step('Meet Astro and build your captain', [
    p('Astro welcomes you. Click [[Continue]] through the messages, then [[Suit me up]].'),
    p('Pick your animal, suit and helmet, then click [[Looks good!]]. Students see this avatar next to your name.'),
  ], [['05-avatar.png', 'Building your captain avatar']]),
  ...step('Take command', [
    p('Check your captain and click [[Take command]] to open your dashboard. Click [[Change something]] to go back.'),
  ], [['06-ready.png', 'Ready to take command']]),
  ...step('Sign in next time', [
    p('Go to **/faculty/login**, type your school email and password, and click [[Sign in]]. A student account can\'t sign in here.'),
  ], [['13-faculty-login.png', 'The faculty sign-in page']]),

  // PART 2
  ...part(2, 'Your dashboard', 'Your dashboard shows how your students are doing, your courses, and who is waiting to join.'),
  ...step('Follow Astro\'s tour', [
    p('The first time you arrive, Astro gives an 11-stop tour of the faculty pages. Click [[Next]] or press the right arrow key. Click [[Skip tour]] to close it, or the **?** button in the sidebar to see it again.'),
  ], [['07-tour.png', 'Astro\'s faculty tour']]),
  ...step('Wait for your admin to give you a course', [
    p('A new faculty account has no course yet. The dashboard says **You don\'t have a course yet**.'),
    p('Your **admin** assigns you the courses you teach. Ask them to add you. Once they do, the course appears on your dashboard and on the **Courses** page.'),
    note('Tip', 'Want to make a brand-new course of your own instead? See Step 27.'),
  ], [['08-dashboard-empty.png', 'The dashboard before a course is assigned']]),
  ...step('Read your dashboard', [
    p('The sidebar has **Dashboard**, **Courses**, **Crew** and **Materials**. On the dashboard:'),
    bullet('**Your crew at a glance**: students, active students, lessons completed, average progress and certificates earned. Pick a course or 7, 14 or 30 days.'),
    bullet('**Activity** chart: lessons completed, active time or active students per day.'),
    bullet('**Students** table: each student\'s progress, lessons, active time and last visit. **Needs a nudge** shows students who haven\'t been active.'),
  ], [['15-accepted.png', 'Dashboard numbers after a student joins']]),

  // PART 3
  ...part(3, 'Run your class', 'Every course you teach gets its own class, with a class code as its ID. Only students you accept can get in.'),
  ...table(['', 'Your class', 'Self-paced'], [
    ['Who is in it', 'Students you accept', 'Students learning on their own'],
    ['Lessons', 'Your edited version', 'The standard TechLab version'],
    ['Mini bosses', 'One try, you see scores', 'Must pass to move on'],
    ['Certificate', 'Signed by you', 'TechLab certificate'],
  ], [2000, 3500, 3526]),
  ...step('Find your course', [
    p('Click **Courses** in the sidebar. Each course card shows its modules, lessons and level.'),
    bullet('[[Manage]] opens the course lessons so you can edit them (Part 7).'),
    bullet('[[Edit card]] changes the picture, title and subtitle students see (Step 26).'),
    bullet('[[+ Build a course]] makes a new course from scratch (Step 27).'),
  ], [['10-courses.png', 'Your courses']]),
  ...step('Accept students into your class', [
    p('Scroll down your dashboard to **Your courses**. Each course shows its **class code** (for example **Z8W214**) and how many students joined or are waiting. The code is just your class\'s ID: students never type it.'),
    p('Students pick you under **Join Class** and click **Ask to join**. They appear under **Waiting to join**.'),
    bullet('Click [[Accept]] to let them in. They are enrolled right away.'),
    bullet('Click [[Decline]] to say no. TechLab emails the student to tell them.'),
    note('Good to know', 'A student who already chose Self-paced or another class for this course can\'t ask to join yours.'),
  ], [['14-join-request.png', 'Your courses, certificate signature and the waiting list']]),
  ...step('See your crew', [
    p('Click **Crew** in the sidebar (or [[Manage course]] on the dashboard). You see your class code and every student with their course progress, XP, gems and day streak.'),
    p('The tabs at the top open the class\'s **Materials**, **Lesson quizzes** and **Exams**.'),
  ], [['19-roster.png', 'Your crew list']]),
  ...step('Add your certificate signature', [
    p('Students who pass your class\'s final exam get a certificate signed by you. On the dashboard, under **Certificate signature**, click [[Choose File]], pick a picture of your signature (PNG or JPG, dark ink on a white or clear background) and click [[Upload]].'),
    p('Until you upload one, your name is written on the certificate instead.'),
  ], []),

  // PART 4
  ...part(4, 'Share learning materials', 'Add files and links for your students, grouped into modules. Astro can answer students\' questions using your PDF, Word, PowerPoint and text files.'),
  ...step('Add a module', [
    p('Open your course\'s **Materials** tab. Under **Add a module**, type a **title** and an optional **short description**.'),
    p('You can drop files in now (PDF, Word, PowerPoint, Excel, images or ZIP) or add them later. Click [[Add module]].'),
  ], [['17-add-module.png', 'Adding a module']]),
  ...step('Add files and links', [
    p('Inside a module, click [[Upload files]] to add files, or [[Add link]] to add a web link (type a link title and the address, then [[Save link]]).'),
    p('Students see new materials right away. Click the **✕** next to an item to remove it, or the bin icon to delete the whole module.'),
  ], [['18-module-added.png', 'A module with a link']]),

  // PART 5
  ...part(5, 'Edit lesson quizzes', 'Every lesson ends with a ready-made Knowledge Check quiz. You can keep it or change it for your class.'),
  ...step('Pick a lesson quiz', [
    p('Open the **Lesson quizzes** tab. Every lesson is listed by module, with its number of questions. **Default** means it is still the TechLab version.'),
    p('Click [[Edit]] next to a lesson.'),
  ], [['21-lesson-quizzes.png', 'All lesson quizzes']]),
  ...step('Change questions and answers', [
    bullet('Edit the **question**, the **choices** and the **explanation** students see after they answer.'),
    bullet('Click the circle next to the right answer to mark it.'),
    bullet('Use **↑ ↓** to reorder questions, **✕** to delete one, and [[+ Add question]] to add more.'),
    p('Click [[Save quiz]] when you are done. You can always reset a quiz back to the default.'),
    note('Tip', 'Keep all choices about the same length, and don\'t always put the right answer in the same spot. Students notice patterns.'),
  ], [['22-lesson-quiz-edit.png', 'Editing a lesson quiz']]),

  // PART 6
  ...part(6, 'Mini bosses and the final exam', 'Mini bosses close each module. The final exam (the Final Boss) gives the certificate.'),
  ...step('Check your mini bosses', [
    p('Open the **Exams** tab. Each module has a ready-made **Mini Boss** with at least 30 questions.'),
    bullet('Students in your class take it **once**, in full screen with a timer, after the module\'s lessons. They earn 10 XP per right answer and move on whether they pass or not.'),
    bullet('Leaving full screen, switching tabs or trying to screenshot ends it as a fail with no XP, and **you get an email**.'),
    bullet('Click [[Edit]] to change its questions, time or pass mark, or [[Reset to default]] to undo your changes. Scores appear under each mini boss.'),
  ], [['24-exams.png', 'The Exams tab']]),
  ...step('Create the final exam', [
    note('Do this first', 'Students can only earn the certificate by passing your final exam. Until you create and publish one, students who finish every lesson have to wait.', true),
    p('Click [[Create final exam]]. In the **Questions** tab:'),
    ...numbered([
      'Type each **question**, and pick the answer type: multiple choice, checkboxes, drop-down or true/false.',
      'Type the options and click the circle next to the right answer.',
      'Click [[Add question]] for the next one. Use the icons to move, copy or delete a question.',
    ]),
    p('The panel on the left shows the **Course summary** and **My materials**, so you can write questions from them.'),
  ], [['25-final-builder.png', 'Writing final exam questions']]),
  ...step('Set the time and pass mark, then publish', [
    p('Open the **Settings** tab:'),
    bullet('**Time limit** (required): the clock starts when the student presses Start. When it hits zero, their answers are handed in.'),
    bullet('**Deadline** (optional): after this, students who haven\'t started can\'t open it.'),
    bullet('**Pass mark**: the score needed for the certificate (75% by default).'),
    p('Click [[Save draft]] to keep working later, or [[Publish]] to make it live.'),
    note('How the final works', 'Leaving full screen, switching tabs or trying to screenshot fails the exam. The student goes back to the start of the course and you get an email. That is a strike. A second strike bans them from the final until you allow a retake. Students see their score but not which answers were wrong.', true),
  ], [['26-final-settings.png', 'Final exam settings']]),

  // PART 7
  ...part(7, 'Edit course content', 'You can change the lessons for your class. Your changes only reach your class. Self-paced students keep the standard course.'),
  ...step('Open a lesson in edit mode', [
    p('On the **Courses** page, click [[Manage]]. The lesson opens with [[✏️ Edit]] buttons on every part: the title, each stage, each checkpoint, the quiz and the lab.'),
    p('Use the **Course Outline** on the left to move between lessons.'),
  ], [['20-lesson-edit-mode.png', 'A lesson in edit mode']]),
  ...step('Change modules and lessons', [
    p('Click [[✏️ Edit modules & lessons]] above the outline. You can:'),
    bullet('Rename any module or lesson by typing in its box.'),
    bullet('Use **↑ ↓** to change the order.'),
    bullet('Click [[Hide]] to hide one from students. Their progress is kept, so you can bring it back.'),
    bullet('Click [[+ Add lesson]] to add a new lesson with empty stages for you to fill in.'),
    p('Click [[Save]], or [[Reset outline]] to go back to the standard one.'),
  ], [['27-outline-editor.png', 'The course outline editor']]),
  ...step('Edit a stage\'s text', [
    p('Click [[✏️ Edit]] on any part of the lesson. Change the text, using the buttons for bold, italics, code, lists and links. Click [[Save]].'),
    p('[[✏️ Edit checkpoint]] works the same way for the question at the end of each stage.'),
  ], [['28-stage-editor.png', 'Editing the lesson title and objective']]),
  ...step('Edit the coding lab', [
    p('Click [[✏️ Edit lab]] on Stage 5. You can change:'),
    bullet('The **title**, **difficulty**, **instructions** and an optional **hint** from Astro.'),
    bullet('**answer.c** (your correct program) and **starter.c** (what students start with).'),
    bullet('The **expected output**. Students pass when their program prints exactly this.'),
    p('Click [[Run answer code]] to test, then [[Save lab]]. Saving runs your answer first, so a wrong expected output can\'t go live.'),
    p('Networking and Cybersecurity lessons have simulator labs instead, edited the same way.'),
  ], [['29-lab-editor.png', 'The coding lab editor']]),
  ...step('Change the course card', [
    p('On the **Courses** page, click [[Edit card]]. Change the **cover image**, **title**, **subtitle** or **level**, then click [[Save]]. This is the card your students pick the course from.'),
  ], [['34-edit-card.png', 'Editing the course card']]),

  // PART 8
  ...part(8, 'More tools', 'Build your own course and keep your class schedule.'),
  ...step('Build a course from scratch', [
    p('On the **Courses** page, click [[+ Build a course]]. Pick a **planet** (Programming, Networking or Cybersecurity), type a **title** and an optional **subtitle**, and click [[Create course]].'),
    p('The new course starts empty and hidden from students. Add modules and lessons with the same editor as Part 7, then publish it when it\'s ready.'),
  ], [['33-build-course.png', 'Building a new course']]),
  ...step('Upload your class schedule', [
    p('Open **My Classes** (**/faculty/classes**). Click [[Upload schedule]] and choose a file or a photo of your schedule. Astro reads it and lays out your classes here.'),
  ], [['32-classes.png', 'My Classes']]),
  ...step('Settings and logging out', [
    p('Click your avatar at the bottom of the sidebar. Choose [[Settings]] to change your name, password or captain avatar, or [[Log out]].'),
    p('You can delete your account from Settings. If you still lead a course with students, TechLab asks you to hand it over first.'),
  ], []),
];

save(content, path.join(__dirname, 'TechLab-Faculty-Manual.docx'), 'TechLab Faculty Manual');
