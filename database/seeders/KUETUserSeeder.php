<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class KUETUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if users already exist (excluding default users)
        if (User::count() > 0) {
            $this->command->info('Users already exist. Skipping...');
            return;
        }
        
        // Hash password once for all users (performance optimization)
        $hashedPassword = Hash::make('password');
        
        // Admin Users
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@kuet.ac.bd',
            'password' => $hashedPassword,
            'role' => 'admin',
            'phone' => '+880-41-769468',
            'address' => 'KUET Administrative Building, Khulna-9203',
            'date_of_birth' => '1975-01-15',
            'gender' => 'male',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Dr. Mihir Ranjan Halder',
            'email' => 'vice.chancellor@kuet.ac.bd',
            'password' => $hashedPassword,
            'role' => 'admin',
            'phone' => '+880-41-769001',
            'address' => 'Vice Chancellor Office, KUET, Khulna-9203',
            'date_of_birth' => '1968-03-20',
            'gender' => 'male',
            'is_active' => true,
        ]);

        // Teacher Users (Faculty Members)
        $teachers = [
            // CSE Department
            ['name' => 'Prof. Dr. Md. Abdul Aziz', 'email' => 'aziz@cse.kuet.ac.bd', 'dept' => 'CSE', 'gender' => 'male'],
            ['name' => 'Prof. Dr. K. M. Azharul Hasan', 'email' => 'azhasan@cse.kuet.ac.bd', 'dept' => 'CSE', 'gender' => 'male'],
            ['name' => 'Dr. Mahmudul Hasan', 'email' => 'mahmud@cse.kuet.ac.bd', 'dept' => 'CSE', 'gender' => 'male'],
            ['name' => 'Dr. Dip Nandi', 'email' => 'dip@cse.kuet.ac.bd', 'dept' => 'CSE', 'gender' => 'male'],
            ['name' => 'Dr. Nafees Mansoor', 'email' => 'nafees@cse.kuet.ac.bd', 'dept' => 'CSE', 'gender' => 'male'],
            
            // EEE Department
            ['name' => 'Prof. Dr. Md. Shahidul Islam', 'email' => 'shahid@eee.kuet.ac.bd', 'dept' => 'EEE', 'gender' => 'male'],
            ['name' => 'Prof. Dr. Quazi Mehbubar Rahman', 'email' => 'qmr@eee.kuet.ac.bd', 'dept' => 'EEE', 'gender' => 'male'],
            ['name' => 'Dr. Mohammad Abdul Alim', 'email' => 'alim@eee.kuet.ac.bd', 'dept' => 'EEE', 'gender' => 'male'],
            ['name' => 'Dr. Prangon Das', 'email' => 'prangon@eee.kuet.ac.bd', 'dept' => 'EEE', 'gender' => 'male'],
            
            // ME Department
            ['name' => 'Prof. Dr. Md. Mahbubur Rahman', 'email' => 'mahbub@me.kuet.ac.bd', 'dept' => 'ME', 'gender' => 'male'],
            ['name' => 'Prof. Dr. Mohammad Ali', 'email' => 'mali@me.kuet.ac.bd', 'dept' => 'ME', 'gender' => 'male'],
            ['name' => 'Dr.Md. Arafat Hossain', 'email' => 'arafat@me.kuet.ac.bd', 'dept' => 'ME', 'gender' => 'male'],
            
            // CE Department
            ['name' => 'Prof. Dr. Md. Mizanur Rahman', 'email' => 'mizan@ce.kuet.ac.bd', 'dept' => 'CE', 'gender' => 'male'],
            ['name' => 'Prof. Dr. Mehedi Ahmed Ansary', 'email' => 'ansary@ce.kuet.ac.bd', 'dept' => 'CE', 'gender' => 'male'],
            ['name' => 'Dr. Tanvir Ahmed', 'email' => 'tanvir@ce.kuet.ac.bd', 'dept' => 'CE', 'gender' => 'male'],
            
            // ECE Department
            ['name' => 'Prof. Dr. Md. Rafiqul Islam', 'email' => 'rafiq@ece.kuet.ac.bd', 'dept' => 'ECE', 'gender' => 'male'],
            ['name' => 'Dr. Shaikh Enayet Ullah', 'email' => 'enayet@ece.kuet.ac.bd', 'dept' => 'ECE', 'gender' => 'male'],
            
            // IEM Department
            ['name' => 'Prof. Dr. Md. Ahsan Habib', 'email' => 'ahsan@iem.kuet.ac.bd', 'dept' => 'IEM', 'gender' => 'male'],
            ['name' => 'Dr. Abu Hashan Md. Mashud', 'email' => 'mashud@iem.kuet.ac.bd', 'dept' => 'IEM', 'gender' => 'male'],
            
            // Basic Science Departments
            ['name' => 'Prof. Dr. Md. Abdus Salam', 'email' => 'salam@chem.kuet.ac.bd', 'dept' => 'CHEM', 'gender' => 'male'],
            ['name' => 'Prof. Dr. Md. Shafiqul Islam', 'email' => 'shafiq@math.kuet.ac.bd', 'dept' => 'MATH', 'gender' => 'male'],
            ['name' => 'Prof. Dr. Md. Abdul Gafur', 'email' => 'gafur@phy.kuet.ac.bd', 'dept' => 'PHY', 'gender' => 'male'],
        ];

        foreach ($teachers as $index => $teacher) {
            User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'password' => $hashedPassword,
                'role' => 'teacher',
                'phone' => '+880-41-7696' . str_pad(100 + $index, 2, '0', STR_PAD_LEFT),
                'address' => 'KUET Campus, Khulna-9203, Bangladesh',
                'date_of_birth' => date('Y-m-d', strtotime('-' . rand(35, 55) . ' years')),
                'gender' => $teacher['gender'],
                'is_active' => true,
            ]);
        }

        // Staff Users
        $staffMembers = [
            ['name' => 'Md. Kamal Hossain', 'designation' => 'Librarian'],
            ['name' => 'Mrs. Fatema Begum', 'designation' => 'Assistant Librarian'],
            ['name' => 'Md. Rafiqul Islam', 'designation' => 'Library Assistant'],
            ['name' => 'Md. Shahidul Islam', 'designation' => 'Administrative Officer'],
            ['name' => 'Mrs. Nasrin Akter', 'designation' => 'Accounts Officer'],
            ['name' => 'Md. Habibur Rahman', 'designation' => 'Registrar Assistant'],
            ['name' => 'Md. Jahangir Alam', 'designation' => 'IT Support Staff'],
            ['name' => 'Mrs. Sultana Razia', 'designation' => 'Office Assistant'],
        ];

        foreach ($staffMembers as $index => $staff) {
            User::create([
                'name' => $staff['name'],
                'email' => strtolower(str_replace([' ', '.'], ['', ''], explode(' ', $staff['name'])[1])) . '@staff.kuet.ac.bd',
                'password' => $hashedPassword,
                'role' => 'staff',
                'phone' => '+880-41-7697' . str_pad(100 + $index, 2, '0', STR_PAD_LEFT),
                'address' => 'KUET Campus, Khulna-9203, Bangladesh',
                'date_of_birth' => date('Y-m-d', strtotime('-' . rand(30, 50) . ' years')),
                'gender' => strpos($staff['name'], 'Mrs.') !== false ? 'female' : 'male',
                'is_active' => true,
            ]);
        }

        // Student Users
        $studentNames = [
            // Male students
            ['name' => 'Md. Tahmid Rahman', 'gender' => 'male'],
            ['name' => 'Md. Sakib Hasan', 'gender' => 'male'],
            ['name' => 'Md. Rafat Ahmed', 'gender' => 'male'],
            ['name' => 'Md. Fahim Shahriar', 'gender' => 'male'],
            ['name' => 'Md. Ashraful Islam', 'gender' => 'male'],
            ['name' => 'Md. Labib Hossain', 'gender' => 'male'],
            ['name' => 'Md. Sabbir Rahman', 'gender' => 'male'],
            ['name' => 'Md. Tanvir Ahmed', 'gender' => 'male'],
            ['name' => 'Md. Farhan Ishraq', 'gender' => 'male'],
            ['name' => 'Md. Mushfiqur Rahman', 'gender' => 'male'],
            ['name' => 'Md. Nahian Chowdhury', 'gender' => 'male'],
            ['name' => 'Md. Samin Yasar', 'gender' => 'male'],
            ['name' => 'Md. Redwan Haque', 'gender' => 'male'],
            ['name' => 'Md. Ahnaf Tahmid', 'gender' => 'male'],
            ['name' => 'Md. Shahriar Hasan', 'gender' => 'male'],
            
            // Female students
            ['name' => 'Fahmida Akter', 'gender' => 'female'],
            ['name' => 'Nusrat Jahan', 'gender' => 'female'],
            ['name' => 'Sabrina Islam', 'gender' => 'female'],
            ['name' => 'Tasnia Rahman', 'gender' => 'female'],
            ['name' => 'Mehjabin Alam', 'gender' => 'female'],
            ['name' => 'Anika Tahsin', 'gender' => 'female'],
            ['name' => 'Fariha Ahmed', 'gender' => 'female'],
            ['name' => 'Tahsina Haque', 'gender' => 'female'],
            ['name' => 'Nafisa Khan', 'gender' => 'female'],
            ['name' => 'Lamia Hassan', 'gender' => 'female'],
            ['name' => 'Sadia Afrin', 'gender' => 'female'],
            ['name' => 'Zarrin Tasnim', 'gender' => 'female'],
            ['name' => 'Humaira Noor', 'gender' => 'female'],
            ['name' => 'Raisa Tabassum', 'gender' => 'female'],
            ['name' => 'Bushra Anjum', 'gender' => 'female'],
            
            // Additional students
            ['name' => 'Alice Johnson', 'gender' => 'female'], // Existing student
            ['name' => 'Md. Ariful Islam', 'gender' => 'male'],
            ['name' => 'Md. Jisan Ahmed', 'gender' => 'male'],
            ['name' => 'Md. Rafi Uddin', 'gender' => 'male'],
            ['name' => 'Md. Shafin Mahmud', 'gender' => 'male'],
            ['name' => 'Md. Towhid Hasan', 'gender' => 'male'],
            ['name' => 'Md. Wasif Rahman', 'gender' => 'male'],
            ['name' => 'Ayesha Siddika', 'gender' => 'female'],
            ['name' => 'Bristy Akter', 'gender' => 'female'],
            ['name' => 'Jannatul Ferdous', 'gender' => 'female'],
        ];

        foreach ($studentNames as $index => $student) {
            $firstName = explode(' ', $student['name'])[count(explode(' ', $student['name'])) - 1];
            $email = strtolower($firstName) . '.' . (1900 + $index) . '@stud.kuet.ac.bd';
            
            // Skip if Alice Johnson already exists
            if ($student['name'] === 'Alice Johnson' && User::where('email', 'alice.johnson@ums.com')->exists()) {
                continue;
            }
            
            User::create([
                'name' => $student['name'],
                'email' => $email,
                'password' => $hashedPassword,
                'role' => 'student',
                'phone' => '+880-1' . rand(3, 9) . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT),
                'address' => 'Khulna, Bangladesh',
                'date_of_birth' => date('Y-m-d', strtotime('-' . rand(18, 24) . ' years')),
                'gender' => $student['gender'],
                'is_active' => true,
            ]);
        }
    }
}

