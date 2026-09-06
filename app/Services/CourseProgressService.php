<?php

namespace App\Services;

class CourseProgressService
{
    /**
     * Get progress data for a course
     *
     * @param string $courseSlug
     * @return array
     */
    public static function getProgress(string $courseSlug): array
    {
        // Return static/fake data for now - structured so real data can be swapped in later
        return [
            'modules' => [
                'M1' => [
                    'percent' => 60,
                    'lessons' => [
                        'lesson-01' => ['status' => 'completed'],
                        'lesson-02' => ['status' => 'completed'],
                        'lesson-03' => ['status' => 'in_progress'],
                        'lesson-04' => ['status' => 'not_started'],
                        'lesson-05' => ['status' => 'not_started'],
                        'lesson-06' => ['status' => 'not_started'],
                    ]
                ],
                'M2' => [
                    'percent' => 0,
                    'lessons' => [
                        'lesson-01' => ['status' => 'not_started'],
                        'lesson-02' => ['status' => 'not_started'],
                        'lesson-03' => ['status' => 'not_started'],
                        'lesson-04' => ['status' => 'not_started'],
                        'lesson-05' => ['status' => 'not_started'],
                        'lesson-06' => ['status' => 'not_started'],
                    ]
                ],
                'M3' => [
                    'percent' => 0,
                    'lessons' => [
                        'lesson-01' => ['status' => 'not_started'],
                        'lesson-02' => ['status' => 'not_started'],
                        'lesson-03' => ['status' => 'not_started'],
                        'lesson-04' => ['status' => 'not_started'],
                        'lesson-05' => ['status' => 'not_started'],
                    ]
                ],
                'M4' => [
                    'percent' => 0,
                    'lessons' => [
                        'lesson-01' => ['status' => 'not_started'],
                        'lesson-02' => ['status' => 'not_started'],
                        'lesson-03' => ['status' => 'not_started'],
                        'lesson-04' => ['status' => 'not_started'],
                        'lesson-05' => ['status' => 'not_started'],
                        'lesson-06' => ['status' => 'not_started'],
                        'lesson-07' => ['status' => 'not_started'],
                        'lesson-08' => ['status' => 'not_started'],
                    ]
                ],
                'M5' => [
                    'percent' => 0,
                    'lessons' => [
                        'lesson-01' => ['status' => 'not_started'],
                        'lesson-02' => ['status' => 'not_started'],
                        'lesson-03' => ['status' => 'not_started'],
                        'lesson-04' => ['status' => 'not_started'],
                        'lesson-05' => ['status' => 'not_started'],
                    ]
                ],
                'M6' => [
                    'percent' => 0,
                    'lessons' => [
                        'lesson-01' => ['status' => 'not_started'],
                        'lesson-02' => ['status' => 'not_started'],
                        'lesson-03' => ['status' => 'not_started'],
                        'lesson-04' => ['status' => 'not_started'],
                    ]
                ],
                'M7' => [
                    'percent' => 0,
                    'lessons' => [
                        'lesson-01' => ['status' => 'not_started'],
                    ]
                ],
            ]
        ];
    }

    /**
     * Get lesson status
     *
     * @param string $courseSlug
     * @param string $module
     * @param string $lesson
     * @return string
     */
    public static function getLessonStatus(string $courseSlug, string $module, string $lesson): string
    {
        $progress = self::getProgress($courseSlug);

        if (isset($progress['modules'][$module]['lessons'][$lesson])) {
            return $progress['modules'][$module]['lessons'][$lesson]['status'];
        }

        return 'not_started';
    }

    /**
     * Get module completion percentage
     *
     * @param string $courseSlug
     * @param string $module
     * @return int
     */
    public static function getModulePercent(string $courseSlug, string $module): int
    {
        $progress = self::getProgress($courseSlug);

        if (isset($progress['modules'][$module]['percent'])) {
            return $progress['modules'][$module]['percent'];
        }

        return 0;
    }
}