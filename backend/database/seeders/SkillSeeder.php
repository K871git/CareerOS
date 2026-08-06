<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            ['name' => 'PHP',             'slug' => 'php',              'category' => 'Backend'],
            ['name' => 'Laravel',         'slug' => 'laravel',          'category' => 'Backend'],
            ['name' => 'JavaScript',      'slug' => 'javascript',       'category' => 'Frontend'],
            ['name' => 'TypeScript',      'slug' => 'typescript',       'category' => 'Frontend'],
            ['name' => 'React',           'slug' => 'react',            'category' => 'Frontend'],
            ['name' => 'Node.js',         'slug' => 'nodejs',           'category' => 'Backend'],
            ['name' => 'MySQL',           'slug' => 'mysql',            'category' => 'Database'],
            ['name' => 'REST API Design', 'slug' => 'rest-api-design',  'category' => 'Backend'],
            ['name' => 'Git',             'slug' => 'git',              'category' => 'DevOps'],
            ['name' => 'Docker',          'slug' => 'docker',           'category' => 'DevOps'],
            ['name' => 'System Design',   'slug' => 'system-design',    'category' => 'Architecture'],
            ['name' => 'Data Structures', 'slug' => 'data-structures',  'category' => 'CS Fundamentals'],
            ['name' => 'Algorithms',      'slug' => 'algorithms',       'category' => 'CS Fundamentals'],
        ];

        foreach ($skills as $skill) {
            DB::table('skills')->updateOrInsert(
                ['slug' => $skill['slug']],
                array_merge($skill, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]),
            );
        }
    }
}
