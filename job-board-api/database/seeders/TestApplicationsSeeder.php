<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestApplicationsSeeder extends Seeder
{
    public function run(): void
    {
        // The 6 test candidates
        $candidateEmails = [
            'alex@candidate.com',
            'maria@candidate.com',
            'kevin@candidate.com',
            'fatima@candidate.com',
            'lucas@candidate.com',
            'aisha@candidate.com',
            'candidate@gmail.com',
        ];

        $candidates = User::whereIn('email', $candidateEmails)->get()->keyBy('email');

        if ($candidates->isEmpty()) {
            $this->command->error('No test candidates found. Run UsersSeeder first.');
            return;
        }

        // Pull a varied set of approved jobs
        $jobs = Job::where('status', 'approved')
            ->inRandomOrder()
            ->limit(40)
            ->get();

        if ($jobs->isEmpty()) {
            $this->command->error('No approved jobs found.');
            return;
        }

        // Define applications per candidate with explicit statuses
        // Format: [ email => [ [job_index, status, cover_letter_snippet], ... ] ]
        $plan = [
            'alex@candidate.com' => [
                [0,  'accepted',     'I have 5 years of Laravel experience and have shipped several enterprise SaaS products. I am confident I can contribute immediately.'],
                [1,  'interviewing', 'My background in Vue.js and REST API design aligns perfectly with this role. Looking forward to discussing further.'],
                [2,  'rejected',     'I am eager to bring my full-stack skills to your team and help scale the platform.'],
                [3,  'applied',      'This role excites me because of the tech stack and the team culture described in the listing.'],
                [4,  'applied',      'I have worked on similar projects and believe I can add real value from day one.'],
            ],
            'maria@candidate.com' => [
                [5,  'accepted',     'My NLP research background and production ML experience make me a strong fit for this data science role.'],
                [6,  'interviewing', 'I have built end-to-end ML pipelines using TensorFlow and deployed models to production at scale.'],
                [7,  'interviewing', 'Data storytelling and stakeholder communication are strengths I bring alongside my technical skills.'],
                [8,  'rejected',     'I am passionate about turning raw data into actionable insights and would love to join your analytics team.'],
                [9,  'applied',      'My experience with Pandas, SQL, and Scikit-learn directly matches the requirements listed.'],
            ],
            'kevin@candidate.com' => [
                [10, 'accepted',     'I have managed Kubernetes clusters at scale and automated infrastructure with Terraform across multi-cloud environments.'],
                [11, 'interviewing', 'CI/CD pipeline design and incident response are areas where I have deep hands-on experience.'],
                [12, 'applied',      'I am excited about the opportunity to improve developer experience and platform reliability at your company.'],
                [13, 'rejected',     'My AWS certifications and production DevOps experience make me well-suited for this role.'],
                [14, 'applied',      'I thrive in fast-paced environments and have a track record of reducing deployment times significantly.'],
            ],
            'fatima@candidate.com' => [
                [15, 'interviewing', 'I have led design systems for products used by millions of users and I am passionate about accessibility.'],
                [16, 'accepted',     'My Figma prototypes and user research process have consistently improved conversion rates for clients.'],
                [17, 'applied',      'I believe great design is invisible — it just works. I would love to bring that philosophy to your product.'],
                [18, 'rejected',     'I have collaborated closely with engineering teams to ship pixel-perfect interfaces on tight deadlines.'],
                [19, 'applied',      'This role is a great match for my background in interaction design and design systems.'],
            ],
            'lucas@candidate.com' => [
                [20, 'applied',      'I have shipped Flutter apps to both the App Store and Google Play with over 50k downloads each.'],
                [21, 'interviewing', 'My experience with React Native and Firebase allows me to build performant cross-platform apps quickly.'],
                [22, 'accepted',     'I am comfortable owning the full mobile development lifecycle from architecture to release.'],
                [23, 'applied',      'I have worked in agile teams and enjoy collaborating with designers and backend engineers closely.'],
                [24, 'rejected',     'Mobile performance optimization and smooth animations are areas I am particularly passionate about.'],
            ],
            'aisha@candidate.com' => [
                [25, 'applied',      'I have audited and deployed smart contracts on Ethereum mainnet and have deep knowledge of DeFi protocols.'],
                [26, 'interviewing', 'My experience with Hardhat, Ethers.js, and Solidity security best practices is directly relevant to this role.'],
                [27, 'applied',      'I am excited about the opportunity to work on cutting-edge Web3 infrastructure.'],
                [28, 'accepted',     'I have contributed to open-source DeFi projects and understand the unique challenges of on-chain development.'],
                [29, 'rejected',     'Blockchain security and gas optimization are areas where I have significant hands-on experience.'],
            ],
            'candidate@gmail.com' => [
                [30, 'applied',      'I am a passionate developer looking for my next challenge. This role aligns perfectly with my skills.'],
                [31, 'interviewing', 'I have worked on similar projects and I am confident I can contribute from day one.'],
                [32, 'accepted',     'Your company culture and tech stack are exactly what I have been looking for in my next role.'],
                [33, 'rejected',     'I have strong communication skills and enjoy working in collaborative cross-functional teams.'],
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($plan as $email => $applications) {
            $candidate = $candidates->get($email);
            if (!$candidate) {
                $this->command->warn("Candidate not found: {$email}");
                continue;
            }

            foreach ($applications as [$jobIndex, $status, $coverLetter]) {
                $job = $jobs->get($jobIndex);
                if (!$job) continue;

                // Skip duplicates
                $exists = Application::where('candidate_id', $candidate->id)
                    ->where('job_id', $job->id)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                Application::create([
                    'candidate_id' => $candidate->id,
                    'job_id'       => $job->id,
                    'resume_path'  => 'resumes/sample_cv.pdf',
                    'cover_letter' => $coverLetter,
                    'status'       => $status,
                    'is_paid'      => in_array($status, ['accepted', 'interviewing']),
                ]);

                $created++;
                $this->command->line("  [{$status}] {$candidate->name} → {$job->title}");
            }
        }

        $this->command->info("\nDone! Created: {$created} applications, Skipped: {$skipped} duplicates.");
    }
}
