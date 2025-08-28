<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminEmail = 'admin@example.com';
        
        $existingAdmin = User::where('email', $adminEmail)->first();
        
        if (!$existingAdmin) {
            User::create([
                'name' => 'Administrator',
                'email' => $adminEmail,
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            
            $this->command->info("Admin user created: {$adminEmail}");
        } else {
            $existingAdmin->update(['role' => 'admin']);
            $this->command->info("Updated existing user to admin: {$adminEmail}");
        }

        $adminEmail2 = 'admin2@example.com';
        
        if (!User::where('email', $adminEmail2)->exists()) {
            User::create([
                'name' => 'Admin Manager',
                'email' => $adminEmail2,
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            
            $this->command->info("Second admin user created: {$adminEmail2}");
        }
    }
}
