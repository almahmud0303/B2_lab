<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Department;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if students already exist
        if (Student::count() > 0) {
            $this->command->info('Students already exist. Skipping...');
            return;
        }
        
        $students = User::where('role', 'student')->get();
        $departments = Department::whereIn('code', ['CSE', 'EEE', 'ME', 'CE', 'ECE', 'IEM'])->get();
        
        $academicYears = ['1st', '2nd', '3rd', '4th'];
        $semesters = ['1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th'];
        $statuses = ['active', 'active', 'active', 'active', 'graduated']; // More active students
        
        $guardianNames = [
            'Md. Abdul Karim', 'Md. Hafizur Rahman', 'Md. Rafiqul Islam', 'Md. Shahjahan Ali',
            'Md. Nurul Haque', 'Md. Abdur Razzak', 'Md. Monsur Ali', 'Md. Shamsul Huda',
            'Mrs. Rahima Begum', 'Mrs. Amena Khatun', 'Mrs. Fatema Begum', 'Mrs. Hasina Akter',
        ];
        
        foreach ($students as $index => $user) {
            $department = $departments->random();
            $academicYear = $academicYears[array_rand($academicYears)];
            
            // Calculate semester based on academic year
            $yearIndex = array_search($academicYear, $academicYears);
            $semesterIndex = ($yearIndex * 2) + rand(0, 1); // Each year has 2 semesters
            $semester = $semesters[$semesterIndex];
            
            // Generate admission number and student ID
            $admissionYear = date('Y') - $yearIndex;
            $admissionNumber = $admissionYear . $department->code . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            $studentId = substr($admissionYear, 2) . $department->code . str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            
            // Calculate CGPA (higher for senior students)
            $cgpa = $academicYear === '1st' ? rand(30, 36) / 10 : rand(32, 40) / 10;
            
            $status = $statuses[array_rand($statuses)];
            if ($academicYear === '4th' && rand(1, 3) === 1) {
                $status = 'graduated';
                $cgpa = rand(33, 40) / 10;
            }
            
            Student::create([
                'user_id' => $user->id,
                'department_id' => $department->id,
                'student_id' => $studentId,
                'admission_number' => $admissionNumber,
                'admission_date' => date('Y-m-d', strtotime($admissionYear . '-01-15')),
                'academic_year' => $academicYear,
                'semester' => $semester,
                'guardian_name' => $guardianNames[array_rand($guardianNames)],
                'guardian_phone' => '+880-1' . rand(3, 9) . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT),
                'guardian_address' => ['Khulna', 'Dhaka', 'Chittagong', 'Rajshahi', 'Sylhet', 'Barisal'][array_rand(['Khulna', 'Dhaka', 'Chittagong', 'Rajshahi', 'Sylhet', 'Barisal'])] . ', Bangladesh',
                'cgpa' => $cgpa,
                'status' => $status,
                'is_active' => $status === 'active',
            ]);
        }
    }
}
