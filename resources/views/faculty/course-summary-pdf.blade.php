{{-- The course summary handout, rendered to PDF by CourseSummaryPdfService (dompdf: plain CSS, no flex/grid). --}}
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>{{ $title }} · Course summary</title>
<style>
  @page { margin: 54px 56px 64px; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; line-height: 1.5; color: #1d2333; }

  .cover-kicker { font-size: 8pt; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; color: #2f6fd6; }
  .cover-title { font-size: 24pt; font-weight: bold; line-height: 1.15; margin: 6px 0 4px; }
  .cover-sub { color: #5a6378; font-size: 10pt; margin: 0; }
  .cover-bar { height: 5px; background: #73b6ff; margin: 18px 0 22px; }

  .toc-h { font-size: 9pt; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; color: #5a6378; margin: 0 0 6px; }
  table.toc { width: 100%; border-collapse: collapse; }
  table.toc td { padding: 7px 0; border-bottom: 1px solid #e3e7ef; vertical-align: top; }
  table.toc td.n { width: 84px; padding-right: 10px; color: #2f6fd6; font-weight: bold; }
  table.toc td.c { width: 90px; text-align: right; color: #5a6378; font-size: 9pt; }

  .module { page-break-before: always; }
  .mod-kicker { font-size: 8pt; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; color: #2f6fd6; }
  .mod-title { font-size: 17pt; font-weight: bold; margin: 2px 0 0; padding-bottom: 8px; border-bottom: 3px solid #73b6ff; }

  .lesson { margin-top: 18px; }
  .lesson-label { font-size: 8pt; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; color: #5a6378; }
  .lesson-title { font-size: 12pt; font-weight: bold; margin: 1px 0 4px; }
  .goal { margin: 0 0 8px; padding: 6px 10px; background: #f2f6fd; border-left: 3px solid #2f6fd6; }
  .goal b { color: #2f6fd6; }

  table.terms { width: 100%; border-collapse: collapse; }
  table.terms tr { page-break-inside: avoid; }
  table.terms td { padding: 5px 8px; border-top: 1px solid #e3e7ef; vertical-align: top; }
  table.terms td.t { width: 30%; font-weight: bold; }
  .none { color: #5a6378; font-style: italic; margin: 0; }
</style>
</head>
<body>
  <p class="cover-kicker">Course summary{{ $code ? ' · '.$code : '' }}</p>
  <h1 class="cover-title">{{ $title }}</h1>
  <p class="cover-sub">
    Every lesson's goal and key terms, taken from the lessons students study.
    Write your quiz and final exam questions from this.
  </p>
  <div class="cover-bar"></div>

  <p class="toc-h">What's inside</p>
  <table class="toc">
    @foreach ($modules as $m)
      <tr>
        <td class="n">Module {{ $m['number'] }}</td>
        <td>{{ $m['title'] }}</td>
        <td class="c">{{ count($m['lessons']) }} {{ \Illuminate\Support\Str::plural('lesson', count($m['lessons'])) }}</td>
      </tr>
    @endforeach
  </table>

  @foreach ($modules as $m)
    <section class="module">
      <p class="mod-kicker">Module {{ $m['number'] }}</p>
      <h2 class="mod-title">{{ $m['title'] }}</h2>

      @foreach ($m['lessons'] as $lesson)
        <div class="lesson">
          @if ($lesson['label'])
            <div class="lesson-label">{{ $lesson['label'] }}</div>
          @endif
          <div class="lesson-title">{{ $lesson['title'] }}</div>
          @if ($lesson['objective'])
            <p class="goal"><b>Goal:</b> {{ $lesson['objective'] }}</p>
          @endif
          @if ($lesson['terms'])
            <table class="terms">
              @foreach ($lesson['terms'] as $term)
                <tr><td class="t">{{ $term['term'] }}</td><td>{{ $term['explains'] }}</td></tr>
              @endforeach
            </table>
          @else
            <p class="none">No key terms marked in this lesson.</p>
          @endif
        </div>
      @endforeach
    </section>
  @endforeach
</body>
</html>
