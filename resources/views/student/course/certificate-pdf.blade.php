{{--
  The certificate as a PDF (CertificatePdfService, rendered by Dompdf). Self-paced: issued by
  TechLab. Class: signed by the faculty member. A white A4
  card: the certificate on the left, a pale TechLab panel with network line art on the right.
  Dompdf lays out absolute boxes far more reliably than flex or grid, so every block is placed
  in points (the page is 595 × 842).
--}}
@php
  $fonts = resource_path('fonts/certificate');

  // Line art for the side panel: a few nodes and the links between them, in TechLab blue.
  $nodes = [[28, 40, 5], [118, 70, 9], [70, 150, 4], [132, 200, 6], [40, 250, 11], [104, 300, 4], [20, 360, 5], [126, 395, 13], [64, 450, 6], [140, 500, 4]];
  $links = [[0, 1], [0, 2], [1, 2], [1, 3], [2, 4], [3, 4], [3, 5], [4, 5], [4, 6], [5, 7], [6, 8], [7, 8], [7, 9], [8, 9]];
  $art = '<svg xmlns="http://www.w3.org/2000/svg" width="150" height="540" viewBox="0 0 150 540">';
  foreach ($links as [$a, $b]) {
      $art .= sprintf('<line x1="%d" y1="%d" x2="%d" y2="%d" stroke="#73b6ff" stroke-width="0.8" stroke-opacity="0.55"/>', $nodes[$a][0], $nodes[$a][1], $nodes[$b][0], $nodes[$b][1]);
  }
  foreach ($nodes as [$x, $y, $r]) {
      $art .= sprintf('<circle cx="%d" cy="%d" r="%d" fill="#ffffff" stroke="#73b6ff" stroke-width="1"/>', $x, $y, $r);
      if ($r > 8) {
          $art .= sprintf('<circle cx="%d" cy="%d" r="%d" fill="#73b6ff" fill-opacity="0.35"/>', $x, $y, (int) ($r / 2.5));
      }
  }
  $art .= '</svg>';
  $track = empty($selfPaced) ? 'Class Learning' : 'Self-paced Learning';

  // Two light diagonals across the bottom of the panel, following the card's rounded corner.
  $sweep = '<svg xmlns="http://www.w3.org/2000/svg" width="150" height="150" viewBox="0 0 150 150">'
      .'<path d="M150 0 V140 Q150 150 140 150 H0 Z" fill="#73b6ff" fill-opacity="0.16"/>'
      .'<path d="M150 60 V140 Q150 150 140 150 H62 Z" fill="#73b6ff" fill-opacity="0.22"/></svg>';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Certificate · {{ $courseTitle }} · TechLab</title>
  <style>
    @font-face { font-family: 'Poppins'; font-weight: 400; src: url('{{ $fonts }}/Poppins-Regular.ttf') format('truetype'); }
    @font-face { font-family: 'Poppins'; font-weight: 500; src: url('{{ $fonts }}/Poppins-Medium.ttf') format('truetype'); }
    @font-face { font-family: 'Poppins'; font-weight: 600; src: url('{{ $fonts }}/Poppins-SemiBold.ttf') format('truetype'); }

    @page { size: A4 portrait; margin: 0; }
    * { margin: 0; padding: 0; }
    body { font-family: 'Poppins', sans-serif; color: #1f2430; background: #ffffff; }

    .card { position: absolute; left: 22pt; top: 22pt; width: 551pt; height: 798pt; border: 0.75pt solid #d5dbe5; border-radius: 10pt; }

    .panel { position: absolute; right: 0; top: 0; width: 150pt; height: 798pt; background: #f2f7ff; border-left: 0.75pt solid #dce8fa; border-top-right-radius: 10pt; border-bottom-right-radius: 10pt; }
    .panel .mark { position: absolute; left: 0; top: 46pt; width: 150pt; text-align: center; }
    .panel .mark img { width: 34pt; height: 34pt; }
    .panel .mark .name { margin-top: 6pt; font-size: 15pt; font-weight: 600; color: #1f2430; }
    .panel .mark .sub { margin-top: 1pt; font-size: 8.5pt; color: #4a7fc4; }
    .panel .art { position: absolute; left: 0; top: 150pt; width: 150pt; }
    .panel .sweep { position: absolute; left: 0; bottom: 0; width: 150pt; height: 150pt; }

    .brand { position: absolute; left: 44pt; top: 46pt; }
    .brand img { width: 30pt; height: 30pt; vertical-align: middle; }
    .brand .word { display: inline-block; margin-left: 8pt; vertical-align: middle; }
    .brand .word b { display: block; font-size: 15pt; font-weight: 600; line-height: 1.1; color: #1f2430; }
    .brand .word span { display: block; font-size: 8.5pt; line-height: 1.2; color: #4a7fc4; }

    .middle { position: absolute; left: 30pt; top: 215pt; width: 370pt; text-align: center; }
    .kicker { font-size: 11pt; color: #5b6475; }
    .course { margin-top: 10pt; font-size: 25pt; font-weight: 600; line-height: 1.2; color: #151a24; }
    .code { margin-top: 5pt; font-size: 8.5pt; letter-spacing: 1pt; color: #8a93a3; }
    .awarded { margin-top: 26pt; font-size: 10.5pt; color: #5b6475; }
    .student { margin-top: 6pt; font-size: 28pt; font-weight: 600; line-height: 1.25; letter-spacing: -0.3pt; color: #2f6fca; }
    .what { margin: 20pt auto 0; width: 300pt; font-size: 9.5pt; line-height: 14pt; color: #3b4352; }

    .sign { position: absolute; left: 44pt; top: 618pt; width: 200pt; }
    /* A class certificate has the signature and the instructor's name, so it starts higher. */
    .sign.signed { top: 578pt; }
    .sign .issuer { font-size: 14pt; font-weight: 600; line-height: 1.2; color: #1f2430; }
    .sign .signature { max-width: 150pt; max-height: 40pt; }
    .sign .line { margin-top: 8pt; width: 150pt; border-top: 0.6pt solid #9aa3b2; }
    .sign .who { margin-top: 6pt; font-size: 9pt; font-weight: 500; color: #1f2430; }
    .sign .role { margin-top: 6pt; font-size: 8pt; line-height: 1.45; color: #5b6475; }

    .date { position: absolute; left: 236pt; top: 646pt; width: 150pt; text-align: right; }
    .date b { display: block; font-size: 9.5pt; font-weight: 500; color: #1f2430; }
    .date span { display: block; margin-top: 2pt; font-size: 7pt; color: #8a93a3; }

    .notice { position: absolute; left: 44pt; top: 716pt; width: 342pt; padding-top: 7pt; border-top: 0.5pt solid #e3e7ee; font-size: 6.4pt; line-height: 9pt; color: #8a93a3; }
    .notice b { color: #5b6475; font-weight: 500; }
  </style>
</head>
<body>
  <div class="card">
    <div class="panel">
      <div class="mark">
        <img src="{{ public_path('apple-touch-icon.png') }}" alt="">
        <div class="name">TechLab</div>
        <div class="sub">{{ $track }}</div>
      </div>
      <img class="art" src="data:image/svg+xml;base64,{{ base64_encode($art) }}" alt="">
      <img class="sweep" src="data:image/svg+xml;base64,{{ base64_encode($sweep) }}" alt="">
    </div>

    <div class="brand">
      <img src="{{ public_path('apple-touch-icon.png') }}" alt="">
      <div class="word">
        <b>TechLab</b>
        <span>{{ $planetTitle }}</span>
      </div>
    </div>

    <div class="middle">
      <p class="kicker">Certificate of Completion</p>
      <p class="course">{{ $courseTitle }}</p>
      @if ($courseCode)<p class="code">{{ $courseCode }}</p>@endif

      <p class="awarded">Awarded to</p>
      <p class="student">{{ $studentName }}</p>

      <p class="what">
        For successfully completing all {{ $lessons }} {{ \Illuminate\Support\Str::plural('lesson', $lessons) }}@if ($labs > 0) and {{ $labs }} hands-on {{ \Illuminate\Support\Str::plural('lab', $labs) }}@endif
        of the TechLab {{ $courseTitle }} course, passing its module exams and final exam,
        and demonstrating an understanding of its core concepts.
      </p>
    </div>

    <div class="sign {{ $captain ? 'signed' : '' }}">
      @if ($signature)
        <img class="signature" src="{{ $signature }}" alt="">
      @else
        <p class="issuer">{{ $captain ?? 'TechLab' }}</p>
      @endif
      <div class="line"></div>
      @if ($captain)<p class="who">{{ $captain }}</p>@endif
      <p class="role">{{ $captain ? 'Instructor, TechLab' : 'Self-paced Learning Program' }}</p>
    </div>

    <div class="date">
      <b>{{ $issuedAt->format('F j, Y') }}</b>
      <span>Certificate No. {{ $certificateId }}</span>
    </div>

    <p class="notice">
      <b>Notice:</b> This certificate shows that the course above was completed on TechLab. It is not yet
      accredited or formally recognized by any college, university, government agency or IT industry
      certification body, and it does not replace academic credit or an official industry certification.
      TechLab is working toward wider recognition in the future.
    </p>
  </div>
</body>
</html>
