<?php

namespace Database\Seeders;

use App\Models\Hall;
use Illuminate\Database\Seeder;

class HallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if halls already exist
        if (Hall::count() > 0) {
            $this->command->info('Halls already exist. Skipping...');
            return;
        }
        
        $halls = [
            // Residential Halls
            [
                'name' => 'Amar Ekushey Hall',
                'capacity' => 500,
                'description' => 'One of the largest residential halls for male students at KUET. Named after the Language Movement of 1952.',
                'location' => 'East Campus, KUET',
                'facilities' => json_encode([
                    'WiFi Internet',
                    'Common Room',
                    'TV Room',
                    'Reading Room',
                    'Prayer Room',
                    'Canteen',
                    'Indoor Games',
                    'Gym',
                ]),
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Khan Jahan Ali Hall',
                'capacity' => 450,
                'description' => 'A prominent residential hall named after the Muslim saint Khan Jahan Ali.',
                'location' => 'North Campus, KUET',
                'facilities' => json_encode([
                    'WiFi Internet',
                    'Common Room',
                    'TV Room',
                    'Reading Room',
                    'Prayer Room',
                    'Canteen',
                    'Sports Facilities',
                ]),
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Lalan Shah Hall',
                'capacity' => 400,
                'description' => 'Residential hall for male students named after the famous Bengali philosopher Lalan Shah.',
                'location' => 'South Campus, KUET',
                'facilities' => json_encode([
                    'WiFi Internet',
                    'Common Room',
                    'Study Room',
                    'Prayer Room',
                    'Canteen',
                    'Outdoor Sports',
                ]),
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Dr. M A Rashid Hall',
                'capacity' => 350,
                'description' => 'Residential hall dedicated to former Vice-Chancellor Dr. M A Rashid.',
                'location' => 'West Campus, KUET',
                'facilities' => json_encode([
                    'WiFi Internet',
                    'Common Room',
                    'Library',
                    'Prayer Room',
                    'Canteen',
                    'Indoor Games',
                ]),
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Rokeya Hall',
                'capacity' => 300,
                'description' => 'Female residential hall named after Begum Rokeya, pioneer of women\'s education.',
                'location' => 'Central Campus, KUET',
                'facilities' => json_encode([
                    'WiFi Internet',
                    'Common Room',
                    'TV Room',
                    'Reading Room',
                    'Prayer Room',
                    'Canteen',
                    'Security',
                    'CCTV Surveillance',
                ]),
                'is_available' => true,
                'is_active' => true,
            ],
            
            // Academic and Administrative Buildings
            [
                'name' => 'Central Library',
                'capacity' => 200,
                'description' => 'Main library building with extensive collection of books, journals, and digital resources.',
                'location' => 'Central Campus, KUET',
                'facilities' => json_encode([
                    'Reading Halls',
                    'Digital Library',
                    'Research Section',
                    'Periodical Section',
                    'WiFi Internet',
                    'Air Conditioning',
                    'Photocopier',
                ]),
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Central Auditorium',
                'capacity' => 800,
                'description' => 'Main auditorium for seminars, conferences, and cultural events.',
                'location' => 'Central Campus, KUET',
                'facilities' => json_encode([
                    'Sound System',
                    'Projector',
                    'Air Conditioning',
                    'Stage',
                    'Green Room',
                    'Lighting System',
                    'Seating Arrangement',
                ]),
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Seminar Hall - CSE Building',
                'capacity' => 150,
                'description' => 'Seminar hall in Computer Science and Engineering Department building.',
                'location' => 'CSE Building, KUET',
                'facilities' => json_encode([
                    'Projector',
                    'Sound System',
                    'Air Conditioning',
                    'WiFi Internet',
                    'Whiteboard',
                ]),
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Seminar Hall - EEE Building',
                'capacity' => 120,
                'description' => 'Seminar hall in Electrical and Electronic Engineering Department.',
                'location' => 'EEE Building, KUET',
                'facilities' => json_encode([
                    'Projector',
                    'Sound System',
                    'Air Conditioning',
                    'WiFi Internet',
                    'Whiteboard',
                ]),
                'is_available' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Conference Room',
                'capacity' => 50,
                'description' => 'Conference room for meetings and small gatherings.',
                'location' => 'Administrative Building, KUET',
                'facilities' => json_encode([
                    'Projector',
                    'Video Conferencing',
                    'Air Conditioning',
                    'WiFi Internet',
                    'Conference Table',
                    'Sound System',
                ]),
                'is_available' => true,
                'is_active' => true,
            ],
        ];

        foreach ($halls as $hall) {
            Hall::create($hall);
        }
    }
}

