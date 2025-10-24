<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if departments already exist
        if (Department::count() > 0) {
            $this->command->info('Departments already exist. Skipping...');
            return;
        }
        
        $departments = [
            [
                'name' => 'Computer Science and Engineering',
                'code' => 'CSE',
                'description' => 'The Department of Computer Science and Engineering at KUET offers undergraduate and postgraduate programs in various areas of computer science and engineering.',
                'head_of_department' => 'Prof. Dr. Md. Abdul Aziz',
                'is_active' => true,
            ],
            [
                'name' => 'Electrical and Electronic Engineering',
                'code' => 'EEE',
                'description' => 'The Department of Electrical and Electronic Engineering focuses on power systems, electronics, control systems, and communication engineering.',
                'head_of_department' => 'Prof. Dr. Md. Shahidul Islam',
                'is_active' => true,
            ],
            [
                'name' => 'Mechanical Engineering',
                'code' => 'ME',
                'description' => 'The Department of Mechanical Engineering provides comprehensive education in thermal engineering, manufacturing, and mechanical design.',
                'head_of_department' => 'Prof. Dr. Md. Mahbubur Rahman',
                'is_active' => true,
            ],
            [
                'name' => 'Civil Engineering',
                'code' => 'CE',
                'description' => 'The Department of Civil Engineering specializes in structural engineering, geotechnical engineering, transportation, and water resources.',
                'head_of_department' => 'Prof. Dr. Md. Mizanur Rahman',
                'is_active' => true,
            ],
            [
                'name' => 'Electronics and Communication Engineering',
                'code' => 'ECE',
                'description' => 'The Department of Electronics and Communication Engineering focuses on electronic circuits, communication systems, and signal processing.',
                'head_of_department' => 'Prof. Dr. Md. Rafiqul Islam',
                'is_active' => true,
            ],
            [
                'name' => 'Industrial Engineering and Management',
                'code' => 'IEM',
                'description' => 'The Department of Industrial Engineering and Management combines engineering principles with management practices for optimization.',
                'head_of_department' => 'Prof. Dr. Md. Ahsan Habib',
                'is_active' => true,
            ],
            [
                'name' => 'Urban and Regional Planning',
                'code' => 'URP',
                'description' => 'The Department of Urban and Regional Planning focuses on sustainable urban development and planning strategies.',
                'head_of_department' => 'Prof. Dr. Kazi Saiful Islam',
                'is_active' => true,
            ],
            [
                'name' => 'Architecture',
                'code' => 'ARCH',
                'description' => 'The Department of Architecture emphasizes architectural design, building technology, and environmental sustainability.',
                'head_of_department' => 'Prof. Dr. Shamsul Wares',
                'is_active' => true,
            ],
            [
                'name' => 'Building Engineering and Construction Management',
                'code' => 'BECM',
                'description' => 'The Department of Building Engineering and Construction Management focuses on construction technology and project management.',
                'head_of_department' => 'Prof. Dr. Md. Jahangir Alam',
                'is_active' => true,
            ],
            [
                'name' => 'Chemistry',
                'code' => 'CHEM',
                'description' => 'The Department of Chemistry provides fundamental and applied chemistry education for engineering students.',
                'head_of_department' => 'Prof. Dr. Md. Abdus Salam',
                'is_active' => true,
            ],
            [
                'name' => 'Mathematics',
                'code' => 'MATH',
                'description' => 'The Department of Mathematics offers courses in pure and applied mathematics supporting engineering programs.',
                'head_of_department' => 'Prof. Dr. Md. Shafiqul Islam',
                'is_active' => true,
            ],
            [
                'name' => 'Physics',
                'code' => 'PHY',
                'description' => 'The Department of Physics provides fundamental physics education and research opportunities for undergraduate and postgraduate students.',
                'head_of_department' => 'Prof. Dr. Md. Abdul Gafur',
                'is_active' => true,
            ],
            [
                'name' => 'Humanities',
                'code' => 'HUM',
                'description' => 'The Department of Humanities offers courses in English, economics, and social sciences to complement technical education.',
                'head_of_department' => 'Prof. Dr. Md. Humayun Kabir',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
