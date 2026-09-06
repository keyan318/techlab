{{-- Lesson Viewer --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $lesson['title'] ?? 'Lesson' }} · {{ $course['title'] }} · TechLab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />
</head>
<body class="min-h-screen bg-gray-50 flex flex-col">
    <x-course.course-player :course="$course" :activeLesson="$lesson['id'] ?? 'programming-s0-l0'" />

    <!-- Lesson Content -->
    @if($lessonContent)
        <div class="flex-1 p-8">
            {!! $lessonContent !!}
        </div>
    @else
        <div class="flex-1 p-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h1 class="text-2xl font-bold mb-4">{{ $lesson['title'] ?? 'Lesson' }}</h1>

                @if(isset($lesson['objective']))
                    <p class="text-lg text-gray-600 mb-6">{{ $lesson['objective'] }}</p>
                @endif

                @if(isset($lesson['simple_explanation']))
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold mb-2">Simple Explanation</h2>
                        <p class="text-gray-700">{{ $lesson['simple_explanation'] }}</p>
                    </div>
                @endif

                @if(isset($lesson['astro_explanation']))
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold mb-2">Astro's Explanation</h2>
                        <p class="text-gray-700 italic">{{ $lesson['astro_explanation'] }}</p>
                    </div>
                @endif

                @if(isset($lesson['code_example']))
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold mb-2">Code Example</h2>
                        <pre class="bg-gray-50 p-4 rounded overflow-x-auto">{{ $lesson['code_example'] }}</pre>
                    </div>
                @endif

                @if(isset($lesson['interactive_exercise']))
                    @include('student.planets.python-editor', [
                        'lesson' => $lesson,
                        'slug' => $slug,
                        'starterCode' => $lesson['interactive_exercise']['starter_code'] ?? '# your code here\n'
                    ])
                @elseif(isset($lesson['challenge']))
                    @include('student.planets.python-editor', [
                        'lesson' => $lesson,
                        'slug' => $slug,
                        'starterCode' => $lesson['challenge']['starter_code'] ?? '# your code here\n'
                    ])
                @endif

                @if(isset($lesson['quiz']) && is_array($lesson['quiz']) && count($lesson['quiz']) > 0)
                    <div class="mt-8">
                        <h2 class="text-xl font-semibold mb-4">Quiz</h2>
                        @foreach($lesson['quiz'] as $index => $question)
                            <div class="bg-white rounded-lg shadow p-6 mb-4">
                                <p class="font-medium mb-4">{{ $index + 1 }}. {{ $question['question'] }}</p>
                                @foreach(['a', 'b', 'c', 'd'] as $option)
                                    @if(isset($question['options'][$option]))
                                        <label class="block mb-2">
                                            <input type="radio" name="question_{{ $index }}" value="{{ $option }}" class="mr-2">
                                            {{ strtoupper($option) }}. {{ $question['options'][$option] }}
                                        </label>
                                    @endif
                                @endforeach
                                @if(isset($question['correct']))
                                    <button onclick="alert('Correct answer: {{ strtoupper($question['correct']) }}. {{ $question['explanation'] }}')"
                                            class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                        Show Answer
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
</body>
</html>