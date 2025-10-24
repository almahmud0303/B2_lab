<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if books already exist
        if (Book::count() > 0) {
            $this->command->info('Books already exist. Skipping...');
            return;
        }
        
        $books = [
            // Computer Science
            ['isbn' => '9780262033848', 'title' => 'Introduction to Algorithms', 'author' => 'Thomas H. Cormen', 'publisher' => 'MIT Press', 'year' => 2009, 'category' => 'Computer Science', 'copies' => 15, 'price' => 5500],
            ['isbn' => '9780132350884', 'title' => 'Clean Code', 'author' => 'Robert C. Martin', 'publisher' => 'Prentice Hall', 'year' => 2008, 'category' => 'Computer Science', 'copies' => 10, 'price' => 3800],
            ['isbn' => '9780201633610', 'title' => 'Design Patterns', 'author' => 'Gang of Four', 'publisher' => 'Addison-Wesley', 'year' => 1994, 'category' => 'Computer Science', 'copies' => 12, 'price' => 4200],
            ['isbn' => '9780137081073', 'title' => 'The Pragmatic Programmer', 'author' => 'Andrew Hunt', 'publisher' => 'Addison-Wesley', 'year' => 2019, 'category' => 'Computer Science', 'copies' => 8, 'price' => 3600],
            ['isbn' => '9781449355739', 'title' => 'Designing Data-Intensive Applications', 'author' => 'Martin Kleppmann', 'publisher' => "O'Reilly", 'year' => 2017, 'category' => 'Computer Science', 'copies' => 10, 'price' => 4500],
            ['isbn' => '9780134685991', 'title' => 'Effective Java', 'author' => 'Joshua Bloch', 'publisher' => 'Addison-Wesley', 'year' => 2018, 'category' => 'Computer Science', 'copies' => 7, 'price' => 4000],
            ['isbn' => '9780596007126', 'title' => 'Head First Design Patterns', 'author' => 'Eric Freeman', 'publisher' => "O'Reilly", 'year' => 2004, 'category' => 'Computer Science', 'copies' => 9, 'price' => 3200],
            ['isbn' => '9780136291558', 'title' => 'Object Oriented Software Engineering', 'author' => 'Ivar Jacobson', 'publisher' => 'Addison-Wesley', 'year' => 1992, 'category' => 'Computer Science', 'copies' => 11, 'price' => 3500],
            
            // Electrical Engineering
            ['isbn' => '9780073529592', 'title' => 'Fundamentals of Electric Circuits', 'author' => 'Charles K. Alexander', 'publisher' => 'McGraw-Hill', 'year' => 2016, 'category' => 'Electrical Engineering', 'copies' => 20, 'price' => 4800],
            ['isbn' => '9780133760033', 'title' => 'Microelectronic Circuits', 'author' => 'Adel S. Sedra', 'publisher' => 'Oxford', 'year' => 2014, 'category' => 'Electrical Engineering', 'copies' => 15, 'price' => 5200],
            ['isbn' => '9780073380674', 'title' => 'Power System Analysis', 'author' => 'John Grainger', 'publisher' => 'McGraw-Hill', 'year' => 2013, 'category' => 'Electrical Engineering', 'copies' => 12, 'price' => 4600],
            ['isbn' => '9780134484143', 'title' => 'Control Systems Engineering', 'author' => 'Norman S. Nise', 'publisher' => 'Wiley', 'year' => 2019, 'category' => 'Electrical Engineering', 'copies' => 14, 'price' => 5000],
            
            // Mechanical Engineering
            ['isbn' => '9780073398174', 'title' => 'Engineering Mechanics: Statics', 'author' => 'J.L. Meriam', 'publisher' => 'Wiley', 'year' => 2016, 'category' => 'Mechanical Engineering', 'copies' => 18, 'price' => 4400],
            ['isbn' => '9780073398198', 'title' => 'Engineering Mechanics: Dynamics', 'author' => 'J.L. Meriam', 'publisher' => 'Wiley', 'year' => 2016, 'category' => 'Mechanical Engineering', 'copies' => 18, 'price' => 4400],
            ['isbn' => '9780073398136', 'title' => 'Thermodynamics: An Engineering Approach', 'author' => 'Yunus A. Cengel', 'publisher' => 'McGraw-Hill', 'year' => 2018, 'category' => 'Mechanical Engineering', 'copies' => 16, 'price' => 4700],
            ['isbn' => '9780134870137', 'title' => 'Fluid Mechanics', 'author' => 'Frank M. White', 'publisher' => 'McGraw-Hill', 'year' => 2015, 'category' => 'Mechanical Engineering', 'copies' => 15, 'price' => 4500],
            ['isbn' => '9780073398235', 'title' => 'Heat and Mass Transfer', 'author' => 'Yunus A. Cengel', 'publisher' => 'McGraw-Hill', 'year' => 2019, 'category' => 'Mechanical Engineering', 'copies' => 14, 'price' => 4800],
            
            // Civil Engineering
            ['isbn' => '9780134484204', 'title' => 'Structural Analysis', 'author' => 'R.C. Hibbeler', 'publisher' => 'Pearson', 'year' => 2017, 'category' => 'Civil Engineering', 'copies' => 17, 'price' => 4900],
            ['isbn' => '9780134814698', 'title' => 'Soil Mechanics and Foundations', 'author' => 'Muni Budhu', 'publisher' => 'Wiley', 'year' => 2015, 'category' => 'Civil Engineering', 'copies' => 13, 'price' => 4600],
            ['isbn' => '9780133826326', 'title' => 'Transportation Engineering', 'author' => 'C. Jotin Khisty', 'publisher' => 'Pearson', 'year' => 2013, 'category' => 'Civil Engineering', 'copies' => 11, 'price' => 4300],
            ['isbn' => '9780073397870', 'title' => 'Construction Management', 'author' => 'Daniel W. Halpin', 'publisher' => 'Wiley', 'year' => 2016, 'category' => 'Civil Engineering', 'copies' => 10, 'price' => 4100],
            
            // Mathematics
            ['isbn' => '9780134689517', 'title' => 'Calculus: Early Transcendentals', 'author' => 'James Stewart', 'publisher' => 'Cengage', 'year' => 2015, 'category' => 'Mathematics', 'copies' => 25, 'price' => 3800],
            ['isbn' => '9780134685991', 'title' => 'Linear Algebra and Its Applications', 'author' => 'David C. Lay', 'publisher' => 'Pearson', 'year' => 2015, 'category' => 'Mathematics', 'copies' => 20, 'price' => 3600],
            ['isbn' => '9780134689494', 'title' => 'Differential Equations', 'author' => 'Dennis G. Zill', 'publisher' => 'Cengage', 'year' => 2016, 'category' => 'Mathematics', 'copies' => 18, 'price' => 3700],
            ['isbn' => '9780134689623', 'title' => 'Probability and Statistics', 'author' => 'Morris H. DeGroot', 'publisher' => 'Pearson', 'year' => 2014, 'category' => 'Mathematics', 'copies' => 16, 'price' => 3500],
            
            // Physics
            ['isbn' => '9780134988559', 'title' => 'University Physics', 'author' => 'Hugh D. Young', 'publisher' => 'Pearson', 'year' => 2019, 'category' => 'Physics', 'copies' => 22, 'price' => 4200],
            ['isbn' => '9781118230725', 'title' => 'Fundamentals of Physics', 'author' => 'David Halliday', 'publisher' => 'Wiley', 'year' => 2013, 'category' => 'Physics', 'copies' => 20, 'price' => 4000],
            ['isbn' => '9780134989174', 'title' => 'Modern Physics', 'author' => 'Kenneth S. Krane', 'publisher' => 'Wiley', 'year' => 2019, 'category' => 'Physics', 'copies' => 12, 'price' => 3900],
            
            // General Engineering
            ['isbn' => '9780134702384', 'title' => 'Engineering Graphics', 'author' => 'Frederick E. Giesecke', 'publisher' => 'Pearson', 'year' => 2016, 'category' => 'General Engineering', 'copies' => 15, 'price' => 3300],
            ['isbn' => '9780133750553', 'title' => 'Introduction to Engineering', 'author' => 'Paul H. Wright', 'publisher' => 'Wiley', 'year' => 2015, 'category' => 'General Engineering', 'copies' => 20, 'price' => 3000],
        ];
        
        foreach ($books as $book) {
            $totalCopies = $book['copies'];
            $availableCopies = $totalCopies - rand(0, min(5, $totalCopies)); // Some books are issued
            
            Book::create([
                'isbn' => $book['isbn'],
                'title' => $book['title'],
                'author' => $book['author'],
                'publisher' => $book['publisher'],
                'publication_year' => $book['year'],
                'category' => $book['category'],
                'total_copies' => $totalCopies,
                'available_copies' => $availableCopies,
                'description' => 'A comprehensive textbook on ' . $book['title'] . ' by ' . $book['author'] . '. Essential reading for students.',
                'price' => $book['price'],
                'is_active' => true,
            ]);
        }
    }
}

