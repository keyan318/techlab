{{-- M4 · Lesson 4.3: Patching the Grid — content fragment.
Syllabus: CC102 Unit 4 — Arrays (array access and manipulation). --}}

<div class="lesson-fragment" data-module="m4" data-lesson="03">

    <h1 class="lesson-heading">Lesson 4.3: Patching the Grid</h1>

    <p class="lesson-objective">
        <strong>Learning Objective:</strong>
        By the end of this lesson, you can combine a traversal loop with an accumulator variable to compute a
        single result — like a total — from every element of an array.
    </p>

    <h2 class="section-heading">Simple Explanation</h2>

    <p class="body-text">
        A traversal loop doesn't just have to print — it can build up a result as it goes. An
        <strong>accumulator</strong> is a variable declared <em>before</em> the loop, starting at a known value,
        that the loop updates on every pass:
    </p>

    <pre class="code-block"><code>int total = 0;
for (int i = 0; i &lt; 5; i++) {
    total = total + emitters[i];
}</code></pre>

    <p class="body-text">
        <code>total</code> starts at 0. Each time through the loop, it's reassigned to itself plus the current
        element — so after all five passes, it holds the sum of every element in the array. The accumulator
        must be declared <em>outside</em> the loop, or it would reset to 0 on every single pass instead of
        building up.
    </p>

    <div class="checkpoint-block" data-cp="1">
        <p class="quiz-prompt">After the loop in the explanation finishes, what does <code>total</code> hold?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The last element only</li>
            <li class="quiz-option">B. How many elements there are</li>
            <li class="quiz-option">C. 0, its starting value</li>
            <li class="quiz-option quiz-correct">D. The sum of every element ✓</li>
        </ul>
        <p class="quiz-explanation"><em>Each pass adds the current element, so after five passes <code>total</code> is the sum of all five.</em></p>
    </div>

    <h2 class="section-heading">Picture It</h2>

    <p class="body-text">
        At a supermarket checkout, the screen shows <strong>0</strong> before the first item. Every beep adds that item's price to the total. After the last item, the screen shows the whole bill.
    </p>

    <p class="body-text">
        <code>total</code> is that screen. It's set to 0 <strong>before</strong> the loop starts, and every pass through the loop does <code>total = total + emitters[i];</code>, one beep per locker. A variable that builds up a result like this is called an <strong>accumulator</strong>.
    </p>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 340" role="img" aria-label="Items priced 80, 45, 90, 30 and 100 roll to a scanner. The total screen starts at 0, then shows 80, 125, 215, 245, and finally 345. int total = 0 goes before the loop so the screen starts at zero. If you reset it inside the loop you would only pay for the last item.">
            <text x="210" y="44" font-size="13" fill="#14306b" text-anchor="middle">each beep:  <tspan font-family="ui-monospace, Menlo, Consolas, monospace" font-weight="800">total = total + emitters[i];</tspan></text><rect x="28" y="104" width="58" height="56" rx="8" fill="#fff0d9" stroke="#f08c1a" stroke-width="2"/><rect x="42" y="94" width="30" height="14" rx="3" fill="#fff" stroke="#f08c1a" stroke-width="1.5"/><text x="57" y="140" font-size="17" fill="#8a4b00" font-weight="800" text-anchor="middle">80</text><rect x="98" y="104" width="58" height="56" rx="8" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/><rect x="112" y="94" width="30" height="14" rx="3" fill="#fff" stroke="#7c5cff" stroke-width="1.5"/><text x="127" y="140" font-size="17" fill="#4a35b8" font-weight="800" text-anchor="middle">45</text><rect x="168" y="104" width="58" height="56" rx="8" fill="#e6f8f9" stroke="#0fa3b1" stroke-width="2"/><rect x="182" y="94" width="30" height="14" rx="3" fill="#fff" stroke="#0fa3b1" stroke-width="1.5"/><text x="197" y="140" font-size="17" fill="#0b6670" font-weight="800" text-anchor="middle">90</text><rect x="238" y="104" width="58" height="56" rx="8" fill="#e9f7ef" stroke="#2fa56b" stroke-width="2"/><rect x="252" y="94" width="30" height="14" rx="3" fill="#fff" stroke="#2fa56b" stroke-width="1.5"/><text x="267" y="140" font-size="17" fill="#1d6b43" font-weight="800" text-anchor="middle">30</text><rect x="308" y="104" width="58" height="56" rx="8" fill="#e8f0ff" stroke="#2f6fe0" stroke-width="2"/><rect x="322" y="94" width="30" height="14" rx="3" fill="#fff" stroke="#2f6fe0" stroke-width="1.5"/><text x="337" y="140" font-size="17" fill="#14306b" font-weight="800" text-anchor="middle">100</text><rect x="20" y="162" width="380" height="20" rx="10" fill="#5b6b86"/><circle cx="38" cy="172" r="5" fill="#cfd7e6"/><circle cx="74" cy="172" r="5" fill="#cfd7e6"/><circle cx="110" cy="172" r="5" fill="#cfd7e6"/><circle cx="146" cy="172" r="5" fill="#cfd7e6"/><circle cx="182" cy="172" r="5" fill="#cfd7e6"/><circle cx="218" cy="172" r="5" fill="#cfd7e6"/><circle cx="254" cy="172" r="5" fill="#cfd7e6"/><circle cx="290" cy="172" r="5" fill="#cfd7e6"/><circle cx="326" cy="172" r="5" fill="#cfd7e6"/><circle cx="362" cy="172" r="5" fill="#cfd7e6"/><line x1="40" y1="200" x2="109.0" y2="200.0" stroke="#9aa5b8" stroke-width="2.5"/><polygon points="120,200 109.0,194.0 109.0,206.0" fill="#9aa5b8"/><text x="130" y="205" font-size="12" fill="#5b6b86" font-style="italic">items roll to the scanner</text><rect x="400" y="96" width="46" height="86" rx="8" fill="#1b2440"/><line x1="408" y1="139" x2="438" y2="139" stroke="#ff5a5a" stroke-width="3"/><rect x="466" y="30" width="154" height="200" rx="12" fill="#1b2440"/><text x="543" y="56" font-size="12" fill="#9aa5b8" font-weight="800" text-anchor="middle" letter-spacing="2">TOTAL</text><text x="484" y="84" font-size="13" fill="#9aa5b8" font-family="ui-monospace, Menlo, Consolas, monospace">start</text><text x="604" y="84" font-size="14" fill="#e8f0ff" font-weight="600" text-anchor="end" font-family="ui-monospace, Menlo, Consolas, monospace">0</text><text x="484" y="109" font-size="13" fill="#9aa5b8" font-family="ui-monospace, Menlo, Consolas, monospace">+80</text><text x="604" y="109" font-size="14" fill="#e8f0ff" font-weight="600" text-anchor="end" font-family="ui-monospace, Menlo, Consolas, monospace">80</text><text x="484" y="134" font-size="13" fill="#9aa5b8" font-family="ui-monospace, Menlo, Consolas, monospace">+45</text><text x="604" y="134" font-size="14" fill="#e8f0ff" font-weight="600" text-anchor="end" font-family="ui-monospace, Menlo, Consolas, monospace">125</text><text x="484" y="159" font-size="13" fill="#9aa5b8" font-family="ui-monospace, Menlo, Consolas, monospace">+90</text><text x="604" y="159" font-size="14" fill="#e8f0ff" font-weight="600" text-anchor="end" font-family="ui-monospace, Menlo, Consolas, monospace">215</text><text x="484" y="184" font-size="13" fill="#9aa5b8" font-family="ui-monospace, Menlo, Consolas, monospace">+30</text><text x="604" y="184" font-size="14" fill="#e8f0ff" font-weight="600" text-anchor="end" font-family="ui-monospace, Menlo, Consolas, monospace">245</text><text x="484" y="209" font-size="13" fill="#9aa5b8" font-family="ui-monospace, Menlo, Consolas, monospace">+100</text><text x="604" y="209" font-size="16" fill="#86e0b0" font-weight="800" text-anchor="end" font-family="ui-monospace, Menlo, Consolas, monospace">345</text><rect x="20" y="246" width="600" height="36" rx="18.0" fill="#e9f7ef" stroke="#2fa56b" stroke-width="1.8"/><text x="320.0" y="268.68" font-size="13" fill="#1d6b43" text-anchor="middle"><tspan font-family="ui-monospace, Menlo, Consolas, monospace" font-weight="800">int total = 0;</tspan> before the loop: the screen starts at zero</text><rect x="20" y="292" width="600" height="36" rx="18.0" fill="#fdeaea" stroke="#b3261e" stroke-width="1.8"/><text x="320.0" y="314.68" font-size="13" fill="#7a1a14" text-anchor="middle">Reset it inside the loop? You'd only pay for the last item.</text>
        </svg>
    </figure>

    <p class="body-text">
        If the cashier reset the screen to 0 for every item, you'd only pay for the last thing in your cart. That is exactly what happens if you write <code>int total = 0;</code> <em>inside</em> the loop.
    </p>

    <div class="checkpoint-block" data-cp="2">
        <p class="quiz-prompt">Why must <code>int total = 0;</code> be written before the loop, not inside it?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Inside, it resets to 0 on every item ✓</li>
            <li class="quiz-option">B. Inside, the loop would run forever</li>
            <li class="quiz-option">C. Before, it makes the loop run faster</li>
            <li class="quiz-option">D. It doesn't matter where you write it</li>
        </ul>
        <p class="quiz-explanation"><em>Inside the loop, <code>total</code> would go back to 0 on every pass and only the last item would count. Outside, it keeps growing.</em></p>
    </div>

    <h2 class="section-heading">Field Diagram</h2>

    <figure class="net-diagram">
        <svg viewBox="0 0 640 190" role="img" aria-label="total starts at 0 and accumulates each array element as the loop runs">
            <rect x="20" y="20" width="130" height="50" rx="10" fill="#fff4e0" stroke="#f08c1a" stroke-width="2"/>
            <text x="85" y="50" text-anchor="middle" font-size="14" font-weight="700" fill="#8a4b00">total = 0</text>

            @php $vals = [80, 45, 90, 30, 100]; $running = 0; @endphp
            @foreach ($vals as $i => $v)
                @php $running += $v; @endphp
                <rect x="{{ 20 + $i * 115 }}" y="100" width="100" height="50" rx="10" fill="#f3f0ff" stroke="#7c5cff" stroke-width="2"/>
                <text x="{{ 70 + $i * 115 }}" y="122" text-anchor="middle" font-size="13" font-weight="700" fill="#4a35b8">+{{ $v }}</text>
                <text x="{{ 70 + $i * 115 }}" y="140" text-anchor="middle" font-size="11" fill="#64748b">total={{ $running }}</text>
            @endforeach

            <line x1="85" y1="70" x2="85" y2="96" stroke="#7c5cff" stroke-width="2.5"/>

            <text x="320" y="180" text-anchor="middle" font-size="13" fill="#475569">After the loop, total holds 345 — the sum of all five emitters.</text>
        </svg>
    </figure>

    <h2 class="section-heading">Code Example</h2>

    <pre class="code-block"><code>#include &lt;stdio.h&gt;

int main() {
    int emitters[5] = {80, 45, 90, 30, 100};
    int total = 0;

    for (int i = 0; i &lt; 5; i++) {
        total = total + emitters[i];
    }
    printf("Total output: %d\n", total);
    return 0;
}</code></pre>

    <div class="checkpoint-block" data-cp="3">
        <p class="quiz-prompt">What does the code example print?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Total output: 345 ✓</li>
            <li class="quiz-option">B. Total output: 100</li>
            <li class="quiz-option">C. Total output: 5</li>
            <li class="quiz-option">D. Total output: 0</li>
        </ul>
        <p class="quiz-explanation"><em>80 + 45 + 90 + 30 + 100 = 345.</em></p>
    </div>

    <h2 class="section-heading">Interactive Coding Exercise</h2>

    <p class="body-text">
        Declare <code>int costs[4] = {12, 45, 78, 20};</code> and an accumulator <code>total</code> starting at
        0. Loop through <code>costs</code>, adding each value into <code>total</code>, then print exactly:
    </p>

    <pre class="code-block"><code>Repair total: 155</code></pre>

    <div class="cta-wrap" style="margin-top:2rem;">
        <form method="POST" action="{{ route('student.planet.editor.launch', ['slug' => 'computer-programming-1']) }}">
            @csrf
            <input type="hidden" name="title"        value="Patching the Grid">
            <input type="hidden" name="filename"     value="repair_total.c">
            <input type="hidden" name="difficulty"   value="Medium">
            <input type="hidden" name="xp"           value="{{ \App\Services\StudentDashboardService::XP_PER_LESSON }}">
            <input type="hidden" name="starter_code" value="#include &lt;stdio.h&gt;

int main() {
    int costs[4] = {12, 45, 78, 20};
    int total = 0;

    // loop through costs, adding each value into total

    // print &quot;Repair total: &quot; + total

    return 0;
}
">
            <input type="hidden" name="instructions" value="costs holds {12, 45, 78, 20}. total already starts at 0.

Write a for loop that adds every element of costs into total (total = total + costs[i];), then print:
Repair total: 155">
            <input type="hidden" name="hint_title"   value="Astro's Hint">
            <input type="hidden" name="hint_body"    value="Inside the loop: total = total + costs[i];. total must be declared before (outside) the loop, or it would reset to 0 every single pass instead of building up.">
            <input type="hidden" name="hint_code"    value="#include &lt;stdio.h&gt;

int main() {
    int costs[4] = {12, 45, 78, 20};
    int total = 0;

    for (int i = 0; i &lt; 4; i++) {
        total = total + costs[i];
    }
    printf(&quot;Repair total: %d\n&quot;, total);
    return 0;
}">
            <input type="hidden" name="return_to"    value="{{ route('student.planet.module.lesson', ['slug' => 'computer-programming-1', 'module' => 'm4', 'lesson' => 'lesson03']) }}">

            <button type="submit" class="code-btn">
                @include('components.c-logo')
                <span>Code it yourself</span>
                <span class="code-btn-arrow" aria-hidden="true">→</span>
            </button>
        </form>
    </div>

    <h2 class="section-heading">Quiz</h2>

    <div class="quiz-block" data-q="1">
        <p class="quiz-prompt">Q1. What is an accumulator?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. Another name for an array</li>
            <li class="quiz-option">B. A function that adds two numbers</li>
            <li class="quiz-option">C. The condition that ends a loop</li>
            <li class="quiz-option quiz-correct">D. A variable a loop keeps adding to ✓</li>
        </ul>
        <p class="quiz-explanation"><em>An accumulator starts at a known value and is updated on every loop pass, gradually building toward a final result like a sum.</em></p>
    </div>

    <div class="quiz-block" data-q="2">
        <p class="quiz-prompt">Q2. Where must an accumulator like total be declared?</p>
        <ul class="quiz-options">
            <li class="quiz-option quiz-correct">A. Before the loop, outside its body ✓</li>
            <li class="quiz-option">B. Inside the loop, at the very top</li>
            <li class="quiz-option">C. Inside the array's square brackets</li>
            <li class="quiz-option">D. Anywhere; the place doesn't matter</li>
        </ul>
        <p class="quiz-explanation"><em>Declaring total outside the loop means it's set to 0 only once — if it were inside the loop, it would reset to 0 every single pass instead of accumulating.</em></p>
    </div>

    <div class="quiz-block" data-q="3">
        <p class="quiz-prompt">Q3. What does <code>total = total + emitters[i];</code> mean?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. total is compared with emitters[i]</li>
            <li class="quiz-option">B. emitters[i] is set to the value of total</li>
            <li class="quiz-option quiz-correct">C. total becomes its old value plus emitters[i] ✓</li>
            <li class="quiz-option">D. total is reset to the value of emitters[i]</li>
        </ul>
        <p class="quiz-explanation"><em>This is a running update: the right side computes with total's current value, and the result becomes total's new value.</em></p>
    </div>

    <div class="quiz-block" data-q="4">
        <p class="quiz-prompt">Q4. Before the loop runs at all, what must total already hold for the sum to come out correct?</p>
        <ul class="quiz-options">
            <li class="quiz-option">A. The array's last element</li>
            <li class="quiz-option quiz-correct">B. 0 ✓</li>
            <li class="quiz-option">C. The array's size</li>
            <li class="quiz-option">D. The array's first element</li>
        </ul>
        <p class="quiz-explanation"><em>Since every element gets added onto whatever total already holds, it must start at 0 — any other starting value would shift the final sum.</em></p>
    </div>

</div>
