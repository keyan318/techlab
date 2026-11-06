// Admin guide for the TechLab user manual. Build: node admin.cjs (needs the docx package).
const path = require('path');
const kit = require('./lib.cjs');
const { p, bullet, numbered, note, table, step, part, cover, save, Paragraph, TextRun, PageBreak, HeadingLevel } = kit('Admin');

const content = [
  ...cover('TechLab Admin Manual',
    'A step-by-step guide for TechLab admins. Admins watch how TechLab is doing, decide which faculty teach which course, and look after students who learn Self-paced. Each step has a real screenshot.',
    ['Screenshots use a demo admin, **Ana Admin**, with demo faculty **Carlo Reyes** and demo students **Maria Santos** and **Leo Cruz**. Captured 18 October 2026.']),

  new Paragraph({ children: [new PageBreak()] }),
  new Paragraph({ heading: HeadingLevel.HEADING_1, children: [new TextRun('Who has which account')] }),
  ...table(['Who', 'How they get an account'], [
    ['Students', 'Sign up themselves on the TechLab home page'],
    ['Faculty', 'Sign up themselves on the faculty page, with a @catsu.edu.ph email'],
    ['Admins', 'Only another admin (the first admin) can add one. There is no sign-up page.'],
  ], [2400, 6626]),

  // PART 1
  ...part(1, 'Sign in', 'Admins have their own sign-in page.'),
  ...step('Sign in at /admin/login', [
    p('Go to **/admin/login**. Type your admin email and password, then click [[Sign in]].'),
    p('Only admin accounts can sign in here. A student or faculty account gets the same error as a wrong password.'),
    note('First time?', 'The first admin gives you your email and password. Change the password in Settings after you sign in (Step 13).'),
  ], [['01-admin-login.png', 'The admin sign-in page']]),

  // PART 2
  ...part(2, 'The Command deck', 'Your dashboard shows how TechLab is growing over the last 12 weeks.'),
  ...step('Read the top numbers', [
    p('The sidebar has **Dashboard**, **Course Assignment**, **Classes** and **Self-paced**. On the dashboard, the four cards at the top show:'),
    bullet('**Students** and **Faculty**: how many accounts there are, and how many joined this week.'),
    bullet('**Lessons finished**: lessons completed, and how many this week.'),
    bullet('**Active this week**: students who studied in the last 7 days, as a number and a percentage.'),
  ], [['02-dashboard.png', 'The Command deck']]),
  ...step('Switch the charts', [
    p('The big chart has two views. Click [[Growth]] to see students and faculty adding up week by week. Click [[Learning]] to see lessons finished each week on each planet.'),
    p('On the right, **Course coverage** shows how many courses have at least one faculty member, and **Students per planet** shows how many students are on each planet.'),
    note('Tip', 'If Course coverage shows unassigned courses, students can only take those Self-paced. Assign faculty in Part 3.'),
  ], [['03-dashboard-learning.png', 'The Learning view']]),

  // PART 3
  ...part(3, 'Assign faculty to courses', 'Every course has a Self-paced version that you look after. Each faculty member you assign gets their own class for that course.'),
  ...step('Open Course Assignment', [
    p('Click **Course Assignment** in the sidebar. Every course is listed by planet, with:'),
    bullet('**Self-paced**: how many students take it on their own. Click the number to see who.'),
    bullet('**Classes**: each faculty member teaching it and how many students they have. Click a name to see that class.'),
    bullet('**Assign faculty**: a list of faculty you can add.'),
  ], [['04-assignment.png', 'Course Assignment']]),
  ...step('Assign a faculty member', [
    ...numbered([
      'Find the course. Open its **Choose faculty** list and pick a name.',
      'Click [[Add]].',
    ]),
    p('You see **Faculty member added**. Their name shows under Classes with **Not opened** until they open the class from their dashboard. The course also appears on their Courses page.'),
    p('A course can have several faculty. **Everyone assigned** means all faculty already teach it.'),
    note('Good to know', 'Faculty must sign up first (on the faculty page) before you can pick them here.'),
  ], [['05-assign-pick.png', 'Picking a faculty member'], ['06-assigned.png', 'Faculty member added']]),
  ...step('Take a faculty member off a course', [
    p('Point at the faculty member\'s name under **Classes**. A small red [[×]] appears next to it. Click it, then confirm **Take them off this course?**'),
    p('You see **Faculty member removed from the course.**'),
    note('Can\'t remove?', 'If their class still has students, TechLab keeps them on the course so the students aren\'t left without a teacher.', true),
  ], [['06b-remove-hover.png', 'The × next to a faculty member\'s name']]),
  ...step('See who learns a course Self-paced', [
    p('Click the **learners** number in the Self-paced column. A list opens with every student taking that course on their own. Click [[Close]] when done.'),
  ], [['05b-learners.png', 'Self-paced learners for a course']]),

  // PART 4
  ...part(4, 'Look at classes', 'See every faculty member\'s class, its class code and its students.'),
  ...step('Open Classes', [
    p('Click **Classes** in the sidebar. There is one card per course. Each card lists every faculty member teaching it, with their number of students (astronauts) and their **class code**. The code is only the class\'s ID: students never type it, they pick their instructor under Join Class.'),
    p('Self-paced students are not shown here. They are on the Self-paced page (Part 5).'),
  ], [['07-classes.png', 'All classes']]),
  ...step('See a class roster', [
    p('Click [[Roster →]] next to a faculty member. A list opens with the **Captain** (the faculty member) and every student in the class. Click [[Close]] when done.'),
  ], [['08-roster.png', 'A class roster']]),

  // PART 5
  ...part(5, 'Look after Self-paced learners', 'Self-paced students use the standard TechLab course, the default final exam and get a TechLab certificate. Faculty edits never reach them.'),
  ...step('Open Self-paced', [
    p('Click **Self-paced** in the sidebar. Each course card shows how many learners it has and where each one is:'),
    ...table(['Status', 'What it means'], [
      ['Studying', 'Still working through the lessons'],
      ['Ready for final', 'Finished every lesson, can take the final exam'],
      ['Passed', 'Passed the final and earned the certificate (with their score)'],
      ['Failed final', 'Failed the final and has to redo the course'],
      ['Banned', 'Caught leaving the final exam twice'],
    ], [2400, 6626]),
    p('**Final auto-builds on first finish** means you don\'t have to write the final exam. TechLab builds the default one the first time a learner finishes the course.'),
  ], [['09-self-paced.png', 'Self-paced learners by course']]),
  ...step('Allow a retake or lift a ban', [
    p('Next to a learner who **failed** the final, click [[Allow retake]] to let them try again. Next to a **banned** learner, click [[Lift ban]] to let them back into the final.'),
    note('Why bans happen', 'Leaving full screen, switching tabs or trying to screenshot during the final counts as a strike. Two strikes ban the student from the final until you lift it.', true),
  ], []),

  // PART 6
  ...part(6, 'Your account and other admins', 'Change your details and password, and manage who else is an admin.'),
  ...step('Open the account menu', [
    p('Click your avatar at the bottom of the sidebar. Choose [[Change avatar]], [[Settings]] or [[Log out]].'),
  ], [['11-account-menu.png', 'The admin account menu']]),
  ...step('Update your profile and password', [
    bullet('**Profile**: change your first and last name, then click [[Save profile]]. Your email can\'t be changed.'),
    bullet('**Your captain**: click [[Make my captain]] to pick an avatar.'),
    bullet('**Preferences**: sound and light mode, saved on this browser only.'),
    bullet('**Password**: type your current password and the new one twice (at least 12 characters), then click [[Change password]].'),
  ], [['12-settings.png', 'Admin settings']]),
  ...step('Add or remove admins', [
    p('Scroll to **Admins** at the bottom of Settings. Every admin is listed. Only the **first admin** can add or remove admins.'),
    p('If you are the first admin, you also see **Add an admin**:'),
    ...numbered([
      'Type their **first name**, **last name** and **email**.',
      'Pick a **password** (at least 12 characters) and type it again.',
      'Click [[Add admin]]. Give them the email and password. They sign in at **/admin/login** and can change the password in their own Settings.',
    ]),
    p('To remove an admin, click [[Remove]] next to their name and confirm. They can\'t sign in any more.'),
  ], [['13-admin-accounts.png', 'The Admins list']]),
];

save(content, path.join(__dirname, 'TechLab-Admin-Manual.docx'), 'TechLab Admin Manual');
