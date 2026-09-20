<aside class="programming-sidebar">

    {{-- TABS --}}

    <div class="sidebar-tabs">

        <button class="sidebar-tab active" type="button">

            <svg class="tab-icon" viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="3" width="16" height="18" rx="2.5"/><path d="M8 3v18"/><rect x="11" y="7" width="6" height="4" rx="1"/><path d="M11 15h6"/></svg>

            <span>Course Outline</span>

        </button>

        <button class="sidebar-tab" type="button">

            <svg class="tab-icon" viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 17h6"/></svg>

            <span>Resources</span>

        </button>

    </div>

    {{-- SEARCH --}}

    <div class="sidebar-search">

        <div class="search-wrapper">

            <span class="search-icon">⌕</span>

            <input
                type="text"
                placeholder="Search course content..."
            >

        </div>

    </div>

    {{-- COURSE OUTLINE --}}

    <div class="course-outline">

        {{-- M1 --}}

        <div class="course-module">

            <div class="module-header">

                <div class="module-info">

                    <div class="module-title">
                        M1 — Python Foundations
                    </div>

                    <div class="module-progress" data-module-progress="m1">

                        <div class="progress-track">
                            <div class="progress-fill" style="width: 0%;"></div>
                        </div>

                        <span>0%</span>

                    </div>

                </div>

                <button
                    type="button"
                    class="module-toggle"
                    onclick="toggleModule('m1', this)"
                    aria-expanded="false"
                >
                    <span>⌄</span>
                </button>

            </div>

            {{-- LESSONS --}}

            <div class="module-lessons" id="m1">

                <a href="/student/planet/programming/m1/lesson01" class="lesson-item" data-module="m1" data-lesson="lesson01" data-order="1">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">1.1</span>
                        <span>First Signal</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m1/lesson02" class="lesson-item" data-module="m1" data-lesson="lesson02" data-order="2">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">1.2</span>
                        <span>Variables & Memory</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m1/lesson03" class="lesson-item" data-module="m1" data-lesson="lesson03" data-order="3">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">1.3</span>
                        <span>Data Types</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m1/lesson04" class="lesson-item" data-module="m1" data-lesson="lesson04" data-order="4">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">1.4</span>
                        <span>Expressions & Operators</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m1/lesson05" class="lesson-item" data-module="m1" data-lesson="lesson05" data-order="5">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">1.5</span>
                        <span>Talking to the Program</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m1/lesson06" class="lesson-item" data-module="m1" data-lesson="lesson06" data-order="6">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">1.6</span>
                        <span>Reading Error Messages</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

            </div>

        </div>


        {{-- M2 --}}

        <div class="course-module">

            <div class="module-header">

                <div class="module-info">

                    <div class="module-title">
                        M2 — Conditions & Loops
                    </div>

                    <div class="module-progress" data-module-progress="m2">

                        <div class="progress-track">
                            <div class="progress-fill" style="width: 0%;"></div>
                        </div>

                        <span>0%</span>

                    </div>

                </div>

                <button
                    type="button"
                    class="module-toggle"
                    onclick="toggleModule('m2', this)"
                    aria-expanded="false"
                >
                    <span>⌄</span>
                </button>

            </div>

            <div class="module-lessons" id="m2">

                <a href="/student/planet/programming/m2/lesson01" class="lesson-item" data-module="m2" data-lesson="lesson01" data-order="1">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">2.1</span>
                        <span>Decision Points</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m2/lesson02" class="lesson-item" data-module="m2" data-lesson="lesson02" data-order="2">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">2.2</span>
                        <span>Branching Paths</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m2/lesson03" class="lesson-item" data-module="m2" data-lesson="lesson03" data-order="3">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">2.3</span>
                        <span>Combining Conditions</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m2/lesson04" class="lesson-item" data-module="m2" data-lesson="lesson04" data-order="4">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">2.4</span>
                        <span>Repeating Signals</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m2/lesson05" class="lesson-item" data-module="m2" data-lesson="lesson05" data-order="5">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">2.5</span>
                        <span>Repeating With for</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m2/lesson06" class="lesson-item" data-module="m2" data-lesson="lesson06" data-order="6">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">2.6</span>
                        <span>Breaking the Loop</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

            </div>

        </div>


        {{-- M3 --}}

        <div class="course-module">

            <div class="module-header">

                <div class="module-info">

                    <div class="module-title">
                        M3 — Functions & Error Handling
                    </div>

                    <div class="module-progress" data-module-progress="m3">

                        <div class="progress-track">
                            <div class="progress-fill" style="width: 0%;"></div>
                        </div>

                        <span>0%</span>

                    </div>

                </div>

                <button
                    type="button"
                    class="module-toggle"
                    onclick="toggleModule('m3', this)"
                    aria-expanded="false"
                >
                    <span>⌄</span>
                </button>

            </div>

            <div class="module-lessons" id="m3">

                <a href="/student/planet/programming/m3/lesson01" class="lesson-item" data-module="m3" data-lesson="lesson01" data-order="1">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">3.1</span>
                        <span>Reusable Routines</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m3/lesson02" class="lesson-item" data-module="m3" data-lesson="lesson02" data-order="2">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">3.2</span>
                        <span>Passing Information</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m3/lesson03" class="lesson-item" data-module="m3" data-lesson="lesson03" data-order="3">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">3.3</span>
                        <span>Returning Results</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m3/lesson04" class="lesson-item" data-module="m3" data-lesson="lesson04" data-order="4">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">3.4</span>
                        <span>Anticipating Failure</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m3/lesson05" class="lesson-item" data-module="m3" data-lesson="lesson05" data-order="5">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">3.5</span>
                        <span>Sanity Checks</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

            </div>

        </div>


        {{-- M4 --}}

        <div class="course-module">

            <div class="module-header">

                <div class="module-info">

                    <div class="module-title">
                        M4 — Working With Data & Files
                    </div>

                    <div class="module-progress" data-module-progress="m4">

                        <div class="progress-track">
                            <div class="progress-fill" style="width: 0%;"></div>
                        </div>

                        <span>0%</span>

                    </div>

                </div>

                <button
                    type="button"
                    class="module-toggle"
                    onclick="toggleModule('m4', this)"
                    aria-expanded="false"
                >
                    <span>⌄</span>
                </button>

            </div>

            <div class="module-lessons" id="m4">

                <a href="/student/planet/programming/m4/lesson01" class="lesson-item" data-module="m4" data-lesson="lesson01" data-order="1">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">4.1</span>
                        <span>Collections</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m4/lesson02" class="lesson-item" data-module="m4" data-lesson="lesson02" data-order="2">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">4.2</span>
                        <span>List Operations</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m4/lesson03" class="lesson-item" data-module="m4" data-lesson="lesson03" data-order="3">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">4.3</span>
                        <span>Labeled Data</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m4/lesson04" class="lesson-item" data-module="m4" data-lesson="lesson04" data-order="4">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">4.4</span>
                        <span>Nested Structures</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m4/lesson05" class="lesson-item" data-module="m4" data-lesson="lesson05" data-order="5">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">4.5</span>
                        <span>Text Manipulation</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m4/lesson06" class="lesson-item" data-module="m4" data-lesson="lesson06" data-order="6">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">4.6</span>
                        <span>Reading Files</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m4/lesson07" class="lesson-item" data-module="m4" data-lesson="lesson07" data-order="7">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">4.7</span>
                        <span>Writing Files</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m4/lesson08" class="lesson-item" data-module="m4" data-lesson="lesson08" data-order="8">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">4.8</span>
                        <span>Structured Data Formats</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

            </div>

        </div>


        {{-- M5 --}}

        <div class="course-module">

            <div class="module-header">

                <div class="module-info">

                    <div class="module-title">
                        M5 — Python Automation
                    </div>

                    <div class="module-progress" data-module-progress="m5">

                        <div class="progress-track">
                            <div class="progress-fill" style="width: 0%;"></div>
                        </div>

                        <span>0%</span>

                    </div>

                </div>

                <button
                    type="button"
                    class="module-toggle"
                    onclick="toggleModule('m5', this)"
                    aria-expanded="false"
                >
                    <span>⌄</span>
                </button>

            </div>

            <div class="module-lessons" id="m5">

                <a href="/student/planet/programming/m5/lesson01" class="lesson-item" data-module="m5" data-lesson="lesson01" data-order="1">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">5.1</span>
                        <span>Pattern Matching</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m5/lesson02" class="lesson-item" data-module="m5" data-lesson="lesson02" data-order="2">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">5.2</span>
                        <span>Practical Regex</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m5/lesson03" class="lesson-item" data-module="m5" data-lesson="lesson03" data-order="3">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">5.3</span>
                        <span>File System Basics</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m5/lesson04" class="lesson-item" data-module="m5" data-lesson="lesson04" data-order="4">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">5.4</span>
                        <span>Building a CLI Tool</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m5/lesson05" class="lesson-item" data-module="m5" data-lesson="lesson05" data-order="5">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">5.5</span>
                        <span>Time & Scheduling Concepts</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

            </div>

        </div>


        {{-- M6 --}}

        <div class="course-module">

            <div class="module-header">

                <div class="module-info">

                    <div class="module-title">
                        M6 — Real-World Projects
                    </div>

                    <div class="module-progress" data-module-progress="m6">

                        <div class="progress-track">
                            <div class="progress-fill" style="width: 0%;"></div>
                        </div>

                        <span>0%</span>

                    </div>

                </div>

                <button
                    type="button"
                    class="module-toggle"
                    onclick="toggleModule('m6', this)"
                    aria-expanded="false"
                >
                    <span>⌄</span>
                </button>

            </div>

            <div class="module-lessons" id="m6">

                <a href="/student/planet/programming/m6/lesson01" class="lesson-item" data-module="m6" data-lesson="lesson01" data-order="1">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">6.1</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m6/lesson02" class="lesson-item" data-module="m6" data-lesson="lesson02" data-order="2">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">6.2</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m6/lesson03" class="lesson-item" data-module="m6" data-lesson="lesson03" data-order="3">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">6.3</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

                <a href="/student/planet/programming/m6/lesson04" class="lesson-item" data-module="m6" data-lesson="lesson04" data-order="4">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">6.4</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

            </div>

        </div>


        {{-- M7 --}}

        <div class="course-module">

            <div class="module-header">

                <div class="module-info">

                    <div class="module-title">
                        M7 — Final Automation Project
                    </div>

                    <div class="module-progress" data-module-progress="m7">

                        <div class="progress-track">
                            <div class="progress-fill" style="width: 0%;"></div>
                        </div>

                        <span>0%</span>

                    </div>

                </div>

                <button
                    type="button"
                    class="module-toggle"
                    onclick="toggleModule('m7', this)"
                    aria-expanded="false"
                >
                    <span>⌄</span>
                </button>

            </div>

            <div class="module-lessons" id="m7">

                <a href="/student/planet/programming/m7/lesson01" class="lesson-item" data-module="m7" data-lesson="lesson01" data-order="1">
                    <div class="lesson-connector">
                        <span class="lesson-dot"></span>
                    </div>
                    <div class="lesson-name">
                        <span class="lesson-number">7.1</span>
                        <span>Proposal & Design</span>
                        <span class="lesson-lock">🔒</span>
                    </div>
                </a>

            </div>

        </div>

    </div>

</aside>


<style>

.programming-sidebar {
    font-family: 'Inter', system-ui, sans-serif;
    width: 30vw;
    min-width: 380px;
    max-width: 520px;
    height: 100vh;
    background: #f9f9f9;
    border-right: 1px solid rgba(0,0,0,.09);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}


/* =========================
   TABS
========================= */

.sidebar-tabs {
    display: flex;
    flex-shrink: 0;
    border-bottom: 1px solid rgba(0,0,0,.09);
}

.sidebar-tab {
    font-family: 'Space Grotesk', sans-serif;
    flex: 1;
    height: 90px;
    border: none;
    background: #ffffff;
    font-size: 20px;
    font-weight: 600;
    color: #0d0d0d;
    cursor: pointer;
    border-bottom: 4px solid transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    transition: all 0.2s ease;
}

.sidebar-tab:hover {
    background: #f7f7f7;
}

.sidebar-tab.active .tab-icon { color: #2f7de1; }

.sidebar-tab.active {
    color: #2f7de1;
    border-bottom-color: #2f7de1;
}

.tab-icon {
    flex-shrink: 0;
    color: #676767;
    font-size: 24px;
}


/* =========================
   SEARCH
========================= */

.sidebar-search {
    padding: 24px 28px;
    flex-shrink: 0;
}

.search-wrapper {
    position: relative;
}

.search-wrapper input {
    width: 100%;
    height: 56px;
    box-sizing: border-box;
    padding: 0 18px 0 48px;
    border: 2px solid rgba(0,0,0,.09);
    border-radius: 12px;
    font-size: 18px;
    outline: none;
    transition: border-color 0.2s ease;
}

.search-wrapper input:focus {
    border-color: #2f7de1;
}

.search-icon {
    position: absolute;
    left: 17px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 28px;
    color: #888888;
    pointer-events: none;
}


/* =========================
   COURSE OUTLINE
========================= */

.course-outline {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
}


/* =========================
   MODULE
========================= */

.course-module {
    border-top: 1px solid rgba(0,0,0,.09);
}


/* =========================
   MODULE HEADER
========================= */

.module-header {
    position: relative;
    min-height: 145px;
    padding: 22px 78px 22px 28px;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    background: #ffffff;
}


/* =========================
   MODULE INFO
========================= */

.module-info {
    width: 100%;
}

.module-title {
    font-family: 'Space Grotesk', sans-serif;
    letter-spacing: -0.02em;
    font-size: 22px;
    line-height: 1.35;
    font-weight: 700;
    color: #0d0d0d;
    margin-bottom: 18px;
}

.module-progress {
    display: flex;
    align-items: center;
    gap: 12px;
}

.progress-track {
    flex: 1;
    height: 8px;
    background: #f3f3f3;
    border-radius: 20px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #2f7de1;
    border-radius: 20px;
    transition: width 0.3s ease;
}

.module-progress span {
    min-width: 40px;
    font-size: 15px;
    color: #555555;
}


/* =========================
   DROPDOWN BUTTON
========================= */

.module-toggle {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    width: 48px;
    height: 48px;
    border: none;
    border-radius: 8px;
    background: #f3f3f3;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition:
        background 0.2s ease,
        color 0.2s ease;
}

.module-toggle span {
    font-size: 26px;
    transition: transform 0.25s ease;
}

.module-toggle:hover {
    background: #2f7de1;
    color: #ffffff;
}

.module-toggle.open {
    background: #2f7de1;
    color: #ffffff;
}

.module-toggle.open span {
    transform: rotate(180deg);
}


/* =========================
   LESSON CONTAINER
========================= */

.module-lessons {
    display: none;
    background: #f6f9fe;
    padding: 8px 18px 16px 28px;
}

.module-lessons.open {
    display: block;
}


/* =========================
   LESSON
========================= */

.lesson-item {
    display: flex;
    min-height: 64px;
    position: relative;
    cursor: pointer;
    border-radius: 8px;
    transition: background 0.2s ease;

    /* Preserve original appearance after converting div to a link */
    text-decoration: none;
    color: inherit;
}

.lesson-item:hover {
    background: #eaf2fd;
}

.lesson-item.active {
    background: #eaf2fd;
}

.lesson-item.active .lesson-dot {
    background: #2f7de1;
    border-color: #2f7de1;
}

/* Completed lessons: solid green filled dot */
.lesson-item.completed .lesson-dot {
    background: #2f7de1;
    border-color: #2f7de1;
}

/* Locked lessons: dim + not-allowed cursor */
.lesson-item.locked {
    opacity: 0.55;
    cursor: not-allowed;
    pointer-events: none;  /* ← added: belt-and-suspenders block */
}

.lesson-item.locked:hover {
    background: transparent;
}

.lesson-lock {
    display: none;
    margin-left: auto;
    font-size: 15px;
    flex-shrink: 0;
}

.lesson-item.locked .lesson-lock {
    display: inline-block;
}


/* =========================
   CONNECTOR
========================= */

.lesson-connector {
    width: 48px;
    position: relative;
    display: flex;
    justify-content: center;
    flex-shrink: 0;
}

.lesson-connector::after {
    content: "";
    position: absolute;
    top: 30px;
    bottom: -30px;
    left: 23px;
    border-left: 2px dashed #b7b7b7;
}

.lesson-item:last-child .lesson-connector::after {
    display: none;
}


/* =========================
   LESSON DOT
========================= */

.lesson-dot {
    width: 28px;
    height: 28px;
    margin-top: 17px;
    border-radius: 50%;
    background: #ffffff;
    border: 2px solid #b9dcb8;
    position: relative;
    z-index: 2;
    transition: background 0.2s ease, border-color 0.2s ease;
}


/* =========================
   LESSON TEXT
========================= */

.lesson-name {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 14px 8px;
    font-size: 18px;
    line-height: 1.35;
    color: #0d0d0d;
}

.lesson-number {
    font-weight: 600;
    color: #2f7de1;
}


/* =========================
   SCROLLBAR
========================= */

.course-outline::-webkit-scrollbar {
    width: 8px;
}

.course-outline::-webkit-scrollbar-track {
    background: #f4f4f4;
}

.course-outline::-webkit-scrollbar-thumb {
    background: #cccccc;
    border-radius: 10px;
}

.course-outline::-webkit-scrollbar-thumb:hover {
    background: #aaaaaa;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 900px) {

    .programming-sidebar {
        width: 38vw;
        min-width: 320px;
    }

    .module-title {
        font-size: 20px;
    }

    .lesson-name {
        font-size: 16px;
    }

}

</style>


<script>

function toggleModule(moduleId, button) {

    const lessons = document.getElementById(moduleId);
    const isOpen = lessons.classList.contains('open');

    if (isOpen) {
        lessons.classList.remove('open');
        button.classList.remove('open');
        button.setAttribute('aria-expanded', 'false');
    } else {
        lessons.classList.add('open');
        button.classList.add('open');
        button.setAttribute('aria-expanded', 'true');
    }

}

/*
|--------------------------------------------------------------------------
| LESSON PROGRESS SYSTEM
|--------------------------------------------------------------------------
|
| Reads the student's completed lessons from the database (rendered by the
| server into SERVER_COMPLETED below — it follows the account across browsers
| and survives clearing localStorage), then derives:
|   - which lessons are unlocked (a lesson is unlocked when the lesson right
|     before it, course-wide, is completed; module 1 lesson 1 is always unlocked)
|   - module completion percentages
|
| This is display only. The real lock is enforced on the server by the
| EnsureLessonUnlocked middleware, so editing the DOM/classes here cannot
| open a locked lesson.
|
| Exposed globally as window.TechLab.markLessonComplete(module, lesson) so
| the Python editor's "Submit"/"Next" flow can call it directly without the
| editor needing to know anything about the sidebar's storage format.
|
| NEW ─ after marking complete, the system also:
|   1. auto-navigates to the next lesson via TechLab.loadLesson (or href fallback)
|   2. updates the active-lesson highlight in the sidebar
*/

(function () {
    // Completed lessons as "m1/lesson01" keys — the database is the source of truth.
    const SERVER_COMPLETED = @json(auth()->check() ? \App\Services\CourseProgressService::completedKeys(auth()->user()) : []);

    // ─── Build flat ordered list of all lessons from the DOM ───────────────
    function buildLessonOrder() {
        return Array.from(document.querySelectorAll('.lesson-item')).map(function (el) {
            return { module: el.dataset.module, lesson: el.dataset.lesson, el: el };
        });
    }

    function keyFor(moduleId, lessonId) {
        return moduleId + '/' + lessonId;
    }

    // ─── State ──────────────────────────────────────────────────────────────
    let completed = SERVER_COMPLETED.slice();   // ["m1/lesson01", …]
    const order   = buildLessonOrder();

    function isCompleted(moduleId, lessonId) {
        return completed.indexOf(keyFor(moduleId, lessonId)) !== -1;
    }

    // A lesson is unlocked when it is the very first one, or the lesson
    // immediately before it (course-wide) has been completed.
    function isUnlocked(index) {
        if (index === 0) return true;
        const prev = order[index - 1];
        return isCompleted(prev.module, prev.lesson);
    }

    // ─── DOM render ─────────────────────────────────────────────────────────
    function renderLockState() {
        order.forEach(function (item, index) {
            const unlocked = isUnlocked(index);
            const done     = isCompleted(item.module, item.lesson);

            item.el.classList.toggle('locked',    !unlocked);
            item.el.classList.toggle('completed',  done);

            if (!unlocked) {
                item.el.setAttribute('aria-disabled', 'true');
            } else {
                item.el.removeAttribute('aria-disabled');
            }
        });
    }

    function renderModulePercentages() {
        const modules = {};

        order.forEach(function (item) {
            if (!modules[item.module]) modules[item.module] = { total: 0, done: 0 };
            modules[item.module].total++;
            if (isCompleted(item.module, item.lesson)) modules[item.module].done++;
        });

        Object.keys(modules).forEach(function (moduleId) {
            const stats = modules[moduleId];
            const pct   = stats.total > 0 ? Math.round((stats.done / stats.total) * 100) : 0;

            const progressEl = document.querySelector('[data-module-progress="' + moduleId + '"]');
            if (!progressEl) return;

            const fill  = progressEl.querySelector('.progress-fill');
            const label = progressEl.querySelector('span');

            if (fill)  fill.style.width   = pct + '%';
            if (label) label.textContent   = pct;
        });
    }

    // Mark the sidebar item that matches the currently-open lesson as active.
    function renderActiveLesson(moduleId, lessonId) {
        order.forEach(function (item) {
            const isCurrent = item.module === moduleId && item.lesson === lessonId;
            item.el.classList.toggle('active', isCurrent);
        });
    }

    function refresh() {
        renderLockState();
        renderModulePercentages();
    }

    // ─── Next-lesson helper ─────────────────────────────────────────────────
    // Returns the {module, lesson, el} entry that comes immediately after the
    // supplied lesson, or null if it is the last lesson in the course.
    function getNextLesson(moduleId, lessonId) {
        const index = order.findIndex(function (item) {
            return item.module === moduleId && item.lesson === lessonId;
        });
        if (index === -1 || index === order.length - 1) return null;
        return order[index + 1];
    }

    // ─── Public: markLessonComplete ─────────────────────────────────────────
    //
    // Marks a lesson done in the sidebar's in-memory state.
    //
    //   window.TechLab.markLessonComplete('m1', 'lesson01');
    //
    // What it does:
    //   1. Records the completion in the sidebar's in-memory state.
    //   2. Re-renders lock states + progress bars immediately.
    //   3. Navigates to the next lesson automatically.
    //
    function markLessonComplete(moduleId, lessonId) {
        // 1 ─ Reflect it locally (the server has already recorded the completion)
        const key = keyFor(moduleId, lessonId);
        if (completed.indexOf(key) === -1) {
            completed.push(key);
        }

        // 2 ─ Re-render sidebar
        refresh();

        // 3 ─ Navigate to the next lesson
        const next = getNextLesson(moduleId, lessonId);
        if (!next) return; // last lesson — nothing to navigate to

        if (window.TechLab && typeof window.TechLab.loadLesson === 'function') {
            // The main panel (programming.blade.php) owns lesson loading.
            window.TechLab.loadLesson(next.module, next.lesson);

            // Auto-expand the next lesson's module in the sidebar if it is
            // in a different module than the one just completed.
            if (next.module !== moduleId) {
                const nextModuleLessons = document.getElementById(next.module);
                const nextModuleToggle  = nextModuleLessons
                    ? nextModuleLessons.previousElementSibling
                        && nextModuleLessons.closest('.course-module')
                           .querySelector('.module-toggle')
                    : null;

                if (nextModuleLessons && !nextModuleLessons.classList.contains('open')) {
                    nextModuleLessons.classList.add('open');
                    if (nextModuleToggle) {
                        nextModuleToggle.classList.add('open');
                        nextModuleToggle.setAttribute('aria-expanded', 'true');
                    }
                }
            }
        } else {
            // Fallback: full page navigation using the href already on the element.
            window.location.href = next.el.getAttribute('href');
        }
    }

    // ─── Public: getLessonStatus ────────────────────────────────────────────
    function getLessonStatus(moduleId, lessonId) {
        const index = order.findIndex(function (item) {
            return item.module === moduleId && item.lesson === lessonId;
        });
        if (index === -1) return { unlocked: false, completed: false };
        return { unlocked: isUnlocked(index), completed: isCompleted(moduleId, lessonId) };
    }

    // ─── Block locked lesson clicks (capture phase — runs first) ────────────
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.lesson-item');
        if (!link) return;
        if (link.classList.contains('locked')) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    }, true);

    // ─── Export public API ───────────────────────────────────────────────────
    window.TechLab = window.TechLab || {};
    window.TechLab.markLessonComplete  = markLessonComplete;
    window.TechLab.getLessonStatus     = getLessonStatus;
    window.TechLab.refreshProgress     = refresh;
    window.TechLab.setActiveLesson     = renderActiveLesson;  // ← new, call from programming.blade.php

    // ─── Initial render ──────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', refresh);
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        refresh();
    }

})();

/*
|--------------------------------------------------------------------------
| LESSON CLICK → TELL THE MAIN PANEL WHICH LESSON TO LOAD
|--------------------------------------------------------------------------
|
| Locked lessons are already blocked by the capture-phase listener above.
| This listener handles unlocked clicks and delegates to the main panel.
*/

document.addEventListener('click', function (e) {
    const link = e.target.closest('.lesson-item');
    if (!link) return;
    if (link.classList.contains('locked')) return;

    e.preventDefault();

    const moduleId = link.dataset.module;
    const lessonId = link.dataset.lesson;

    // Update the sidebar's own active highlight immediately.
    document.querySelectorAll('.lesson-item').forEach(function (el) {
        el.classList.remove('active');
    });
    link.classList.add('active');

    if (window.TechLab && typeof window.TechLab.loadLesson === 'function') {
        window.TechLab.loadLesson(moduleId, lessonId);
    } else {
        window.location.href = link.getAttribute('href');
    }
});

</script>