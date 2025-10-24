<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookIssueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if book issues already exist
        if (BookIssue::count() > 0) {
            $this->command->info('Book issues already exist. Skipping...');
            return;
        }
        
        $students = Student::where('status', 'active')->limit(30)->get();
        $books = Book::where('is_active', true)->get();
        $libraryStaff = User::where('role', 'staff')->first();
        
        if (!$libraryStaff) {
            echo "No library staff found. Skipping book issues.\n";
            return;
        }
        
        foreach ($students as $student) {
            // Each student has issued 1-3 books
            $numberOfBooks = rand(1, 3);
            
            for ($i = 0; $i < $numberOfBooks; $i++) {
                $book = $books->random();
                
                // Determine if book is returned or still issued
                $statusRand = rand(1, 10);
                
                if ($statusRand <= 6) {
                    // 60% returned on time
                    $issueDate = now()->subDays(rand(20, 60));
                    $dueDate = $issueDate->copy()->addDays(14);
                    $returnDate = $issueDate->copy()->addDays(rand(7, 14));
                    $status = 'returned';
                    $fineAmount = 0;
                } elseif ($statusRand <= 8) {
                    // 20% currently issued (not overdue)
                    $issueDate = now()->subDays(rand(1, 10));
                    $dueDate = $issueDate->copy()->addDays(14);
                    $returnDate = null;
                    $status = 'issued';
                    $fineAmount = 0;
                } else {
                    // 20% overdue
                    $issueDate = now()->subDays(rand(20, 40));
                    $dueDate = $issueDate->copy()->addDays(14);
                    $returnDate = null;
                    $status = 'overdue';
                    
                    // Calculate fine (10 BDT per day)
                    $overdueDays = now()->diffInDays($dueDate);
                    $fineAmount = $overdueDays * 10;
                }
                
                $notes = '';
                if ($status === 'returned') {
                    $notes = 'Book returned in good condition.';
                } elseif ($status === 'issued') {
                    $notes = 'Book issued. Please return by due date.';
                } else {
                    $notes = 'Book overdue. Fine applicable: BDT ' . $fineAmount;
                }
                
                BookIssue::create([
                    'student_id' => $student->id,
                    'book_id' => $book->id,
                    'staff_id' => $libraryStaff->id,
                    'issue_date' => $issueDate,
                    'due_date' => $dueDate,
                    'return_date' => $returnDate,
                    'fine_amount' => $fineAmount,
                    'status' => $status,
                    'notes' => $notes,
                ]);
            }
        }
    }
}

