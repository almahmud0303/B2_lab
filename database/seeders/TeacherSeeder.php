<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Department;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if teachers already exist
        if (Teacher::count() > 0) {
            $this->command->info('Teachers already exist. Skipping...');
            return;
        }
        
        $teachers = User::where('role', 'teacher')->get();
        
        $teacherData = [
            // CSE Department
            ['email' => 'aziz@cse.kuet.ac.bd', 'dept' => 'CSE', 'qualification' => 'Ph.D. in Computer Science', 'specialization' => 'Artificial Intelligence, Machine Learning'],
            ['email' => 'azhasan@cse.kuet.ac.bd', 'dept' => 'CSE', 'qualification' => 'Ph.D. in Software Engineering', 'specialization' => 'Software Architecture, Cloud Computing'],
            ['email' => 'mahmud@cse.kuet.ac.bd', 'dept' => 'CSE', 'qualification' => 'Ph.D. in Computer Networks', 'specialization' => 'Network Security, IoT'],
            ['email' => 'dip@cse.kuet.ac.bd', 'dept' => 'CSE', 'qualification' => 'Ph.D. in Data Science', 'specialization' => 'Data Mining, Big Data Analytics'],
            ['email' => 'nafees@cse.kuet.ac.bd', 'dept' => 'CSE', 'qualification' => 'Ph.D. in Computer Vision', 'specialization' => 'Image Processing, Deep Learning'],
            
            // EEE Department
            ['email' => 'shahid@eee.kuet.ac.bd', 'dept' => 'EEE', 'qualification' => 'Ph.D. in Power Systems', 'specialization' => 'Renewable Energy, Smart Grid'],
            ['email' => 'qmr@eee.kuet.ac.bd', 'dept' => 'EEE', 'qualification' => 'Ph.D. in Electronics', 'specialization' => 'VLSI Design, Embedded Systems'],
            ['email' => 'alim@eee.kuet.ac.bd', 'dept' => 'EEE', 'qualification' => 'Ph.D. in Control Systems', 'specialization' => 'Automation, Robotics'],
            ['email' => 'prangon@eee.kuet.ac.bd', 'dept' => 'EEE', 'qualification' => 'Ph.D. in Communication Engineering', 'specialization' => 'Wireless Communication, 5G'],
            
            // ME Department
            ['email' => 'mahbub@me.kuet.ac.bd', 'dept' => 'ME', 'qualification' => 'Ph.D. in Thermal Engineering', 'specialization' => 'Heat Transfer, Energy Systems'],
            ['email' => 'mali@me.kuet.ac.bd', 'dept' => 'ME', 'qualification' => 'Ph.D. in Manufacturing', 'specialization' => 'CAD/CAM, Production Engineering'],
            ['email' => 'arafat@me.kuet.ac.bd', 'dept' => 'ME', 'qualification' => 'Ph.D. in Mechanical Design', 'specialization' => 'Finite Element Analysis, Materials'],
            
            // CE Department
            ['email' => 'mizan@ce.kuet.ac.bd', 'dept' => 'CE', 'qualification' => 'Ph.D. in Structural Engineering', 'specialization' => 'Earthquake Engineering, Concrete Structures'],
            ['email' => 'ansary@ce.kuet.ac.bd', 'dept' => 'CE', 'qualification' => 'Ph.D. in Geotechnical Engineering', 'specialization' => 'Foundation Engineering, Soil Mechanics'],
            ['email' => 'tanvir@ce.kuet.ac.bd', 'dept' => 'CE', 'qualification' => 'Ph.D. in Transportation', 'specialization' => 'Traffic Engineering, Highway Design'],
            
            // ECE Department
            ['email' => 'rafiq@ece.kuet.ac.bd', 'dept' => 'ECE', 'qualification' => 'Ph.D. in Electronics', 'specialization' => 'Microelectronics, Nanotechnology'],
            ['email' => 'enayet@ece.kuet.ac.bd', 'dept' => 'ECE', 'qualification' => 'Ph.D. in Signal Processing', 'specialization' => 'Digital Signal Processing, Communications'],
            
            // IEM Department
            ['email' => 'ahsan@iem.kuet.ac.bd', 'dept' => 'IEM', 'qualification' => 'Ph.D. in Industrial Engineering', 'specialization' => 'Operations Research, Supply Chain'],
            ['email' => 'mashud@iem.kuet.ac.bd', 'dept' => 'IEM', 'qualification' => 'Ph.D. in Management Science', 'specialization' => 'Quality Management, Six Sigma'],
            
            // Basic Science
            ['email' => 'salam@chem.kuet.ac.bd', 'dept' => 'CHEM', 'qualification' => 'Ph.D. in Chemistry', 'specialization' => 'Organic Chemistry, Materials Science'],
            ['email' => 'shafiq@math.kuet.ac.bd', 'dept' => 'MATH', 'qualification' => 'Ph.D. in Mathematics', 'specialization' => 'Applied Mathematics, Numerical Analysis'],
            ['email' => 'gafur@phy.kuet.ac.bd', 'dept' => 'PHY', 'qualification' => 'Ph.D. in Physics', 'specialization' => 'Condensed Matter Physics, Materials Science'],
        ];

        foreach ($teacherData as $index => $data) {
            $user = User::where('email', $data['email'])->first();
            if (!$user) continue;
            
            $department = Department::where('code', $data['dept'])->first();
            if (!$department) continue;
            
            Teacher::create([
                'user_id' => $user->id,
                'department_id' => $department->id,
                'employee_id' => 'KUET-' . $data['dept'] . '-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'qualification' => $data['qualification'],
                'specialization' => $data['specialization'],
                'salary' => rand(80000, 150000),
                'joining_date' => date('Y-m-d', strtotime('-' . rand(5, 20) . ' years')),
                'employment_type' => 'full_time',
                'bio' => $user->name . ' is a faculty member at the Department of ' . $department->name . ', KUET. Specializes in ' . $data['specialization'] . '.',
                'is_active' => true,
            ]);
        }
    }
}
