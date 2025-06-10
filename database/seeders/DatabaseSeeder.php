<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


        User::create([
            'user_id' => 1,
            'user_login' => 'admin',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@mail.com',
        ]);

        // Grade Seeder

        $grades = [
            ['level' => 'Grade 1', 'active' => true],
            ['level' => 'Grade 2', 'active' => true],
            ['level' => 'Grade 3', 'active' => true],
            ['level' => 'Grade 4', 'active' => true],
            ['level' => 'Grade 5', 'active' => true],
            ['level' => 'Grade 6', 'active' => true],
            ['level' => 'Grade 7', 'active' => true],
            ['level' => 'Grade 8', 'active' => true],
            ['level' => 'Grade 9', 'active' => true],
        ];

        foreach ($grades as $grade) {
            \App\Models\Grade::create($grade);
        }

        $quarters = [
            ['quarter' => 'First Quarter', 'active' => true],
            ['quarter' => 'Second Quarter', 'active' => true],
            ['quarter' => 'Third Quarter', 'active' => true],
            ['quarter' => 'Fourth Quarter', 'active' => true],
        ];

        foreach ($quarters as $quarter) {
            \App\Models\Quarter::create($quarter);
        }

        $proficiencies = [
            ['level' => 'Novice', 'active' => true],
            ['level' => 'Expert', 'active' => true],
            ['level' => 'Master', 'active' => true],
        ];

        foreach ($proficiencies as $proficiency) {
            \App\Models\ProficiencyLevel::create($proficiency);
        }

        $languages = [
            ['language' => 'English', 'active' => true],
            ['language' => 'Filipino', 'active' => true],
            ['language' => 'Cebuano', 'active' => true],
            ['language' => 'Ilocano', 'active' => true],
            ['language' => 'Hiligaynon', 'active' => true],
        ];

        foreach ($languages as $language) {
            \App\Models\Language::create($language);
        }
    }
}
