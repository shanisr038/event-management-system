<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Conference', 'color' => '#3498db', 'icon' => 'fas fa-calendar-alt'],
            ['name' => 'Workshop', 'color' => '#2ecc71', 'icon' => 'fas fa-chalkboard-teacher'],
            ['name' => 'Concert', 'color' => '#e74c3c', 'icon' => 'fas fa-music'],
            ['name' => 'Meetup', 'color' => '#f39c12', 'icon' => 'fas fa-users'],
            ['name' => 'Seminar', 'color' => '#9b59b6', 'icon' => 'fas fa-microphone'],
            ['name' => 'Webinar', 'color' => '#1abc9c', 'icon' => 'fas fa-video'],
            ['name' => 'Networking', 'color' => '#34495e', 'icon' => 'fas fa-handshake'],
            ['name' => 'Fundraiser', 'color' => '#e67e22', 'icon' => 'fas fa-donate'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                [
                    'slug' => Str::slug($category['name']),
                    'color' => $category['color'],
                    'icon' => $category['icon'],
                    'description' => "Events related to {$category['name']}",
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Categories seeded successfully!');
    }
}