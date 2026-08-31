{{-- Programming planet — onboarding questions, then the plan-reveal carousel.
     Standalone full-screen doc (same pattern as auth/login): no nav, no
     sidebar, no card padding.

     Transition: when <x-onboarding-question> finishes (its
     `onboarding-question:complete` event), this page hides the questions and
     reveals <x-plan-reveal-carousel>, which immediately fires the plan
     generation POST in the background and redirects to the node map once both
     the slides and the request are done. --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ 'Programming City' }} · TechLab</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />
</head>
<body style="margin:0;background:#ffffff">
  <x-onboarding-question />

  {{-- Hidden until the questions finish. `plan-endpoint` is the roadmap
       generation POST (placeholder payload until Nemotron generation lands
       inside PlanetController::generatePlan); `destination-url` is the course
       overview for this track (the node path with "Start learning"). --}}
  <div class="plan-slot" hidden>
    <x-plan-reveal-carousel
      :plan-endpoint="route('student.planet.plan', ['slug' => 'programming'])"
      :destination-url="route('student.planet.overview', ['slug' => 'programming'])" />
  </div>

  <script>
    (function () {
      document.addEventListener('onboarding-question:complete', function () {
        var questions = document.querySelector('.obq');
        if (questions) questions.style.display = 'none';

        var slot = document.querySelector('.plan-slot');
        if (!slot) return;
        slot.hidden = false;
        window.scrollTo(0, 0);

        var carousel = slot.querySelector('.prc');
        if (carousel) {
          /* Kick off the background plan-generation POST now. */
          carousel.dispatchEvent(new CustomEvent('plan-reveal:start'));
        }
      });
    })();
  </script>
</body>
</html>
