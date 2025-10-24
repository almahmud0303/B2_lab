<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Exam;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if exams already exist
        if (Exam::count() > 0) {
            $this->command->info('Exams already exist. Skipping...');
            return;
        }
        
        $courses = Course::all();
        
        $examTypes = [
            ['type' => 'quiz', 'marks' => 10, 'duration' => 30],
            ['type' => 'midterm', 'marks' => 30, 'duration' => 90],
            ['type' => 'final', 'marks' => 50, 'duration' => 180],
            ['type' => 'assignment', 'marks' => 10, 'duration' => 0],
        ];
        
        $venues = [
            'Building 1, Room 101',
            'Building 1, Room 102',
            'Building 2, Room 201',
            'Building 2, Room 202',
            'Building 3, Room 301',
            'Central Exam Hall',
            'Computer Lab 1',
            'Computer Lab 2',
            'Main Auditorium',
            'Lecture Theater 1',
        ];
        
        foreach ($courses as $course) {
            foreach ($examTypes as $examType) {
                // Determine status based on exam type and random chance
                $statuses = ['completed', 'completed', 'completed', 'scheduled', 'ongoing'];
                $status = $statuses[array_rand($statuses)];
                
                // Calculate exam date based on status
                if ($status === 'completed') {
                    $examDate = now()->subDays(rand(30, 120));
                } elseif ($status === 'ongoing') {
                    $examDate = now();
                } else {
                    $examDate = now()->addDays(rand(10, 60));
                }
                
                // For assignments, no specific venue or time
                if ($examType['type'] === 'assignment') {
                    Exam::create([
                        'course_id' => $course->id,
                        'title' => ucfirst($examType['type']) . ' - ' . $course->title,
                        'description' => 'Assignment for ' . $course->title . '. Submit through online portal.',
                        'type' => $examType['type'],
                        'exam_date' => $examDate->format('Y-m-d'),
                        'start_time' => '09:00:00',
                        'end_time' => '23:59:00',
                        'total_marks' => $examType['marks'],
                        'venue' => 'Online Submission',
                        'status' => $status,
                    ]);
                } else {
                    // Random start time between 9 AM and 2 PM
                    $startHour = rand(9, 14);
                    $startTime = sprintf('%02d:00:00', $startHour);
                    $endTime = date('H:i:s', strtotime($startTime . ' + ' . $examType['duration'] . ' minutes'));
                    
                    Exam::create([
                        'course_id' => $course->id,
                        'title' => ucfirst($examType['type']) . ' Exam - ' . $course->title,
                        'description' => ucfirst($examType['type']) . ' examination for ' . $course->title . '. Duration: ' . $examType['duration'] . ' minutes.',
                        'type' => $examType['type'],
                        'exam_date' => $examDate->format('Y-m-d'),
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'total_marks' => $examType['marks'],
                        'venue' => $venues[array_rand($venues)],
                        'status' => $status,
                    ]);
                }
            }
        }
    }
}

