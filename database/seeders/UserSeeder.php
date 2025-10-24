<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@ums.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '+1234567890',
            'date_of_birth' => '1985-01-01',
            'gender' => 'male',
            'address' => '123 Admin Street, City, State',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create some Staff Users
        User::create([
            'name' => 'Library Staff',
            'email' => 'library@ums.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'phone' => '+1234567891',
            'date_of_birth' => '1990-05-15',
            'gender' => 'female',
            'address' => '456 Library Avenue, City, State',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Hall Manager',
            'email' => 'hall@ums.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'phone' => '+1234567892',
            'date_of_birth' => '1988-03-20',
            'gender' => 'male',
            'address' => '789 Hall Road, City, State',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create Teacher Users
        $teachers = [
            ['name' => 'Dr. John Smith', 'email' => 'john.smith@ums.com', 'phone' => '+1234567893', 'gender' => 'male'],
            ['name' => 'Dr. Sarah Johnson', 'email' => 'sarah.johnson@ums.com', 'phone' => '+1234567894', 'gender' => 'female'],
            ['name' => 'Prof. Michael Brown', 'email' => 'michael.brown@ums.com', 'phone' => '+1234567895', 'gender' => 'male'],
            ['name' => 'Dr. Emily Davis', 'email' => 'emily.davis@ums.com', 'phone' => '+1234567896', 'gender' => 'female'],
            ['name' => 'Prof. David Wilson', 'email' => 'david.wilson@ums.com', 'phone' => '+1234567897', 'gender' => 'male'],
        ];

        foreach ($teachers as $teacher) {
            User::create([
                'name' => $teacher['name'],
                'email' => $teacher['email'],
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'phone' => $teacher['phone'],
                'date_of_birth' => fake()->date('1970-01-01', '1985-12-31'),
                'gender' => $teacher['gender'],
                'address' => fake()->address(),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Create Student Users
        $students = [
            ['name' => 'Alice Johnson', 'email' => 'alice.johnson@ums.com', 'phone' => '+1234567898', 'gender' => 'female'],
            ['name' => 'Bob Smith', 'email' => 'bob.smith@ums.com', 'phone' => '+1234567899', 'gender' => 'male'],
            ['name' => 'Carol Brown', 'email' => 'carol.brown@ums.com', 'phone' => '+1234567800', 'gender' => 'female'],
            ['name' => 'David Miller', 'email' => 'david.miller@ums.com', 'phone' => '+1234567801', 'gender' => 'male'],
            ['name' => 'Eva Wilson', 'email' => 'eva.wilson@ums.com', 'phone' => '+1234567802', 'gender' => 'female'],
            ['name' => 'Frank Garcia', 'email' => 'frank.garcia@ums.com', 'phone' => '+1234567803', 'gender' => 'male'],
            ['name' => 'Grace Lee', 'email' => 'grace.lee@ums.com', 'phone' => '+1234567804', 'gender' => 'female'],
            ['name' => 'Henry Taylor', 'email' => 'henry.taylor@ums.com', 'phone' => '+1234567805', 'gender' => 'male'],
        ];

        foreach ($students as $student) {
            User::create([
                'name' => $student['name'],
                'email' => $student['email'],
                'password' => Hash::make('password'),
                'role' => 'student',
                'phone' => $student['phone'],
                'date_of_birth' => fake()->date('1995-01-01', '2005-12-31'),
                'gender' => $student['gender'],
                'address' => fake()->address(),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }
    }
}