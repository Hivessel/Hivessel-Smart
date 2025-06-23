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

        // Grades Seeder
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
            ['level' => 'Grade 10', 'active' => true],
        ];

        foreach ($grades as $grade) {
            \App\Models\Grade::create($grade);
        }


        // Subjects Seeder
        $subjects = [
            // Grade 1 Subjects
            ['subject' => 'GMRC', 'grade_id' => 1],
            ['subject' => 'Makabansa', 'grade_id' => 1],
            ['subject' => 'Language', 'grade_id' => 1],
            ['subject' => 'Math', 'grade_id' => 1],
            ['subject' => 'Reading and Literacy', 'grade_id' => 1],

            // Grade 2 Subjects
            ['subject' => 'English', 'grade_id' => 2],
            ['subject' => 'Filipino', 'grade_id' => 2],
            ['subject' => 'Math', 'grade_id' => 2],
            ['subject' => 'Makabansa', 'grade_id' => 2],
            ['subject' => 'GMRC', 'grade_id' => 2],

            // Grade 3 Subjects
            ['subject' => 'English', 'grade_id' => 3],
            ['subject' => 'Filipino', 'grade_id' => 3],
            ['subject' => 'Math', 'grade_id' => 3],
            ['subject' => 'Makabansa', 'grade_id' => 3],
            ['subject' => 'GMRC', 'grade_id' => 3],
            ['subject' => 'Science', 'grade_id' => 3],

            // Grade 4 Subjects
            ['subject' => 'AP', 'grade_id' => 4],
            ['subject' => 'English', 'grade_id' => 4],
            ['subject' => 'Filipino', 'grade_id' => 4],
            ['subject' => 'Math', 'grade_id' => 4],
            ['subject' => 'GMRC', 'grade_id' => 4],
            ['subject' => 'Science', 'grade_id' => 4],
            ['subject' => 'EPP/TLE', 'grade_id' => 4],
            ['subject' => 'Music and Arts', 'grade_id' => 4],
            ['subject' => 'PE and Health', 'grade_id' => 4],

            // Grade 5 Subjects
            ['subject' => 'AP', 'grade_id' => 5],
            ['subject' => 'English', 'grade_id' => 5],
            ['subject' => 'Filipino', 'grade_id' => 5],
            ['subject' => 'Math', 'grade_id' => 5],
            ['subject' => 'GMRC', 'grade_id' => 5],
            ['subject' => 'Science', 'grade_id' => 5],
            ['subject' => 'EPP/TLE', 'grade_id' => 5],
            ['subject' => 'Music and Arts', 'grade_id' => 5],
            ['subject' => 'PE and Health', 'grade_id' => 5],

            // Grade 6 Subjects
            ['subject' => 'AP', 'grade_id' => 6],
            ['subject' => 'English', 'grade_id' => 6],
            ['subject' => 'Filipino', 'grade_id' => 6],
            ['subject' => 'Math', 'grade_id' => 6],
            ['subject' => 'GMRC', 'grade_id' => 6],
            ['subject' => 'Science', 'grade_id' => 6],
            ['subject' => 'EPP/TLE', 'grade_id' => 6],
            ['subject' => 'Music and Arts', 'grade_id' => 6],
            ['subject' => 'PE and Health', 'grade_id' => 6],

            // Grade 7 Subjects
            ['subject' => 'AP', 'grade_id' => 7],
            ['subject' => 'English', 'grade_id' => 7],
            ['subject' => 'Filipino', 'grade_id' => 7],
            ['subject' => 'Math', 'grade_id' => 7],
            ['subject' => 'Value Education', 'grade_id' => 7],
            ['subject' => 'Science', 'grade_id' => 7],
            ['subject' => 'EPP/TLE', 'grade_id' => 7],
            ['subject' => 'Music and Arts', 'grade_id' => 7],
            ['subject' => 'PE and Health', 'grade_id' => 7],

            // Grade 8 Subjects
            ['subject' => 'AP', 'grade_id' => 8],
            ['subject' => 'English', 'grade_id' => 8],
            ['subject' => 'Filipino', 'grade_id' => 8],
            ['subject' => 'Math', 'grade_id' => 8],
            ['subject' => 'Value Education', 'grade_id' => 8],
            ['subject' => 'Science', 'grade_id' => 8],
            ['subject' => 'EPP/TLE', 'grade_id' => 8],
            ['subject' => 'Music and Arts', 'grade_id' => 8],
            ['subject' => 'PE and Health', 'grade_id' => 8],

            // Grade 9 Subjects
            ['subject' => 'AP', 'grade_id' => 9],
            ['subject' => 'English', 'grade_id' => 9],
            ['subject' => 'Filipino', 'grade_id' => 9],
            ['subject' => 'Math', 'grade_id' => 9],
            ['subject' => 'Value Education', 'grade_id' => 9],
            ['subject' => 'Science', 'grade_id' => 9],
            ['subject' => 'EPP/TLE', 'grade_id' => 9],
            ['subject' => 'Music and Arts', 'grade_id' => 9],
            ['subject' => 'PE and Health', 'grade_id' => 9],

            // Grade 10 Subjects
            ['subject' => 'AP', 'grade_id' => 10],
            ['subject' => 'English', 'grade_id' => 10],
            ['subject' => 'Filipino', 'grade_id' => 10],
            ['subject' => 'Math', 'grade_id' => 10],
            ['subject' => 'Value Education', 'grade_id' => 10],
            ['subject' => 'Science', 'grade_id' => 10],
            ['subject' => 'EPP/TLE', 'grade_id' => 10],
            ['subject' => 'Music and Arts', 'grade_id' => 10],
            ['subject' => 'PE and Health', 'grade_id' => 10],
        ];
        foreach ($subjects as $subject) {
            \App\Models\Subject::create($subject);
        }

        // Quarters Seeder
        $quarters = [
            ['quarter' => 'Quarter 1', 'active' => true],
            ['quarter' => 'Quarter 2', 'active' => true],
            ['quarter' => 'Quarter 3', 'active' => true],
            ['quarter' => 'Quarter 4', 'active' => true],
        ];

        foreach ($quarters as $quarter) {
            \App\Models\Quarter::create($quarter);
        }


        // Contents Seeder
        $contents = [
            [
                'content' => 'Describing Forces',
                'grade_id' => 4,
                'subject_id' => 22,
                'quarter_id' => 3
            ]
        ];

        foreach ($contents as $content) {
            \App\Models\Content::create($content);
        }
        
        // Competencies Seeder
        $competencies = [
            [
                'content_id' => 1,
                'competency' => 'The learners participate in guided activities to discover and predict how rigid and soft
objects can be moved and/or changed in shape.',
                'reference' => 'https://drive.google.com/file/d/1zZEyv7jjxQLmgqRDGaTA2QpbUQ01W4ib/view?usp=drive_link',
            ]
        ];

        foreach ($competencies as $ompetency) {
            \App\Models\Competency::create($ompetency);
        }

        $proficiencies = [
            ['level' => 'Novice', 'active' => true],
            ['level' => 'Apprentice', 'active' => true],
            ['level' => 'Practitioner', 'active' => true],
            ['level' => 'Expert', 'active' => true],
            ['level' => 'Master', 'active' => true],
        ];

        foreach ($proficiencies as $proficiency) {
            \App\Models\ProficiencyLevel::create($proficiency);
        }

        $languages = [
            ['language' => 'English', 'active' => true],
            ['language' => 'Tagalog', 'active' => true]
        ];

        foreach ($languages as $language) {
            \App\Models\Language::create($language);
        }
    }
}
