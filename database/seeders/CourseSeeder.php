<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Department;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if courses already exist
        if (Course::count() > 0) {
            $this->command->info('Courses already exist. Skipping...');
            return;
        }
        
        $departments = Department::all();
        
        // CSE Courses
        $cseCourses = [
            // 1st Year
            ['code' => 'CSE1101', 'title' => 'Structured Programming', 'credits' => 3, 'year' => '1st', 'semester' => '1st', 'description' => 'Introduction to C programming and problem solving', 'type' => 'compulsory'],
            ['code' => 'CSE1102', 'title' => 'Structured Programming Lab', 'credits' => 1, 'year' => '1st', 'semester' => '1st', 'description' => 'Laboratory work for structured programming', 'type' => 'compulsory'],
            ['code' => 'CSE1201', 'title' => 'Object Oriented Programming', 'credits' => 3, 'year' => '1st', 'semester' => '2nd', 'description' => 'Introduction to OOP concepts using C++', 'type' => 'compulsory'],
            ['code' => 'CSE1202', 'title' => 'Object Oriented Programming Lab', 'credits' => 1, 'year' => '1st', 'semester' => '2nd', 'description' => 'Laboratory work for OOP', 'type' => 'compulsory'],
            ['code' => 'CSE1203', 'title' => 'Discrete Mathematics', 'credits' => 3, 'year' => '1st', 'semester' => '2nd', 'description' => 'Mathematical foundations for computer science', 'type' => 'compulsory'],
            
            // 2nd Year
            ['code' => 'CSE2101', 'title' => 'Data Structures', 'credits' => 3, 'year' => '2nd', 'semester' => '3rd', 'description' => 'Study of fundamental data structures and algorithms', 'type' => 'compulsory'],
            ['code' => 'CSE2102', 'title' => 'Data Structures Lab', 'credits' => 1, 'year' => '2nd', 'semester' => '3rd', 'description' => 'Laboratory work for data structures', 'type' => 'compulsory'],
            ['code' => 'CSE2103', 'title' => 'Digital Logic Design', 'credits' => 3, 'year' => '2nd', 'semester' => '3rd', 'description' => 'Digital circuits and logic design', 'type' => 'compulsory'],
            ['code' => 'CSE2201', 'title' => 'Algorithms', 'credits' => 3, 'year' => '2nd', 'semester' => '4th', 'description' => 'Design and analysis of algorithms', 'type' => 'compulsory'],
            ['code' => 'CSE2202', 'title' => 'Algorithms Lab', 'credits' => 1, 'year' => '2nd', 'semester' => '4th', 'description' => 'Laboratory work for algorithms', 'type' => 'compulsory'],
            
            // 3rd Year
            ['code' => 'CSE3101', 'title' => 'Database Management Systems', 'credits' => 3, 'year' => '3rd', 'semester' => '5th', 'description' => 'Database design and SQL', 'type' => 'compulsory'],
            ['code' => 'CSE3102', 'title' => 'Database Management Systems Lab', 'credits' => 1, 'year' => '3rd', 'semester' => '5th', 'description' => 'Laboratory work for DBMS', 'type' => 'compulsory'],
            ['code' => 'CSE3103', 'title' => 'Computer Networks', 'credits' => 3, 'year' => '3rd', 'semester' => '5th', 'description' => 'Networking fundamentals and protocols', 'type' => 'compulsory'],
            ['code' => 'CSE3201', 'title' => 'Software Engineering', 'credits' => 3, 'year' => '3rd', 'semester' => '6th', 'description' => 'Software development lifecycle and methodologies', 'type' => 'compulsory'],
            ['code' => 'CSE3202', 'title' => 'Operating Systems', 'credits' => 3, 'year' => '3rd', 'semester' => '6th', 'description' => 'OS concepts and design', 'type' => 'compulsory'],
            
            // 4th Year - Mix of compulsory and optional
            ['code' => 'CSE4101', 'title' => 'Artificial Intelligence', 'credits' => 3, 'year' => '4th', 'semester' => '7th', 'description' => 'AI fundamentals and applications', 'type' => 'compulsory'],
            ['code' => 'CSE4102', 'title' => 'Machine Learning', 'credits' => 3, 'year' => '4th', 'semester' => '7th', 'description' => 'ML algorithms and techniques', 'type' => 'optional', 'max_enrollments' => 40],
            ['code' => 'CSE4103', 'title' => 'Cloud Computing', 'credits' => 3, 'year' => '4th', 'semester' => '7th', 'description' => 'Cloud platforms and services', 'type' => 'optional', 'max_enrollments' => 35],
            ['code' => 'CSE4104', 'title' => 'Blockchain Technology', 'credits' => 3, 'year' => '4th', 'semester' => '7th', 'description' => 'Blockchain and cryptocurrencies', 'type' => 'optional', 'max_enrollments' => 30],
            ['code' => 'CSE4201', 'title' => 'Computer Graphics', 'credits' => 3, 'year' => '4th', 'semester' => '8th', 'description' => 'Graphics programming and visualization', 'type' => 'optional', 'max_enrollments' => 30],
            ['code' => 'CSE4202', 'title' => 'Thesis/Project', 'credits' => 3, 'year' => '4th', 'semester' => '8th', 'description' => 'Final year project work', 'type' => 'compulsory'],
        ];
        
        // EEE Courses
        $eeeCourses = [
            ['code' => 'EEE1101', 'title' => 'Electrical Circuits I', 'credits' => 3, 'year' => '1st', 'semester' => '1st', 'description' => 'Basic circuit analysis', 'type' => 'compulsory'],
            ['code' => 'EEE1201', 'title' => 'Electrical Circuits II', 'credits' => 3, 'year' => '1st', 'semester' => '2nd', 'description' => 'Advanced circuit analysis', 'type' => 'compulsory'],
            ['code' => 'EEE2101', 'title' => 'Electronics I', 'credits' => 3, 'year' => '2nd', 'semester' => '3rd', 'description' => 'Analog electronics', 'type' => 'compulsory'],
            ['code' => 'EEE2201', 'title' => 'Electronics II', 'credits' => 3, 'year' => '2nd', 'semester' => '4th', 'description' => 'Digital electronics', 'type' => 'compulsory'],
            ['code' => 'EEE3101', 'title' => 'Power Systems', 'credits' => 3, 'year' => '3rd', 'semester' => '5th', 'description' => 'Power generation and distribution', 'type' => 'compulsory'],
            ['code' => 'EEE3201', 'title' => 'Control Systems', 'credits' => 3, 'year' => '3rd', 'semester' => '6th', 'description' => 'Automatic control theory', 'type' => 'compulsory'],
            ['code' => 'EEE4101', 'title' => 'Communication Engineering', 'credits' => 3, 'year' => '4th', 'semester' => '7th', 'description' => 'Communication systems', 'type' => 'compulsory'],
            ['code' => 'EEE4201', 'title' => 'Renewable Energy', 'credits' => 3, 'year' => '4th', 'semester' => '8th', 'description' => 'Solar and wind energy systems', 'type' => 'optional', 'max_enrollments' => 35],
        ];
        
        // ME Courses
        $meCourses = [
            ['code' => 'ME1101', 'title' => 'Engineering Mechanics', 'credits' => 3, 'year' => '1st', 'semester' => '1st', 'description' => 'Statics and dynamics', 'type' => 'compulsory'],
            ['code' => 'ME1201', 'title' => 'Engineering Drawing', 'credits' => 2, 'year' => '1st', 'semester' => '2nd', 'description' => 'Technical drawing and CAD', 'type' => 'compulsory'],
            ['code' => 'ME2101', 'title' => 'Thermodynamics', 'credits' => 3, 'year' => '2nd', 'semester' => '3rd', 'description' => 'Laws of thermodynamics', 'type' => 'compulsory'],
            ['code' => 'ME2201', 'title' => 'Fluid Mechanics', 'credits' => 3, 'year' => '2nd', 'semester' => '4th', 'description' => 'Fluid flow and properties', 'type' => 'compulsory'],
            ['code' => 'ME3101', 'title' => 'Heat Transfer', 'credits' => 3, 'year' => '3rd', 'semester' => '5th', 'description' => 'Heat transfer mechanisms', 'type' => 'compulsory'],
            ['code' => 'ME3201', 'title' => 'Manufacturing Processes', 'credits' => 3, 'year' => '3rd', 'semester' => '6th', 'description' => 'Manufacturing methods', 'type' => 'compulsory'],
            ['code' => 'ME4101', 'title' => 'Machine Design', 'credits' => 3, 'year' => '4th', 'semester' => '7th', 'description' => 'Design of machine elements', 'type' => 'compulsory'],
            ['code' => 'ME4201', 'title' => 'Automotive Engineering', 'credits' => 3, 'year' => '4th', 'semester' => '8th', 'description' => 'Vehicle design and systems', 'type' => 'optional', 'max_enrollments' => 30],
        ];
        
        // CE Courses
        $ceCourses = [
            ['code' => 'CE1101', 'title' => 'Engineering Surveying', 'credits' => 3, 'year' => '1st', 'semester' => '1st', 'description' => 'Land surveying techniques', 'type' => 'compulsory'],
            ['code' => 'CE1201', 'title' => 'Building Materials', 'credits' => 3, 'year' => '1st', 'semester' => '2nd', 'description' => 'Construction materials', 'type' => 'compulsory'],
            ['code' => 'CE2101', 'title' => 'Structural Analysis I', 'credits' => 3, 'year' => '2nd', 'semester' => '3rd', 'description' => 'Analysis of structures', 'type' => 'compulsory'],
            ['code' => 'CE2201', 'title' => 'Structural Analysis II', 'credits' => 3, 'year' => '2nd', 'semester' => '4th', 'description' => 'Advanced structural analysis', 'type' => 'compulsory'],
            ['code' => 'CE3101', 'title' => 'Geotechnical Engineering', 'credits' => 3, 'year' => '3rd', 'semester' => '5th', 'description' => 'Soil mechanics', 'type' => 'compulsory'],
            ['code' => 'CE3201', 'title' => 'Transportation Engineering', 'credits' => 3, 'year' => '3rd', 'semester' => '6th', 'description' => 'Highway and traffic engineering', 'type' => 'compulsory'],
            ['code' => 'CE4101', 'title' => 'Earthquake Engineering', 'credits' => 3, 'year' => '4th', 'semester' => '7th', 'description' => 'Seismic design', 'type' => 'optional', 'max_enrollments' => 25],
            ['code' => 'CE4201', 'title' => 'Construction Management', 'credits' => 3, 'year' => '4th', 'semester' => '8th', 'description' => 'Project planning and control', 'type' => 'compulsory'],
        ];
        
        // Add courses for each department
        $allCourses = [
            'CSE' => $cseCourses,
            'EEE' => $eeeCourses,
            'ME' => $meCourses,
            'CE' => $ceCourses,
        ];
        
        foreach ($allCourses as $deptCode => $courses) {
            $department = Department::where('code', $deptCode)->first();
            if (!$department) continue;
            
            $teachers = Teacher::where('department_id', $department->id)->get();
            if ($teachers->isEmpty()) continue;
            
            foreach ($courses as $course) {
                $teacher = $teachers->random();
                
                Course::create([
                    'department_id' => $department->id,
                    'teacher_id' => $teacher->id,
                    'course_code' => $course['code'],
                    'title' => $course['title'],
                    'description' => $course['description'],
                    'credits' => $course['credits'],
                    'semester' => $course['semester'],
                    'academic_year' => $course['year'],
                    'course_type' => $course['type'] ?? 'compulsory', // Add course type
                    'prerequisites' => $course['prerequisites'] ?? null,
                    'max_students' => rand(40, 60),
                    'max_enrollments' => isset($course['max_enrollments']) ? $course['max_enrollments'] : null,
                    'is_active' => true,
                ]);
            }
        }
    }
}
