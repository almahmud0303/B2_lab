<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete existing admin users
        User::where('role', 'admin')->delete();
        
        // Create a fresh admin user
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@ums.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '1234567890',
            'is_active' => true,
        ]);

        $this->command->info('✅ Admin user created successfully!');
        $this->command->info('📧 Email: admin@ums.com');
        $this->command->info('🔑 Password: password123');
        $this->command->info('');
        $this->command->info('🚀 You can now login and access all admin features!');
    }
}
