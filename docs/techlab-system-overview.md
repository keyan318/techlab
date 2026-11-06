# TechLab — Full System Overview (as of 2026-10-17)

Checked against the code on branch `networking-course`. Items marked "(verified)" correct older notes.

# PART A — What TechLab is

**TechLab** is a gamified web learning platform for CICT students (CatSU). It teaches **Programming, Networking
and Cybersecurity** through space-themed "planets", hands-on in-browser labs, and an AI tutor named **Astro**.
Students are called **astronauts**, faculty are **Captains**, and each course's group of students is a **crew**.

**Story world:** Astro (Captain), Rivet (Engineer) and Volt (Shield Tech) live on planet **Codexia**, which
**Planet Doom** (led by **Commander Vex**) wants to invade. Each course is a story season.

## Tech stack
| Layer | What is used |
|---|---|
| Backend | Laravel 13 (PHP) |
| Frontend | Server-rendered Blade pages + Alpine.js + Tailwind CSS (built with Vite). Not a single-page app. |
| Database | PostgreSQL hosted on Supabase (tests use in-memory SQLite) |
| AI | NVIDIA NIM, model **Nemotron 3 Ultra** (`nvidia/nemotron-3-ultra-550b-a55b`); a vision model (Llama 3.2 11B Vision) for reading uploaded images |
| Email | Gmail SMTP, sent through a database queue |
| Code labs | Code runs **in the student's browser**: C via **JSCPP** (Programming 1) and **PicoC** (Programming 2); Python via Pyodide (hidden course). No server-side code runner. |
| Simulator labs | **NetSim** (network simulator, incl. Cisco IOS routers/switches), **Relay Console** (router command line for BGP, QoS, VPN…), **Citadel Sim** (cyber attack-and-defend scenarios) |

## The three planets and six live courses
| Planet | Course (code) | Modules / Lessons | Hands-on lab | Story |
|---|---|---|---|---|
| Programming City | Computer Programming 1 (CC102) | 4 / 21 | "Code it yourself" C editor (JSCPP) | "The Dead Console: wake the Wanderer" (Season 1) |
| Programming City | Computer Programming 2 (CC103) — requires CP1 | 6 / 31 | C editor (PicoC: structs, pointers, malloc, files) | "Switch the City On" (Season 2) |
| Networking Nebula | Networking 1 (ITP422) | 4 / 24 | 24 NetSim labs ("Configure it yourself") | "Call Earth" |
| Networking Nebula | Networking 2 (ITP831) — requires N1 | 6 / 32 | 18 NetSim Cisco IOS labs + 14 Relay Console labs | "Light the Grid" (Season 2) |
| Cybersecurity Citadel | Information Assurance & Security 1 (ITP1232) | 5 / 24 | 22 Citadel Sim + 2 NetSim labs ("Defend it yourself") | "Siege of the Citadel" |
| Cybersecurity Citadel | Information Assurance & Security 2 (ITP1333) — requires IAS1 | 8 / 40 | 40 Citadel Sim labs | "Rebuilding the Citadel" |

Total: **172 lessons**, **1,011 default mini-boss questions**. A **Python** course exists but is **hidden** (switched off).
Faculty can also add **their own courses** (Part D).

---

# PART B — The big change: Self-paced vs Join Class

Every course now has **two ways in ("tracks")**. Internally both are "crews":

| | **Self-paced** | **Join Class** |
|---|---|---|
| What it is | One system crew per course, no teacher | One class per faculty member; a course can have **many** faculty, each with their own class |
| How to join | Click **Self-paced** → in instantly, no approval | Pick an instructor from a **dropdown** → "Ask to join" → faculty **Accepts** or **Declines** |
| Prerequisite | **Yes.** CP2 needs CP1, N2 needs N1, IAS2 needs IAS1 finished (all lessons done **and** final exam passed). For a faculty-built course: pass the planet's base course **Module 1 mini boss** | **No check.** The faculty member decides who belongs (e.g. can decline a 4th-year who already passed) |
| Course content | Always the **default TechLab course**. Faculty edits **never** reach it | The faculty member's **own edited version**. Their edits reach **only their class** |
| Mini bosses | Must pass to unlock the next module; fail = 0 XP + 60-minute wait | One try, pass or fail you move on; score recorded |
| Final exam | Default final, **built automatically** from the lessons' Knowledge Checks | Written by the faculty member |
| Supplementary Materials tab | Not shown (nobody uploads) | Faculty's uploaded files and links |
| Certificate | "Self-paced · Issued by TechLab" | Carries the **faculty member's name and uploaded digital signature** |
| Watched by | **Admin** (Self-paced page) | **The faculty member** (their dashboard) |

**Rules (verified in code):**
- A student is on **one track per course**, and **once picked, the track is final** — a class member can't switch to Self-paced, and a self-paced student can't ask to join a class for that course. *(An early plan allowed switching; the built system does not.)*
- Only **one pending request per course**: asking a second faculty member withdraws the first. Choosing Self-paced withdraws any pending request.
- When accepted, the student is enrolled **immediately**; the home page shows once "You were accepted into [faculty]'s class". When declined, the student gets an email and can ask again.
- Progress is stored **per track** (each lesson record has the crew it was earned in). XP from a lesson is counted only once and is never lost.
- Each class also has a 6-character **class code** (shown to the faculty member). It only works for a student that faculty member already accepted, so sharing it lets nobody else in. The normal way in is the dropdown.

---

# PART C — Student workflow

### 1. Sign up and onboarding
1. **Register** (`/register`): first name, last name, email, password (8+), **program** (IT, IS, CS, BLIS, EMC), **year level** (1st–4th), and **one starting planet**.
2. **Terms and Conditions** (`/terms`): must tick "I agree" before anything else (re-asked if the Terms version changes). "I don't agree" logs out.
3. **Welcome** (`/welcome`): Astro says hello; the student builds an **animal astronaut** avatar (animal, coat, suit, suit colour, visor, patch, helmet) and picks a unique **codename** (classmates see the codename, not the real name).
4. **Home dashboard**, where **Astro's guided tour** starts by itself the first time (it can be replayed from the sidebar). Tours exist for Home, Planets, Courses, a course, a lesson, Chat, Shop and Settings.

**Student sidebar:** Home · Chat (with recent conversations) · Planets · Shop · Astro's tour · theme (light/dark) · sound on/off · account menu (Settings, log out).

### 2. Planets and unlocking
- The student starts with **one planet**. Finishing **one module** on a planet unlocks the **next planet** in order (Programming → Networking → Cybersecurity).
- Each planet lists its courses. On a course card (and on the Home hero card) the student sees **Self-paced** and **Join Class** (instructor dropdown), plus a note if a prerequisite blocks Self-paced.

### 3. The course hub
After joining, the course opens to its hub with tabs: **Home** (chapters/modules and progress), **Supplementary Materials** (class only), **Crew** (the captain and a crew leaderboard ranked by level). The hub also shows the **mini bosses** and the **Final Boss**, and the **certificate** once earned.

### 4. Inside a lesson (fixed 5-stage flow)
1. **Mission Brief** — what you'll learn.
2. **Picture It** (everyday analogy + picture; done in Programming 1) **or** the crew's story ("Astro / Rivet / Volt Says") in other courses.
3. **Lab / Field Diagram** — worked code example or diagram.
4. **Knowledge Check** — the lesson quiz.
5. **Hands-on stage** — "Code it yourself" (C editor), "Configure it yourself" (NetSim / Relay Console) or "Defend it yourself" (Citadel Sim).

Gates inside a lesson:
- **Checkpoints:** one question at the end of a stage; the next stage stays locked until it's right. Retries allowed, but the answer is never shown, and each miss adds a wait (5 s, 10 s, then 20 s).
- **Knowledge Check:** first answer to each question counts (+10 XP per correct). Pass = **at most 2 mistakes**. Pass it to open the hands-on stage.
- **Failed Knowledge Check:** "Ask Astro about my mistakes" opens a chat with Astro's review already written. **This review is written by the system from the answer key, not by the AI** (it was made reliable after AI timeouts). Then the student retries only the missed questions — **retries give no XP**.
- **Hands-on stage:** code output is checked by the server against the answer key; simulator labs report a pass. Passing completes the lesson (**+100 XP**) and unlocks the next lesson.
- Paid help: **Astro's hint** in the code editor (**50 XP**, once per lesson) and **Astro's live lab-help chat** in Citadel Sim labs (**60 XP** per lab attempt, then free follow-ups; this one uses the AI and sees the student's terminal).
- Lessons unlock **in order**; you can't skip ahead by URL.

### 5. Mini bosses (end of every module)
A full-screen exam after each module (default 45 minutes, pass mark 75%, at least 30 default questions per module). **+10 XP per right answer.** Same anti-cheating rules as the Final Boss (leaving full screen, switching tab/window, reloading, screenshot attempt = instant fail + alert).
- **Self-paced:** next module stays locked until passed. A fail earns **0 XP** and the student waits **60 minutes** to study before retrying. The Final Boss waits until every mini boss is passed.
- **Class:** **one try**; pass or fail, the student moves on; the score and XP are recorded for the faculty member.

### 6. Final Boss (final exam) and certificate
Opens when every lesson is done. Full screen, big timer (turns amber under 5 min, red in the last minute), question map, and an alarm sound on any violation. Question types: multiple choice, true/false, checkboxes, dropdown.
- Leaving full screen, switching tab/window, leaving/reloading the page, or trying a screenshot = **instant fail + 1 strike**. Running out of time = fail, **no strike**.
- **Any fail** (low score, time up, caught) **wipes the student's progress in that course**: they redo every lesson, then the final reopens.
- **2 strikes** (across tries) = **banned** from the final (progress not wiped). Only "Allow retake / Lift ban" (faculty for a class, admin for self-paced) reopens it and gives the wiped lessons back.
- After the final the student sees **only the score** — no answers, no mistakes (so retries can't be memorised). Ordinary quizzes and mini bosses show the score; Knowledge Checks explain mistakes.
- Faculty (class) or admins (self-paced) get an **email + dashboard alert** for every violation. A watermark shows the student's name.
- **Pass** → **Certificate of Completion** with student name, course title and code, number of chapters/lessons/labs, XP, issue date (date the final was passed), and a unique ID like `TL-ITP1232-7F3A-09C2`. Class certificate: faculty name + signature. Self-paced: issued by TechLab.

### 7. Chat with Astro (AI tutor)
ChatGPT-style chat (streams the answer), with: conversation history (rename, pin, delete), **Sources panel** (attach Planet lessons the student has unlocked and the faculty's materials), file/photo attachments, voice input, cited sources/links. Astro picks **Fast** or **Deep** mode by simple rules (long messages, code, errors, "step by step"…), no extra AI call.
**Cost:** **10 free messages per day**, then **20 XP per message** (30 XP with a file attached). If the AI call fails, XP is refunded. Only students pay. *(The Studio panel is gone — see Part F.)*

### 8. Other student pages
- **Progress** page: full stats (XP, lessons/modules done, weekly activity, performance per planet, leaderboard).
- **My Courses**: every course they're on, with Self-paced or the captain's name.
- **Shop** (Part G) and **Settings**: name, program and year level, password, avatar, sound/theme, delete account.

---

# PART D — Faculty (Captain) workflow

### 1. Sign up and onboarding
Faculty register at `/faculty/register` with a **school email (@catsu.edu.ph only)** → agree to the Terms (plus extra "Captain rules": keep student info private, use it only for teaching, upload only what you may share) → **captain welcome**: build a captain avatar (captain-only animals: Lion, Gorilla, Eagle; captain suits: Admiral, Marshal, Commodore; no codename — crews see the real name) → dashboard + **captain tour**.

**Faculty sidebar:** Dashboard · Courses · Crew (expands to every crew they watch, grouped by planet) · Materials · Astro's tour · account menu. **No Chat** (faculty Astro chat was removed). The Classes (weekly schedule) and Calendar items are **hidden, not deleted**.

### 2. Getting a class — TWO ways (major change)
**a) Open a class for an assigned course.** The admin assigns the faculty member to an existing course (several faculty can be assigned to the same course). On the dashboard the faculty member clicks **"Open class"** → their own class is created (with its code) → they now appear in that course's **Join Class** dropdown for students.

**b) Build a brand-new course from scratch ("+ Build a course").** On the Courses page the faculty member:
1. Picks a **planet**, a **title**, an optional subtitle, and the **hands-on lab type**: Programming always uses the code editor; Networking/Cybersecurity choose **NetSim**, **Relay Console**, **Citadel Sim** or **None** (lessons and quizzes only).
2. The course is created as a **draft, hidden from students**. The faculty member is automatically its teacher, and **both tracks are opened right away**: their own class **and** a self-paced crew.
3. They build it with the same editor used for every course: add modules and lessons, write lesson pages, checkpoints and Knowledge Checks, add labs (a simulator lab starts by **copying one of TechLab's existing labs as a template**, then editing it), write mini bosses (a new module's mini boss starts empty) and the final exam.
4. **Publish** (needs at least one lesson) → it appears on the planet for students, with Self-paced and Join Class.
   - Self-paced students see the **creator's version** (there's no default on disk).
   - The creator also **watches the self-paced learners** of that course (the admin's Self-paced page covers only TechLab's built-in courses).
   - Self-paced prerequisite: pass Module 1's mini boss of the planet's base course (CP1, Networking 1 or IAS1).

### 3. Accepting students
Join requests appear on the faculty dashboard (oldest first) → **Accept** (student enrolled instantly, class progress starts fresh) or **Decline** (student emailed, may ask again). No prerequisite check — the faculty member decides.

### 4. Editing a course (only reaches their own class)
The faculty member opens the course in the **same course player students use** and edits it in place. Edits are saved **on top of** the original lesson files ("copy-on-write"), per class, and every edit can be **reset to the TechLab default**. Self-paced students and other faculty members' classes never see them.
- **Course card:** title, subtitle, level, cover art.
- **Outline:** rename, reorder, hide and add modules and lessons (keys are never reused, so student progress stays put).
- **Lesson page:** title, objective, section text and images, **checkpoints**. Interactive parts (forms, labs, answer keys) are locked so they can't break.
- **Knowledge Check (Lesson quizzes tab):** rewrite questions, choices, right answer, explanation; or reset.
- **Coding lab ("Lab studio")**: full-screen editor that looks like the student's: starter code, answer code, expected output, hint. **On save the answer code is run in the browser and must produce the expected output**, or the save is refused.
- **Simulator lab:** edit title, story messages, step instructions, objectives and their check values (and Citadel file contents). If a change affects grading, the faculty member must **"Test this version"** and pass it in the simulator before it saves.
- **Exams tab:** each module's **mini boss** (edit questions in the quiz builder, set time and pass mark, or reset to TechLab's questions; see every score and violation) and the **Final exam** (results, strikes, Allow retake / Lift ban).
- **Final exam builder:** Google-Forms style, **no AI**. Beside it: module summaries read from the lessons (also downloadable as a summary PDF) and the faculty's own materials; each question can carry a "Based on" lesson tag. A final someone has already taken can't be rewritten. Until the final is published, the dashboard warns "Create the final exam so students can pass".
- **Materials:** create learning modules and upload files or add links → students' **Supplementary Materials** tab (with "new" badges).
- **Signature:** upload a PNG/JPG signature (max 1 MB) → printed on their class's certificates.

### 5. Monitoring
- **Dashboard analytics:** charts, stat tiles and a student table, filtered by course and date range (last 60 days kept), covering their classes and the self-paced run of any course they built.
- **Alerts:** students caught during a final or mini boss (e.g. "Caught: switched tab or window · strike 1 of 2").
- **Crew / roster pages:** each student's progress %, XP and gems per course.
- Faculty use Astro and the course for free; XP, gems and streaks are student-only.

---

# PART E — Admin workflow
- Admin accounts are **not** made by sign-up: the first is created with `php artisan admin:create`; passwords reset with `php artisan admin:password`. Admins log in at `/admin/login` (rate-limited) and skip the Terms screen.
- **Admin sidebar:** Dashboard · Course Assignment · Classes · Self-paced · account menu.
- **Dashboard ("Command deck"):** community growth (students and faculty per week, 12 weeks), lessons finished per planet, course coverage, students per planet, **planet leaderboards**, **students by program and year level**, recent activity.
- **Course Assignment:** per course: self-paced learner count, open classes, **assign faculty** (many per course). Unassign is allowed only while that faculty member's class has **no students**.
- **Classes:** one card per course listing every faculty member's class, its code and roster.
- **Self-paced:** every self-paced learner per course, their progress and default-final status (strikes, bans) with **Allow retake / Lift ban**. Admins receive the violation emails for self-paced exams.
- **Default finals:** built automatically (2 questions from each lesson's Knowledge Check, up to 40; time = 1.5 min per question, at least 15 min; pass 75%) the first time a self-paced student finishes every lesson, or with `php artisan techlab:default-finals`. There is no admin screen to edit them.
- **Settings → Admins:** only the **first (owner) admin** can add other admins (name, email, password of 12+ characters) or remove them; others only see the list. Admins can't delete their own account.
- Admins also have a captain avatar.

---

# PART F — Features REMOVED or hidden

| Removed / changed | What it was | Now |
|---|---|---|
| **Faculty Astro chat ("Astro for Captains")** | A teaching-assistant AI chat for faculty (lesson plans, rubrics…) with its own Studio panel and a **PowerPoint/slide deck generator** | **Deleted** (route, controller and sidebar link gone). Faculty have no AI chat. |
| **Student Studio panel** | Right-hand panel in student chat: AI **Quiz, Flashcards, Key terms, Infographic, Reports**, saved outputs and notes | **Removed from the student chat** (students get Sources + chat only). "Save to note" on Astro's replies was also removed. |
| **Course hub "Quiz" tab** | Faculty-made ad-hoc quizzes | **Removed.** Assessment is now checkpoints + Knowledge Checks + **mini bosses** + **Final Boss**. |
| **AI in the quiz builder** | Astro drafted quiz questions | **Removed** — builder is manual (Google-Forms style). |
| **AI quiz-mistake review** | AI explained a failed Knowledge Check | Replaced by a **system-written** review in Astro's voice. |
| **"Accepted" email with join code** | Accepting emailed a code the student typed in | Accepting now **enrolls instantly**; only declines send an email. |
| **One teacher per course** | Each course had only one faculty member, whose edits reached everyone | **Many faculty per course**, edits per class, plus Self-paced. |
| Hidden, not deleted | Faculty **Classes** (upload a weekly schedule, AI organises it) and **Calendar** sidebar items; the **Python** course | Still in the code, switched off. |

---

# PART G — Gamification (exact numbers)

**Earning XP** (all of these raise your **level**):
| Action | XP |
|---|---|
| Complete a lesson (pass its coding challenge or simulator lab) | **+100** |
| Correct first answer in a Knowledge Check | **+10** each (retries: 0) |
| Mini boss | **+10 per right answer** (self-paced fail: 0) |
| Daily login (first Home visit each day) | **+10** |

**Spending XP** (lowers the **balance** but **never the level**): Astro's hint 50 · Citadel lab-help chat 60 · Astro chat 20 per message after 10 free a day (30 with a file) · Shop items.

**Gems:** **10 gems per finished module.** Gems can be traded in the Shop at **1 gem = 50 XP** (verified in `config/ranks.php`). Traded XP is spendable but does not raise the level.

**Levels:** based on XP **earned**. Level 1→2 costs 50 XP, each next level 5 XP more (55, 60…). Finishing every course perfectly lands around level 100.

**11 space ranks** (one every 10 levels): Stardust → Comet → Meteor → Moonwalker → Orbiter → Nova → Nebula → Pulsar → Quasar → Supernova → **Galaxy Legend** (100+, a star for each level past 99). A **"Rank up!"** celebration pops once per new rank.

**Avatar frames:** 3 per rank (33 total): a free default plus two to buy (frame A = 100 × (rank number + 1) XP, frame B = 150 × (rank number + 1), rank 1 = Stardust: e.g. Stardust 200/300, Galaxy Legend 1,200/1,800). They go on sale when the student reaches that rank.

**Shop** (XP only): animals — Cat free, Dog 200, Bird 300, Cow 400, Pig 400; suits — Explorer free, Pilot 300, Engineer 600, Commander 900; frames; gem exchange. (All students were reset to the free cat when animals became paid.)

**Leaderboards** rank by **level, then XP earned** (spending can't drop anyone): a **planet leaderboard** on Home/Progress (everyone on that planet, both tracks) and a **crew leaderboard** in each course's Crew tab.

**Streaks:** a day counts if the student studied or finished a lesson. The streak flame changes every 10 days: Spark → Ember → Blaze → Inferno → Plasma → Nova. A streak pop-up shows once a day (with the +10 XP login bonus).

**Other game feel:** mini bosses and the "Final Boss", pixel-art planets and mascots, sounds and music (can be muted), Astro's cheers, story seasons per course.

---

# PART H — Terms and Conditions (what users agree to)
Version `2026-10-16`. Students and faculty must agree before using TechLab; changing the version makes everyone agree again.
1. **Be yourself** — real name, keep your account private; classmates see your codename.
2. **Play fair** — quizzes are one try with a timer; leaving the Final Boss fails it with a strike; any failed final means redoing the course; two strikes = banned.
3. **Astro is an AI** — messages go to NVIDIA's AI, can be wrong; don't share private info.
4. **Hack only the labs** — cyber skills only in TechLab's labs.
5. **Rewards are just for fun** — XP, gems and suits have no money value; cheating can lose them and the certificate.
6. **Your captain can see your work** — captains see progress, scores and exam results of their class; "we don't sell your info".
7. **Be kind** — no bullying, spam or messing with the site.
8. *(Faculty only)* **Captain rules** — keep student info private, use it only for teaching, upload only what you may share.
Users can delete their account any time (faculty only once they no longer run a class or course).

---

# PART I — Glossary (terms used in the system)
| Term | Meaning |
|---|---|
| Planet | A subject area: Programming City, Networking Nebula, Cybersecurity Citadel |
| Course | A subject course on a planet (e.g. CC102); built-in or faculty-built |
| Module / Chapter | A group of lessons; ends with a mini boss |
| Lesson | One page with 5 stages |
| Astronaut | A student |
| Captain | A faculty member |
| Crew | The students in one run of a course (a class or the self-paced run) |
| Track | How a student takes a course: Self-paced or a Class |
| Self-paced | Learn alone, default course, admin watches, prerequisites apply |
| Join Class | Ask a chosen faculty member to join their class |
| Checkpoint | One gate question inside a lesson |
| Knowledge Check | The lesson quiz (≤ 2 mistakes to pass) |
| Code / Configure / Defend it yourself | The hands-on lab of a programming / networking / cybersecurity lesson |
| Mini boss | Module exam |
| Final Boss | Final exam that earns the certificate |
| Strike | Being caught cheating in a final; 2 = banned |
| XP / Level / Rank / Gems / Streak / Frame | Gamification items in Part G |
| Codename | A student's public nickname |
| Astro | The AI tutor (and mascot) |

---

# PART J — Data model (for the ERD)
Main tables and how they connect:
- **users** (role: student / faculty / admin; first_name, last_name, program, year_level, planets, avatar, frame, codename, rank_seen, terms_accepted_at, terms_version, signature_path, tour_seen…)
- **crews** (type: `self_paced` or `class`, planet, course_slug, teacher_id → users, code) — many per course
- **crew_members** (crew ↔ user roster, role, accepted_seen_at, quizzes_seen_at…)
- **course_assignments** (admin puts a faculty member on a course; unique per course + faculty)
- **course_join_requests** (student → class crew; pending / accepted / declined)
- **courses** (faculty-built courses: planet, slug, title, blurb, level, art_path, lab_kind, status draft/published, created_by → users)
- **lesson_progress** (user, course, module, lesson, **crew_id** = track, completed_at, **shelved_at**, saved code)
- Faculty override tables, all keyed by **crew_id**: **course_customizations** (card + outline), **lesson_contents** (page + checkpoints), **lesson_quizzes** (Knowledge Check), **lesson_labs** (coding lab), **lesson_sim_labs** (simulator lab patch)
- **crew_quizzes** (`is_final` = Final Boss, `module` = mini boss; time, pass_percent, published_at) → **crew_quiz_questions** → **crew_quiz_attempts** (score, xp, status, violation, strikes, try_number, wiped_progress)
- **quiz_answers** (Knowledge Check answers + XP), **quiz_reviews**, **lesson_checkpoints**
- **course_modules** → **module_materials** (faculty uploads), **material_chunks** (for Astro's sources)
- Gamification: **daily_rewards**, **xp_spends**, **ai_usages** (each Astro use and its XP cost), **shop_purchases**, **gem_spends**, **gem_exchanges**, **study_activity**
- Chat: **conversations** → **messages**; **studio_outputs** (left from the removed Studio)
- Hidden schedule: **teacher_classes**

---

# PART K — Suggestions for updating the paper

**Diagrams to redraw**
1. **System architecture:** Browser (Blade/Alpine pages, in-browser C/Python runners, NetSim/Relay Console/Citadel Sim) ↔ Laravel app ↔ Supabase PostgreSQL; Laravel ↔ NVIDIA NIM (Nemotron); Laravel → Gmail SMTP.
2. **Context diagram / DFD level 0:** Student, Faculty, Admin, NVIDIA NIM, Email server.
3. **Use-case diagram (3 actors):** Student (register, agree to terms, build avatar, choose track, take lessons, checkpoints, knowledge checks, labs, mini bosses, final, chat with Astro, shop, view certificate); Faculty (register, open class, build course, edit course, accept/decline, write exams, upload materials and signature, monitor, allow retake); Admin (assign/unassign faculty, monitor classes and self-paced, allow retake for self-paced, view analytics, manage admins). Remove faculty chat / slide generator / studio tools.
4. **Flowcharts:** (a) sign-up → terms → welcome → dashboard; (b) **enrollment decision: Self-paced (prereq check) vs Join Class (request → accept/decline)**; (c) lesson flow with gates; (d) mini boss self-paced vs class; (e) Final Boss with strikes, wipe and retake; (f) **faculty Build a course → draft → publish**; (g) admin assignment → faculty Open class.
5. **ERD** from Part J (the new parts: crews.type, lesson_progress.crew_id/shelved_at, override tables with crew_id, courses, mini boss columns, shop/gem/daily tables).

**RRL topics this version now touches**
- Gamification in education (XP, levels, ranks, badges/frames, leaderboards, streaks, virtual goods).
- Self-paced vs instructor-led online courses (e.g. edX self-paced vs instructor-paced runs; master course + section copies like Canvas Blueprint).
- Mastery learning / gated progression (checkpoints, ≤2-mistake rule, module exams before moving on).
- AI tutors / LLMs in education, and limiting AI use (free daily allowance, XP cost).
- Online exam proctoring and academic integrity (full-screen lock, tab-switch detection, strikes).
- Simulation-based labs for networking and cybersecurity; in-browser code execution for programming.
- Teacher authoring and course customization; course prerequisites and sequencing.
- Digital certificates with instructor signatures.
- Data privacy and terms of use for student data (useful to cite the Philippine Data Privacy Act if the paper discusses it).

