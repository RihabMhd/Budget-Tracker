<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
{
    $badges = [
        ['title' => 'Iron Saver I', 'points_required' => 100, 'image_path' => 'badges/ranks/iron1.png'],
        ['title' => 'Bronze Budgeter I', 'points_required' => 500, 'image_path' => 'badges/ranks/bronze1.png'],
        ['title' => 'Silver Strategist I', 'points_required' => 1000, 'image_path' => 'badges/ranks/silver1.png'],
        ['title' => 'Gold Guardian I', 'points_required' => 2000, 'image_path' => 'badges/ranks/gold1.png'],
        ['title' => 'Platinum Planner I', 'points_required' => 3500, 'image_path' => 'badges/ranks/Platinum1.png'],
        ['title' => 'Diamond Discipline I', 'points_required' => 6000, 'image_path' => 'badges/ranks/Diamond1.png'],
        ['title' => 'Ascendant I', 'points_required' => 10000, 'image_path' => 'badges/ranks/Ascendant1.png'],
        ['title' => 'Immortal I', 'points_required' => 20000, 'image_path' => 'badges/ranks/Immortal1.png'],
        ['title' => 'Radiant Master', 'points_required' => 50000, 'image_path' => 'badges/ranks/Radiant1.png']
    ];

    foreach ($badges as $badge) {
        \App\Models\Badge::updateOrCreate(['title' => $badge['title']], $badge);
    }
}
}
