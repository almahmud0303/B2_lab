<?php

namespace Database\Seeders;

use App\Models\Notice;
use App\Models\User;
use Illuminate\Database\Seeder;

class NoticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if notices already exist
        if (Notice::count() > 0) {
            $this->command->info('Notices already exist. Skipping...');
            return;
        }
        
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            echo "No admin user found. Skipping notices.\n";
            return;
        }
        
        $notices = [
            // Academic Notices
            [
                'title' => 'Semester Final Examination Schedule - Fall 2024',
                'content' => 'The final examination for Fall 2024 semester will commence from December 15, 2024. Students are advised to check the detailed schedule on the notice board and departmental websites. Admit cards will be distributed from December 10, 2024. Students must bring their admit cards and student ID cards to the examination hall.',
                'type' => 'exam',
                'priority' => 'urgent',
                'target_roles' => json_encode(['student', 'teacher']),
                'publish_date' => now()->subDays(5),
                'expiry_date' => now()->addDays(30),
                'is_published' => true,
                'is_pinned' => true,
            ],
            [
                'title' => 'Class Routine for Spring 2025 Semester',
                'content' => 'The class routine for Spring 2025 semester has been published. Classes will start from January 2, 2025. Students can download the routine from the university website or collect from their respective departments. Any changes in the routine will be notified separately.',
                'type' => 'academic',
                'priority' => 'high',
                'target_roles' => json_encode(['student', 'teacher']),
                'publish_date' => now()->subDays(10),
                'expiry_date' => now()->addDays(60),
                'is_published' => true,
                'is_pinned' => true,
            ],
            [
                'title' => 'Mid-term Exam Result Published',
                'content' => 'The mid-term examination results for all departments have been published. Students can check their results through the online portal using their student ID and password. For any discrepancies, contact your respective course teachers within 7 days.',
                'type' => 'exam',
                'priority' => 'high',
                'target_roles' => json_encode(['student']),
                'publish_date' => now()->subDays(3),
                'expiry_date' => now()->addDays(15),
                'is_published' => true,
                'is_pinned' => false,
            ],
            
            // Fee Notices
            [
                'title' => 'Semester Fee Payment Deadline Extended',
                'content' => 'Due to technical issues with the online payment system, the deadline for semester fee payment has been extended to November 30, 2024. Students are requested to complete their payment before the deadline to avoid late fees. Payment can be made through bank deposits or online payment gateway.',
                'type' => 'fee',
                'priority' => 'urgent',
                'target_roles' => json_encode(['student']),
                'publish_date' => now()->subDays(7),
                'expiry_date' => now()->addDays(20),
                'is_published' => true,
                'is_pinned' => true,
            ],
            [
                'title' => 'Tuition Fee Structure for Academic Year 2024-25',
                'content' => 'The tuition fee structure for the academic year 2024-25 has been revised. The new fee structure is as follows: Undergraduate - BDT 25,000 per semester, Laboratory Fee - BDT 3,000, Library Fee - BDT 1,500. For detailed breakdown, please visit the accounts section.',
                'type' => 'fee',
                'priority' => 'medium',
                'target_roles' => json_encode(['student']),
                'publish_date' => now()->subDays(15),
                'expiry_date' => null,
                'is_published' => true,
                'is_pinned' => false,
            ],
            
            // Library Notices
            [
                'title' => 'Library Timings During Examination Period',
                'content' => 'The Central Library will remain open from 8:00 AM to 12:00 midnight during the examination period (December 15, 2024 to January 10, 2025). Students can utilize the extended hours for their studies. Please maintain silence in the reading rooms.',
                'type' => 'library',
                'priority' => 'medium',
                'target_roles' => json_encode(['student', 'teacher']),
                'publish_date' => now()->subDays(8),
                'expiry_date' => now()->addDays(45),
                'is_published' => true,
                'is_pinned' => false,
            ],
            [
                'title' => 'New Books Added to Library Collection',
                'content' => 'The library has acquired 500+ new books in various subjects including Computer Science, Electrical Engineering, Mechanical Engineering, and Civil Engineering. Students can check the complete list on the library website and reserve books online. Happy reading!',
                'type' => 'library',
                'priority' => 'low',
                'target_roles' => json_encode(['student', 'teacher']),
                'publish_date' => now()->subDays(12),
                'expiry_date' => now()->addDays(30),
                'is_published' => true,
                'is_pinned' => false,
            ],
            
            // Event Notices
            [
                'title' => 'Annual Cultural Festival "KUET Carnival 2024"',
                'content' => 'KUET Carnival 2024, the annual cultural festival, will be held on December 20-22, 2024 at the university campus. Students interested in participating in various competitions (singing, dancing, drama, debate, photography, etc.) should register by December 10, 2024. Visit the student affairs office for registration.',
                'type' => 'event',
                'priority' => 'high',
                'target_roles' => json_encode(['student', 'teacher', 'staff']),
                'publish_date' => now()->subDays(6),
                'expiry_date' => now()->addDays(25),
                'is_published' => true,
                'is_pinned' => true,
            ],
            [
                'title' => 'International Conference on Engineering and Technology',
                'content' => 'KUET is organizing an International Conference on Engineering and Technology (ICET 2025) on February 15-17, 2025. Faculty members and researchers are invited to submit their papers by January 15, 2025. Registration is now open at www.icet.kuet.ac.bd',
                'type' => 'event',
                'priority' => 'medium',
                'target_roles' => json_encode(['teacher']),
                'publish_date' => now()->subDays(20),
                'expiry_date' => now()->addDays(70),
                'is_published' => true,
                'is_pinned' => false,
            ],
            
            // General Notices
            [
                'title' => 'Campus Internet Maintenance Notice',
                'content' => 'The campus-wide internet service will undergo maintenance on November 25, 2024, from 2:00 AM to 6:00 AM. Internet services may be temporarily unavailable during this period. We apologize for any inconvenience caused.',
                'type' => 'general',
                'priority' => 'medium',
                'target_roles' => json_encode(['student', 'teacher', 'staff']),
                'publish_date' => now()->subDays(4),
                'expiry_date' => now()->addDays(5),
                'is_published' => true,
                'is_pinned' => false,
            ],
            [
                'title' => 'COVID-19 Vaccination Drive on Campus',
                'content' => 'A COVID-19 vaccination drive will be organized on campus on November 28-29, 2024, in collaboration with Khulna City Corporation. All students, faculty, and staff who haven\'t completed their vaccination are encouraged to participate. The vaccination camp will be set up at the Central Auditorium from 10:00 AM to 4:00 PM.',
                'type' => 'general',
                'priority' => 'high',
                'target_roles' => json_encode(['student', 'teacher', 'staff']),
                'publish_date' => now()->subDays(9),
                'expiry_date' => now()->addDays(10),
                'is_published' => true,
                'is_pinned' => false,
            ],
            [
                'title' => 'Student ID Card Renewal Process',
                'content' => 'Students who have lost or damaged their ID cards can apply for renewal at the Registrar\'s Office. Required documents: Application form, 2 passport-size photos, and fee payment receipt (BDT 200). Processing time: 7 working days.',
                'type' => 'general',
                'priority' => 'low',
                'target_roles' => json_encode(['student']),
                'publish_date' => now()->subDays(25),
                'expiry_date' => null,
                'is_published' => true,
                'is_pinned' => false,
            ],
            [
                'title' => 'Convocation Ceremony 2024 Announcement',
                'content' => 'The 15th Convocation Ceremony of KUET will be held on March 15, 2025. Graduating students from 2022-2024 batches are eligible to participate. Registration for convocation will start from January 1, 2025. Detailed guidelines will be published soon.',
                'type' => 'event',
                'priority' => 'urgent',
                'target_roles' => json_encode(['student', 'teacher', 'staff']),
                'publish_date' => now()->subDays(2),
                'expiry_date' => now()->addDays(120),
                'is_published' => true,
                'is_pinned' => true,
            ],
            [
                'title' => 'Career Counseling and Job Fair 2024',
                'content' => 'The Career Development Center is organizing a Career Counseling and Job Fair on December 5-6, 2024. Leading companies from various sectors will participate. Final year students are encouraged to attend with updated resumes. Pre-registration is required.',
                'type' => 'event',
                'priority' => 'high',
                'target_roles' => json_encode(['student']),
                'publish_date' => now()->subDays(11),
                'expiry_date' => now()->addDays(15),
                'is_published' => true,
                'is_pinned' => false,
            ],
        ];

        foreach ($notices as $notice) {
            Notice::create([
                'user_id' => $admin->id,
                'title' => $notice['title'],
                'content' => $notice['content'],
                'type' => $notice['type'],
                'priority' => $notice['priority'],
                'target_roles' => $notice['target_roles'],
                'publish_date' => $notice['publish_date'],
                'expiry_date' => $notice['expiry_date'],
                'is_published' => $notice['is_published'],
                'is_pinned' => $notice['is_pinned'],
            ]);
        }
    }
}

