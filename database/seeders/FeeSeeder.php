<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Fee;
use Illuminate\Database\Seeder;

class FeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if fees already exist
        if (Fee::count() > 0) {
            $this->command->info('Fees already exist. Skipping...');
            return;
        }
        
        $students = Student::where('status', 'active')->get();
        
        $feeTypes = [
            ['type' => 'Tuition Fee', 'amount' => 25000],
            ['type' => 'Library Fee', 'amount' => 1500],
            ['type' => 'Laboratory Fee', 'amount' => 3000],
            ['type' => 'Sports Fee', 'amount' => 500],
            ['type' => 'Development Fee', 'amount' => 2000],
            ['type' => 'Computer Lab Fee', 'amount' => 2500],
            ['type' => 'Semester Fee', 'amount' => 5000],
            ['type' => 'Examination Fee', 'amount' => 1000],
        ];
        
        foreach ($students as $student) {
            // Generate fees for current semester
            foreach ($feeTypes as $feeType) {
                // Some fees are mandatory, some are optional
                if (in_array($feeType['type'], ['Tuition Fee', 'Semester Fee', 'Examination Fee']) || rand(1, 10) <= 7) {
                    
                    // Determine payment status
                    $statusRand = rand(1, 10);
                    if ($statusRand <= 6) {
                        // 60% fully paid
                        $status = 'paid';
                        $paidAmount = $feeType['amount'];
                        $paidDate = now()->subDays(rand(5, 30));
                    } elseif ($statusRand <= 8) {
                        // 20% partially paid
                        $status = 'partial';
                        $paidAmount = $feeType['amount'] * rand(30, 70) / 100;
                        $paidDate = now()->subDays(rand(5, 20));
                    } else {
                        // 20% pending or overdue
                        $dueDate = now()->subDays(rand(-30, 15)); // Some overdue, some upcoming
                        if ($dueDate < now()) {
                            $status = 'overdue';
                        } else {
                            $status = 'pending';
                        }
                        $paidAmount = 0;
                        $paidDate = null;
                    }
                    
                    $dueDate = now()->addDays(rand(-15, 30));
                    
                    $notes = '';
                    if ($status === 'paid') {
                        $notes = 'Payment received. Transaction ID: TXN' . rand(100000, 999999);
                    } elseif ($status === 'partial') {
                        $notes = 'Partial payment received. Remaining: BDT ' . ($feeType['amount'] - $paidAmount);
                    } elseif ($status === 'overdue') {
                        $notes = 'Payment overdue. Please clear dues immediately.';
                    }
                    
                    Fee::create([
                        'student_id' => $student->id,
                        'fee_type' => $feeType['type'],
                        'amount' => $feeType['amount'],
                        'paid_amount' => $paidAmount,
                        'due_date' => $dueDate,
                        'paid_date' => $paidDate,
                        'status' => $status,
                        'notes' => $notes,
                    ]);
                }
            }
            
            // Add some previous semester fees (all paid)
            if (rand(1, 10) <= 7) {
                $previousSemesterFees = ['Tuition Fee', 'Semester Fee', 'Library Fee'];
                foreach ($previousSemesterFees as $feeTypeName) {
                    $feeType = collect($feeTypes)->firstWhere('type', $feeTypeName);
                    if ($feeType) {
                        Fee::create([
                            'student_id' => $student->id,
                            'fee_type' => $feeType['type'] . ' (Previous Semester)',
                            'amount' => $feeType['amount'],
                            'paid_amount' => $feeType['amount'],
                            'due_date' => now()->subMonths(6),
                            'paid_date' => now()->subMonths(5)->subDays(rand(1, 15)),
                            'status' => 'paid',
                            'notes' => 'Payment received. Transaction ID: TXN' . rand(100000, 999999),
                        ]);
                    }
                }
            }
        }
    }
}

