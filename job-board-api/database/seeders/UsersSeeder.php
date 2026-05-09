<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole     = Role::firstOrCreate(['name' => 'admin',     'guard_name' => 'api']);
        $employerRole  = Role::firstOrCreate(['name' => 'employer',  'guard_name' => 'api']);
        $candidateRole = Role::firstOrCreate(['name' => 'candidate', 'guard_name' => 'api']);

        $users = [
            // ── Admins ────────────────────────────────────────────────────────
            [
                'name'     => 'Super Admin',
                'email'    => 'superadmin@jobboard.com',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
                'bio'      => 'Platform super administrator.',
                'location' => 'New York, USA',
                'assign'   => $adminRole,
            ],

            // ── Employers ─────────────────────────────────────────────────────
            [
                'name'                => 'Sarah Mitchell',
                'email'               => 'sarah@technova.com',
                'password'            => Hash::make('password123'),
                'role'                => 'employer',
                'bio'                 => 'Head of Talent at TechNova Solutions.',
                'location'            => 'San Francisco, USA',
                'company_name'        => 'TechNova Solutions',
                'company_description' => 'Enterprise software consultancy building scalable web and mobile apps.',
                'website'             => 'https://technova.io',
                'linkedin_url'        => 'https://linkedin.com/company/technova',
                'assign'              => $employerRole,
            ],
            [
                'name'                => 'James Carter',
                'email'               => 'james@cloudpeak.com',
                'password'            => Hash::make('password123'),
                'role'                => 'employer',
                'bio'                 => 'CTO at CloudPeak Systems.',
                'location'            => 'Seattle, USA',
                'company_name'        => 'CloudPeak Systems',
                'company_description' => 'Cloud infrastructure specialists helping businesses scale on AWS and GCP.',
                'website'             => 'https://cloudpeak.io',
                'linkedin_url'        => 'https://linkedin.com/company/cloudpeak',
                'assign'              => $employerRole,
            ],
            [
                'name'                => 'Lena Hoffmann',
                'email'               => 'lena@pixelcraft.de',
                'password'            => Hash::make('password123'),
                'role'                => 'employer',
                'bio'                 => 'Creative Director at PixelCraft Studio.',
                'location'            => 'Berlin, Germany',
                'company_name'        => 'PixelCraft Studio',
                'company_description' => 'Award-winning design agency focused on user-centered digital experiences.',
                'website'             => 'https://pixelcraft.de',
                'assign'              => $employerRole,
            ],
            [
                'name'                => 'Omar Hassan',
                'email'               => 'omar@finedge.ae',
                'password'            => Hash::make('password123'),
                'role'                => 'employer',
                'bio'                 => 'Founder of FinEdge Capital.',
                'location'            => 'Dubai, UAE',
                'company_name'        => 'FinEdge Capital',
                'company_description' => 'Fintech startup building next-generation payment and investment tools.',
                'website'             => 'https://finedge.ae',
                'assign'              => $employerRole,
            ],
            [
                'name'                => 'Priya Sharma',
                'email'               => 'priya@mobilefirst.in',
                'password'            => Hash::make('password123'),
                'role'                => 'employer',
                'bio'                 => 'VP Engineering at MobileFirst Inc.',
                'location'            => 'Bangalore, India',
                'company_name'        => 'MobileFirst Inc',
                'company_description' => 'Mobile-first product studio building iOS and Android apps at scale.',
                'website'             => 'https://mobilefirst.io',
                'assign'              => $employerRole,
            ],

            // ── Candidates ────────────────────────────────────────────────────
            [
                'name'         => 'Alex Johnson',
                'email'        => 'alex@candidate.com',
                'password'     => Hash::make('password123'),
                'role'         => 'candidate',
                'bio'          => 'Full-stack developer with 5 years of experience in Laravel and Vue.js.',
                'location'     => 'London, UK',
                'skills'       => json_encode(['PHP', 'Laravel', 'Vue.js', 'MySQL', 'Docker']),
                'linkedin_url' => 'https://linkedin.com/in/alexjohnson',
                'resume_path'  => 'resumes/sample_cv.pdf',
                'assign'       => $candidateRole,
            ],
            [
                'name'         => 'Maria Garcia',
                'email'        => 'maria@candidate.com',
                'password'     => Hash::make('password123'),
                'role'         => 'candidate',
                'bio'          => 'Data scientist specializing in machine learning and NLP.',
                'location'     => 'Madrid, Spain',
                'skills'       => json_encode(['Python', 'TensorFlow', 'Pandas', 'SQL', 'Scikit-learn']),
                'linkedin_url' => 'https://linkedin.com/in/mariagarcia',
                'resume_path'  => 'resumes/sample_cv.pdf',
                'assign'       => $candidateRole,
            ],
            [
                'name'         => 'Kevin Nguyen',
                'email'        => 'kevin@candidate.com',
                'password'     => Hash::make('password123'),
                'role'         => 'candidate',
                'bio'          => 'DevOps engineer with expertise in Kubernetes and AWS.',
                'location'     => 'Toronto, Canada',
                'skills'       => json_encode(['AWS', 'Kubernetes', 'Docker', 'Terraform', 'CI/CD']),
                'linkedin_url' => 'https://linkedin.com/in/kevinnguyen',
                'resume_path'  => 'resumes/sample_cv.pdf',
                'assign'       => $candidateRole,
            ],
            [
                'name'         => 'Fatima Al-Rashid',
                'email'        => 'fatima@candidate.com',
                'password'     => Hash::make('password123'),
                'role'         => 'candidate',
                'bio'          => 'UI/UX designer passionate about accessible and beautiful interfaces.',
                'location'     => 'Dubai, UAE',
                'skills'       => json_encode(['Figma', 'Adobe XD', 'Tailwind CSS', 'Sketch', 'Prototyping']),
                'linkedin_url' => 'https://linkedin.com/in/fatimaalrashid',
                'resume_path'  => 'resumes/sample_cv.pdf',
                'assign'       => $candidateRole,
            ],
            [
                'name'         => 'Lucas Müller',
                'email'        => 'lucas@candidate.com',
                'password'     => Hash::make('password123'),
                'role'         => 'candidate',
                'bio'          => 'Mobile developer building cross-platform apps with Flutter and React Native.',
                'location'     => 'Berlin, Germany',
                'skills'       => json_encode(['Flutter', 'React Native', 'Dart', 'Firebase', 'Swift']),
                'linkedin_url' => 'https://linkedin.com/in/lucasmuller',
                'resume_path'  => 'resumes/sample_cv.pdf',
                'assign'       => $candidateRole,
            ],
            [
                'name'         => 'Aisha Okonkwo',
                'email'        => 'aisha@candidate.com',
                'password'     => Hash::make('password123'),
                'role'         => 'candidate',
                'bio'          => 'Blockchain developer with experience in Solidity and DeFi protocols.',
                'location'     => 'Lagos, Nigeria',
                'skills'       => json_encode(['Solidity', 'Web3.js', 'Hardhat', 'Ethers.js', 'React']),
                'linkedin_url' => 'https://linkedin.com/in/aishaokonkwo',
                'resume_path'  => 'resumes/sample_cv.pdf',
                'assign'       => $candidateRole,
            ],
        ];

        foreach ($users as $data) {
            $role = $data['assign'];
            unset($data['assign']);

            // Skip if email already exists
            if (User::where('email', $data['email'])->exists()) {
                $this->command->warn("Skipped (already exists): {$data['email']}");
                continue;
            }

            $user = User::create($data);
            $user->assignRole($role);
            $this->command->info("Created [{$user->role}]: {$user->email}");
        }

        $this->command->info('Done! Users added successfully.');
    }
}
