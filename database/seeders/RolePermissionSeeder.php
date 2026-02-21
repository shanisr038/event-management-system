<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Event permissions
            'view events',
            'create events',
            'edit events',
            'delete events',
            'publish events',
            
            // Registration permissions
            'view registrations',
            'manage registrations',
            'cancel registrations',
            'check-in attendees',
            
            // Ticket permissions
            'manage tickets',
            'create tickets',
            'edit tickets',
            'delete tickets',
            
            // Category permissions
            'manage categories',
            
            // Report permissions
            'view reports',
            'export reports',
            
            // User management
            'manage users',
            'view users',
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        
        // Admin Role - has all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // Organizer Role
        $organizerRole = Role::firstOrCreate(['name' => 'organizer', 'guard_name' => 'web']);
        $organizerRole->syncPermissions([
            'view events',
            'create events',
            'edit events',
            'delete events',
            'publish events',
            'view registrations',
            'manage registrations',
            'check-in attendees',
            'manage tickets',
            'create tickets',
            'edit tickets',
            'delete tickets',
            'view reports',
        ]);

        // Attendee Role
        $attendeeRole = Role::firstOrCreate(['name' => 'attendee', 'guard_name' => 'web']);
        $attendeeRole->syncPermissions([
            'view events',
            'view registrations',
            'cancel registrations',
        ]);

        // Create or update admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'phone' => '1234567890',
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        // Create or update organizer
        $organizer = User::updateOrCreate(
            ['email' => 'organizer@example.com'],
            [
                'name' => 'Event Organizer',
                'password' => Hash::make('password'),
                'phone' => '1234567890',
                'email_verified_at' => now(),
            ]
        );
        $organizer->syncRoles(['organizer']);

        // Create or update attendee
        $attendee = User::updateOrCreate(
            ['email' => 'attendee@example.com'],
            [
                'name' => 'John Attendee',
                'password' => Hash::make('password'),
                'phone' => '1234567890',
                'email_verified_at' => now(),
            ]
        );
        $attendee->syncRoles(['attendee']);

        $this->command->info('Roles and permissions seeded successfully!');
    }
}