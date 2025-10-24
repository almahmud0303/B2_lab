<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    public function index()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get FAQ categories
        $faqCategories = [
            'general' => [
                'title' => 'General Questions',
                'faqs' => [
                    [
                        'question' => 'How do I reset my password?',
                        'answer' => 'Click on "Forgot Password" on the login page and follow the instructions sent to your email.'
                    ],
                    [
                        'question' => 'How do I update my profile information?',
                        'answer' => 'Go to Settings > Profile and update your information. Some changes may require admin approval.'
                    ],
                    [
                        'question' => 'How do I contact my teachers?',
                        'answer' => 'Use the Communication Center to send messages to your teachers and staff members.'
                    ],
                ]
            ],
            'academic' => [
                'title' => 'Academic Questions',
                'faqs' => [
                    [
                        'question' => 'How do I enroll in courses?',
                        'answer' => 'Go to Courses > Available Courses and click "Enroll" on the courses you want to take.'
                    ],
                    [
                        'question' => 'How do I view my exam results?',
                        'answer' => 'Go to Exams > Results to view all your published exam results.'
                    ],
                    [
                        'question' => 'How do I access course materials?',
                        'answer' => 'Go to Courses > My Courses and click on a course to access its materials and assignments.'
                    ],
                ]
            ],
            'financial' => [
                'title' => 'Financial Questions',
                'faqs' => [
                    [
                        'question' => 'How do I pay my fees?',
                        'answer' => 'Go to Fees and click on a pending fee to proceed with payment.'
                    ],
                    [
                        'question' => 'How do I view my payment history?',
                        'answer' => 'Go to Fees > Payment History to view all your past payments.'
                    ],
                    [
                        'question' => 'What payment methods are accepted?',
                        'answer' => 'We accept credit cards, debit cards, and bank transfers.'
                    ],
                ]
            ],
            'technical' => [
                'title' => 'Technical Support',
                'faqs' => [
                    [
                        'question' => 'I cannot log in to my account',
                        'answer' => 'Check your email and password. If the problem persists, contact technical support.'
                    ],
                    [
                        'question' => 'The website is loading slowly',
                        'answer' => 'Try refreshing the page or clearing your browser cache. Contact support if the issue continues.'
                    ],
                    [
                        'question' => 'I cannot upload files',
                        'answer' => 'Make sure your file size is under 2MB and in an accepted format (PDF, DOC, DOCX, JPG, PNG).'
                    ],
                ]
            ],
        ];

        // Get contact information
        $contactInfo = [
            'email' => 'support@ums.com',
            'phone' => '+1 (555) 123-4567',
            'hours' => 'Monday - Friday: 9:00 AM - 5:00 PM',
            'address' => '123 University Street, City, State 12345',
        ];

        return view('student.support.index', compact('faqCategories', 'contactInfo'));
    }

    public function submitTicket(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|in:technical,academic,financial,general',
            'priority' => 'required|in:low,medium,high,urgent',
            'description' => 'required|string|max:2000',
            'attachments' => 'nullable|array|max:3',
            'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        try {
            // In a real application, you would save this to a support_tickets table
            // For now, we'll simulate creating a ticket
            $ticketId = 'SUP-' . now()->format('Ymd') . '-' . rand(1000, 9999);

            // Send email notification (placeholder)
            // Mail::to('support@ums.com')->send(new SupportTicketCreated($student, $request->all(), $ticketId));

            return back()->with('success', "Support ticket created successfully! Your ticket ID is: {$ticketId}. We will respond within 24 hours.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit support ticket. Please try again or contact us directly.');
        }
    }

    public function knowledgeBase()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get knowledge base articles
        $articles = [
            [
                'id' => 1,
                'title' => 'Getting Started with UMS',
                'category' => 'Getting Started',
                'content' => 'Welcome to the University Management System! This guide will help you get started...',
                'updated_at' => now()->subDays(1),
                'views' => 150,
            ],
            [
                'id' => 2,
                'title' => 'How to Enroll in Courses',
                'category' => 'Academic',
                'content' => 'Learn how to browse and enroll in courses available for your program...',
                'updated_at' => now()->subDays(3),
                'views' => 89,
            ],
            [
                'id' => 3,
                'title' => 'Understanding Your Academic Calendar',
                'category' => 'Academic',
                'content' => 'Your academic calendar contains important dates and events...',
                'updated_at' => now()->subDays(5),
                'views' => 67,
            ],
            [
                'id' => 4,
                'title' => 'Payment Methods and Process',
                'category' => 'Financial',
                'content' => 'Learn about the different payment methods available and how to pay your fees...',
                'updated_at' => now()->subDays(7),
                'views' => 134,
            ],
            [
                'id' => 5,
                'title' => 'Library Services Guide',
                'category' => 'Library',
                'content' => 'Everything you need to know about using the library services...',
                'updated_at' => now()->subDays(10),
                'views' => 92,
            ],
        ];

        $categories = array_unique(array_column($articles, 'category'));

        return view('student.support.knowledge-base', compact('articles', 'categories'));
    }

    public function showArticle($id)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        // Get article by ID (in a real app, this would come from database)
        $articles = [
            1 => [
                'id' => 1,
                'title' => 'Getting Started with UMS',
                'category' => 'Getting Started',
                'content' => 'Welcome to the University Management System! This comprehensive guide will help you get started with all the features available to students.

## Dashboard Overview
Your dashboard is the central hub where you can access all your academic information, including:
- Current courses and enrollments
- Upcoming exams and deadlines
- Fee payment status
- Library book status
- Recent notices and announcements

## Navigation
Use the sidebar navigation to access different sections:
- **Profile**: Manage your personal information
- **Courses**: View and enroll in courses
- **Exams**: Check exam schedules and results
- **Fees**: View and pay your fees
- **Library**: Access library services
- **Reports**: Download academic reports
- **Settings**: Manage your account settings

## Getting Help
If you need assistance, you can:
1. Check the FAQ section
2. Browse the Knowledge Base
3. Submit a support ticket
4. Contact us directly

We hope you have a great experience using our system!',
                'updated_at' => now()->subDays(1),
                'views' => 150,
            ],
            // Add more articles as needed
        ];

        $article = $articles[$id] ?? null;
        
        if (!$article) {
            abort(404, 'Article not found');
        }

        return view('student.support.article', compact('article'));
    }

    public function contact()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $contactInfo = [
            'email' => 'support@ums.com',
            'phone' => '+1 (555) 123-4567',
            'hours' => 'Monday - Friday: 9:00 AM - 5:00 PM',
            'address' => '123 University Street, City, State 12345',
            'social' => [
                'facebook' => 'https://facebook.com/ums',
                'twitter' => 'https://twitter.com/ums',
                'linkedin' => 'https://linkedin.com/company/ums',
            ]
        ];

        return view('student.support.contact', compact('contactInfo'));
    }

    public function submitContact(Request $request)
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            // In a real application, you would save this to a contact_messages table
            // and send email notifications
            
            return back()->with('success', 'Your message has been sent successfully! We will get back to you within 24 hours.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send message. Please try again or contact us directly.');
        }
    }

    public function tutorials()
    {
        $student = Auth::user()->student;
        
        if (!$student) {
            abort(404, 'Student profile not found');
        }

        $tutorials = [
            [
                'id' => 1,
                'title' => 'How to Navigate the Dashboard',
                'description' => 'Learn the basics of navigating your student dashboard',
                'duration' => '5 minutes',
                'video_url' => '#',
                'category' => 'Getting Started',
            ],
            [
                'id' => 2,
                'title' => 'Course Enrollment Process',
                'description' => 'Step-by-step guide to enrolling in courses',
                'duration' => '8 minutes',
                'video_url' => '#',
                'category' => 'Academic',
            ],
            [
                'id' => 3,
                'title' => 'Making Fee Payments',
                'description' => 'How to pay your fees online',
                'duration' => '6 minutes',
                'video_url' => '#',
                'category' => 'Financial',
            ],
            [
                'id' => 4,
                'title' => 'Accessing Library Resources',
                'description' => 'How to search and borrow books from the library',
                'duration' => '7 minutes',
                'video_url' => '#',
                'category' => 'Library',
            ],
        ];

        $categories = array_unique(array_column($tutorials, 'category'));

        return view('student.support.tutorials', compact('tutorials', 'categories'));
    }
}
