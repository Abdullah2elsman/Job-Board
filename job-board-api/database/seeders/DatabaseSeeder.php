<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Job;
use App\Models\JobSkill;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    private array $categoryData = [
        ['name' => 'Software Engineering',  'slug' => 'software-engineering'],
        ['name' => 'Data Science',           'slug' => 'data-science'],
        ['name' => 'UI/UX Design',           'slug' => 'ui-ux-design'],
        ['name' => 'DevOps & Cloud',         'slug' => 'devops-cloud'],
        ['name' => 'Cybersecurity',          'slug' => 'cybersecurity'],
        ['name' => 'Product Management',     'slug' => 'product-management'],
        ['name' => 'Marketing & Growth',     'slug' => 'marketing-growth'],
        ['name' => 'Sales & Business Dev',   'slug' => 'sales-business-dev'],
        ['name' => 'Finance & Accounting',   'slug' => 'finance-accounting'],
        ['name' => 'Human Resources',        'slug' => 'human-resources'],
        ['name' => 'Content & Copywriting',  'slug' => 'content-copywriting'],
        ['name' => 'Customer Support',       'slug' => 'customer-support'],
        ['name' => 'Mobile Development',     'slug' => 'mobile-development'],
        ['name' => 'QA & Testing',           'slug' => 'qa-testing'],
        ['name' => 'Blockchain & Web3',      'slug' => 'blockchain-web3'],
    ];

    private array $skillPool = [
        'PHP',
        'Laravel',
        'Vue.js',
        'React',
        'Node.js',
        'TypeScript',
        'JavaScript',
        'Python',
        'Django',
        'FastAPI',
        'Go',
        'Rust',
        'Java',
        'Spring Boot',
        'Kotlin',
        'Swift',
        'Flutter',
        'React Native',
        'Docker',
        'Kubernetes',
        'AWS',
        'GCP',
        'Azure',
        'Terraform',
        'Ansible',
        'CI/CD',
        'Jenkins',
        'GitHub Actions',
        'PostgreSQL',
        'MySQL',
        'MongoDB',
        'Redis',
        'Elasticsearch',
        'GraphQL',
        'REST API',
        'Microservices',
        'Linux',
        'Bash',
        'Figma',
        'Adobe XD',
        'Sketch',
        'Tailwind CSS',
        'SASS',
        'Webpack',
        'Vite',
        'Next.js',
        'Nuxt.js',
        'TensorFlow',
        'PyTorch',
        'Pandas',
        'NumPy',
        'Scikit-learn',
        'Tableau',
        'Power BI',
        'SQL',
        'Spark',
        'Hadoop',
        'Airflow',
        'dbt',
        'Solidity',
        'Web3.js',
        'Ethers.js',
        'Hardhat',
        'SEO',
        'Google Ads',
        'HubSpot',
        'Salesforce',
        'Jira',
        'Confluence',
        'Agile',
        'Scrum',
        'Kanban',
    ];

    private array $companies = [
        ['name' => 'TechNova Solutions',   'desc' => 'A leading software consultancy delivering enterprise-grade web and mobile applications.'],
        ['name' => 'CloudPeak Systems',    'desc' => 'Cloud infrastructure specialists helping businesses scale with AWS and GCP.'],
        ['name' => 'DataBridge Analytics', 'desc' => 'Data-driven insights company transforming raw data into business intelligence.'],
        ['name' => 'PixelCraft Studio',    'desc' => 'Award-winning design agency focused on user-centered digital experiences.'],
        ['name' => 'SecureNet Labs',       'desc' => 'Cybersecurity firm protecting enterprises from modern digital threats.'],
        ['name' => 'GrowthHive Agency',    'desc' => 'Performance marketing agency specializing in SaaS and e-commerce growth.'],
        ['name' => 'FinEdge Capital',      'desc' => 'Fintech startup building next-generation payment and investment tools.'],
        ['name' => 'MobileFirst Inc',      'desc' => 'Mobile-first product studio building iOS and Android apps at scale.'],
        ['name' => 'DevStream Corp',       'desc' => 'Open-source tooling company empowering developer productivity worldwide.'],
        ['name' => 'NexGen Ventures',      'desc' => 'Venture-backed startup studio launching products across AI and Web3.'],
        ['name' => 'Horizon HR Tech',      'desc' => 'HR technology platform streamlining hiring, onboarding, and people ops.'],
        ['name' => 'ContentLab Media',     'desc' => 'Content strategy and production house for B2B and B2C brands.'],
        ['name' => 'SupportHub Global',    'desc' => 'Customer experience outsourcing company serving 200+ global clients.'],
        ['name' => 'BlockChain Forge',     'desc' => 'Web3 development studio building DeFi protocols and NFT platforms.'],
        ['name' => 'QualityFirst QA',      'desc' => 'Dedicated software testing and quality assurance services company.'],
        ['name' => 'Apex Product Co',      'desc' => 'Product management consultancy helping startups ship faster and smarter.'],
        ['name' => 'SalesPro Global',      'desc' => 'B2B sales enablement company with a global network of enterprise clients.'],
        ['name' => 'Nimbus Cloud',         'desc' => 'Multi-cloud managed services provider with 24/7 infrastructure support.'],
        ['name' => 'Spark Data Co',        'desc' => 'Big data engineering firm specializing in real-time analytics pipelines.'],
        ['name' => 'UX Collective',        'desc' => 'Remote-first design collective working with startups and Fortune 500s.'],
    ];

    private array $jobTitles = [
        'Senior Backend Engineer',
        'Full Stack Developer',
        'Frontend Engineer',
        'Junior PHP Developer',
        'Laravel Developer',
        'Node.js Engineer',
        'Python Developer',
        'Go Engineer',
        'Java Backend Developer',
        'Software Architect',
        'Staff Engineer',
        'Principal Engineer',
        'Data Scientist',
        'Data Analyst',
        'Machine Learning Engineer',
        'Data Engineer',
        'BI Developer',
        'Analytics Engineer',
        'UI/UX Designer',
        'Product Designer',
        'Visual Designer',
        'UX Researcher',
        'Motion Designer',
        'Brand Designer',
        'DevOps Engineer',
        'Site Reliability Engineer',
        'Cloud Architect',
        'Platform Engineer',
        'Infrastructure Engineer',
        'Kubernetes Engineer',
        'Security Engineer',
        'Penetration Tester',
        'SOC Analyst',
        'Application Security Engineer',
        'Cloud Security Specialist',
        'Product Manager',
        'Senior Product Manager',
        'Technical Product Manager',
        'Product Owner',
        'Growth Product Manager',
        'Digital Marketing Manager',
        'SEO Specialist',
        'Content Strategist',
        'Performance Marketing Manager',
        'Email Marketing Specialist',
        'Account Executive',
        'Sales Development Representative',
        'Enterprise Sales Manager',
        'Business Development Manager',
        'Customer Success Manager',
        'Financial Analyst',
        'Accountant',
        'Controller',
        'FP&A Analyst',
        'HR Manager',
        'Talent Acquisition Specialist',
        'People Operations Manager',
        'Recruiter',
        'HR Business Partner',
        'Technical Writer',
        'Content Writer',
        'Copywriter',
        'Blog Manager',
        'Customer Support Specialist',
        'Support Engineer',
        'Technical Support Lead',
        'iOS Developer',
        'Android Developer',
        'Flutter Developer',
        'React Native Developer',
        'QA Engineer',
        'Automation Test Engineer',
        'QA Lead',
        'SDET',
        'Solidity Developer',
        'Web3 Engineer',
        'Smart Contract Auditor',
        'DeFi Developer',
    ];

    private array $locations = [
        'New York, USA',
        'San Francisco, USA',
        'Austin, USA',
        'Seattle, USA',
        'London, UK',
        'Berlin, Germany',
        'Amsterdam, Netherlands',
        'Paris, France',
        'Toronto, Canada',
        'Vancouver, Canada',
        'Sydney, Australia',
        'Melbourne, Australia',
        'Dubai, UAE',
        'Singapore',
        'Tokyo, Japan',
        'Bangalore, India',
        'Cairo, Egypt',
        'Lagos, Nigeria',
        'Nairobi, Kenya',
        'Remote',
    ];

    private array $experienceLevels = ['entry', 'mid', 'senior', 'director'];
    private array $workTypes        = ['remote', 'onsite', 'hybrid'];
    private array $appStatuses      = ['applied', 'interviewing', 'accepted', 'rejected'];

    private array $jobDescriptions = [
        "We are looking for a talented %s to join our growing team. You will work closely with cross-functional teams to design, develop, and maintain high-quality software solutions. This is an exciting opportunity to make a real impact at a fast-growing company. Our engineering culture values ownership, collaboration, and continuous improvement.",
        "Join our engineering team as a %s and help us build the next generation of our platform. You will own key features end-to-end, collaborate with product and design, and mentor junior team members. We ship fast, learn from data, and celebrate wins together.",
        "As a %s at our company, you will be responsible for delivering scalable, maintainable solutions in a collaborative environment. We value clean code, good communication, and a passion for continuous learning. You will have real ownership over your work and the freedom to propose improvements.",
        "We are hiring a %s to help us scale our infrastructure and improve developer experience. You will work on challenging problems with a talented team that values autonomy and ownership. Remote-friendly culture with async-first communication.",
        "Our team is growing and we need a skilled %s to help us deliver world-class products. You will have the opportunity to shape technical decisions and contribute to our engineering culture. Competitive salary, equity, and a fully remote setup.",
    ];

    private array $responsibilitiesPool = [
        "Design and implement scalable backend services. Collaborate with frontend engineers on API contracts. Participate in code reviews and technical discussions. Write unit and integration tests. Contribute to architectural decisions.",
        "Build and maintain responsive user interfaces. Work closely with designers to implement pixel-perfect UIs. Optimize application performance. Write clean, well-documented code. Participate in agile ceremonies.",
        "Manage and optimize cloud infrastructure. Implement CI/CD pipelines. Monitor system health and respond to incidents. Automate repetitive operational tasks. Document infrastructure and processes.",
        "Analyze large datasets to extract actionable insights. Build and maintain data pipelines. Create dashboards and reports for stakeholders. Collaborate with engineering on data models. Present findings to leadership.",
        "Lead product discovery and define roadmaps. Work with engineering and design to ship features. Gather and prioritize customer feedback. Define success metrics and track KPIs. Communicate progress to stakeholders.",
    ];

    private array $requirementsPool = [
        "3+ years of professional software development experience. Strong proficiency in at least one backend language. Experience with relational databases. Familiarity with cloud platforms. Excellent communication skills.",
        "2+ years of frontend development experience. Proficiency in React or Vue.js. Strong understanding of HTML, CSS, and JavaScript. Experience with REST APIs. Attention to detail and design sensibility.",
        "4+ years of DevOps or infrastructure experience. Hands-on experience with AWS or GCP. Proficiency with Docker and Kubernetes. Experience with infrastructure-as-code tools. Strong scripting skills.",
        "Bachelor degree in Computer Science or related field. 2+ years of data analysis experience. Proficiency in SQL and Python. Experience with BI tools. Strong analytical and problem-solving skills.",
        "5+ years of product management experience. Track record of shipping successful products. Strong analytical skills. Excellent written and verbal communication. Experience working in agile environments.",
    ];

    private array $commentTemplates = [
        'Is this position still open? I have %d years of experience in %s.',
        'What is the interview process like for this role?',
        'Does this role offer visa sponsorship for international candidates?',
        'I applied last week, any update on the timeline?',
        'Great opportunity! The %s requirement is exactly my background.',
        'Is remote work permanent or temporary for this position?',
        'What tech stack does the team primarily use day-to-day?',
        'Are there opportunities for growth into a senior role?',
        'What is the team size for this position?',
        'Does the company offer equity or stock options?',
        'Is there a take-home assignment as part of the interview?',
        'What does the onboarding process look like?',
        'I have experience with %s, would that be relevant here?',
        'Is this a new role or a backfill?',
        'What are the core working hours for this remote position?',
        'Does the company sponsor conferences or training?',
        'Is there flexibility on the experience level requirement?',
        'What is the biggest challenge the team is currently facing?',
        'Are there opportunities to work across different teams?',
        'This looks like a great fit for my background in %s!',
    ];

    public function run(): void
    {
        // ── Roles ─────────────────────────────────────────────────────────────
        $adminRole     = Role::firstOrCreate(['name' => 'admin',     'guard_name' => 'api']);
        $employerRole  = Role::firstOrCreate(['name' => 'employer',  'guard_name' => 'api']);
        $candidateRole = Role::firstOrCreate(['name' => 'candidate', 'guard_name' => 'api']);

        // ── Fixed accounts ────────────────────────────────────────────────────
        User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role'     => 'admin',
            'bio'      => 'Platform administrator managing all operations.',
            'location' => 'New York, USA',
        ])->assignRole($adminRole);

        User::create([
            'name'         => 'Candidate User',
            'email'        => 'candidate@gmail.com',
            'password'     => Hash::make('12345678'),
            'role'         => 'candidate',
            'bio'          => 'Passionate full-stack developer with 4 years of experience.',
            'location'     => 'Berlin, Germany',
            'skills'       => json_encode(['PHP', 'Laravel', 'Vue.js', 'MySQL']),
            'linkedin_url' => 'https://linkedin.com/in/candidate-user',
        ])->assignRole($candidateRole);

        User::create([
            'name'                => 'Employer User',
            'email'               => 'employer@gmail.com',
            'password'            => Hash::make('12345678'),
            'role'                => 'employer',
            'bio'                 => 'HR lead at TechNova Solutions.',
            'location'            => 'San Francisco, USA',
            'company_name'        => 'TechNova Solutions',
            'company_description' => 'A leading software consultancy.',
            'website'             => 'https://technova.io',
        ])->assignRole($employerRole);

        // ── Categories ────────────────────────────────────────────────────────
        $categories = collect($this->categoryData)->map(fn($c) => Category::create($c));

        // ── Employers ─────────────────────────────────────────────────────────
        $employers = collect($this->companies)->map(function ($company) use ($employerRole) {
            $user = User::create([
                'name'                => fake()->name(),
                'email'               => fake()->unique()->safeEmail(),
                'password'            => Hash::make('password'),
                'role'                => 'employer',
                'bio'                 => 'Hiring manager at ' . $company['name'],
                'location'            => fake()->randomElement($this->locations),
                'company_name'        => $company['name'],
                'company_description' => $company['desc'],
                'website'             => 'https://' . Str::slug($company['name']) . '.com',
                'linkedin_url'        => 'https://linkedin.com/company/' . Str::slug($company['name']),
            ]);
            $user->assignRole($employerRole);
            return $user;
        });

        // ── Candidates (310) ──────────────────────────────────────────────────
        $candidateSkillSets = [
            ['PHP', 'Laravel', 'MySQL', 'Vue.js'],
            ['Python', 'Django', 'PostgreSQL', 'REST API'],
            ['React', 'TypeScript', 'Node.js', 'GraphQL'],
            ['Java', 'Spring Boot', 'Kubernetes', 'Docker'],
            ['Go', 'Microservices', 'Redis', 'AWS'],
            ['Flutter', 'Dart', 'Firebase', 'REST API'],
            ['Swift', 'iOS', 'Xcode', 'Core Data'],
            ['Kotlin', 'Android', 'Jetpack Compose', 'Firebase'],
            ['Figma', 'Adobe XD', 'Sketch', 'Tailwind CSS'],
            ['TensorFlow', 'PyTorch', 'Pandas', 'NumPy'],
            ['AWS', 'Terraform', 'Docker', 'CI/CD'],
            ['Solidity', 'Web3.js', 'Hardhat', 'Ethers.js'],
            ['SEO', 'Google Ads', 'HubSpot', 'Content Strategy'],
            ['SQL', 'Power BI', 'Tableau', 'dbt'],
            ['Selenium', 'Cypress', 'Jest', 'Postman'],
        ];

        $candidates = collect(range(1, 310))->map(function () use ($candidateRole, $candidateSkillSets) {
            $user = User::create([
                'name'         => fake()->name(),
                'email'        => fake()->unique()->safeEmail(),
                'password'     => Hash::make('password'),
                'role'         => 'candidate',
                'bio'          => fake()->sentence(12),
                'location'     => fake()->randomElement($this->locations),
                'skills'       => json_encode(fake()->randomElement($candidateSkillSets)),
                'linkedin_url' => 'https://linkedin.com/in/' . Str::slug(fake()->name()),
                'resume_path'  => 'resumes/sample_cv.pdf',
            ]);
            $user->assignRole($candidateRole);
            return $user;
        });

        // ── Jobs (~320) ───────────────────────────────────────────────────────
        $jobs = collect();

        $employers->each(function ($employer) use ($categories, &$jobs) {
            $count = rand(14, 18);
            for ($i = 0; $i < $count; $i++) {
                $title     = fake()->randomElement($this->jobTitles);
                $salaryMin = fake()->randomElement([40000, 50000, 60000, 70000, 80000, 90000, 100000]);
                $salaryMax = $salaryMin + fake()->randomElement([10000, 15000, 20000, 25000, 30000]);
                $workType  = fake()->randomElement($this->workTypes);

                $job = Job::create([
                    'employer_id'      => $employer->id,
                    'category_id'      => $categories->random()->id,
                    'title'            => $title,
                    'description'      => sprintf(fake()->randomElement($this->jobDescriptions), $title) . ' ' . fake()->paragraph(3),
                    'responsibilities' => fake()->randomElement($this->responsibilitiesPool),
                    'requirements'     => fake()->randomElement($this->requirementsPool),
                    'skills'           => implode(', ', fake()->randomElements($this->skillPool, rand(3, 5))),
                    'salary'           => ($salaryMin + $salaryMax) / 2,
                    'salary_min'       => $salaryMin,
                    'salary_max'       => $salaryMax,
                    'work_type'        => $workType,
                    'location'         => $workType === 'remote' ? 'Remote' : fake()->randomElement($this->locations),
                    'experience_level' => fake()->randomElement($this->experienceLevels),
                    'status'           => fake()->randomElement(['approved', 'approved', 'approved', 'pending', 'rejected']),
                    'views_count'      => rand(0, 2500),
                    'deadline'         => now()->addDays(rand(10, 90)),
                ]);

                foreach (fake()->randomElements($this->skillPool, rand(3, 6)) as $skill) {
                    JobSkill::create(['job_id' => $job->id, 'skill_name' => $skill]);
                }

                $jobs->push($job);
            }
        });

        // ── Applications (300+) ───────────────────────────────────────────────
        $approvedJobs = $jobs->where('status', 'approved')->values();
        $usedPairs    = [];
        $applications = collect();

        $candidates->each(function ($candidate) use ($approvedJobs, &$usedPairs, &$applications) {
            $applyCount = rand(1, 3);
            $applied = 0;
            $attempts = 0;
            while ($applied < $applyCount && $attempts < 20) {
                $attempts++;
                $job  = $approvedJobs->random();
                $pair = $candidate->id . '-' . $job->id;
                if (isset($usedPairs[$pair])) continue;
                $usedPairs[$pair] = true;
                $app = Application::create([
                    'candidate_id' => $candidate->id,
                    'job_id'       => $job->id,
                    'resume_path'  => 'resumes/sample_cv.pdf',
                    'cover_letter' => fake()->paragraph(3),
                    'status'       => fake()->randomElement($this->appStatuses),
                    'is_paid'      => fake()->boolean(25),
                ]);
                $applications->push($app);
                $applied++;
            }
        });

        while ($applications->count() < 300) {
            $candidate = $candidates->random();
            $job       = $approvedJobs->random();
            $pair      = $candidate->id . '-' . $job->id;
            if (isset($usedPairs[$pair])) continue;
            $usedPairs[$pair] = true;
            $app = Application::create([
                'candidate_id' => $candidate->id,
                'job_id'       => $job->id,
                'resume_path'  => 'resumes/sample_cv.pdf',
                'cover_letter' => fake()->paragraph(2),
                'status'       => fake()->randomElement($this->appStatuses),
                'is_paid'      => fake()->boolean(25),
            ]);
            $applications->push($app);
        }

        // ── Payments (300+) ───────────────────────────────────────────────────
        $paidApps = $applications->where('is_paid', true)->values();

        while ($paidApps->count() < 300) {
            $app = $applications->random();
            if (!$app->is_paid) {
                $app->update(['is_paid' => true]);
                $paidApps->push($app->fresh());
            }
        }

        $paymentMethods  = ['stripe', 'paypal', 'credit_card', 'bank_transfer'];
        $paymentStatuses = ['paid', 'paid', 'paid', 'pending', 'failed'];

        foreach ($paidApps->take(320) as $app) {
            $job = $jobs->firstWhere('id', $app->job_id);
            if (!$job) continue;
            Payment::create([
                'employer_id'    => $job->employer_id,
                'application_id' => $app->id,
                'transaction_id' => 'txn_' . Str::random(24),
                'amount'         => fake()->randomElement([25.00, 49.00, 99.00, 149.00, 199.00]),
                'currency'       => 'USD',
                'payment_method' => fake()->randomElement($paymentMethods),
                'status'         => fake()->randomElement($paymentStatuses),
            ]);
        }

        // ── Comments (300+) ───────────────────────────────────────────────────
        $allUsers     = User::all();
        $commentCount = 0;

        foreach ($approvedJobs->take(80) as $job) {
            foreach (range(1, rand(3, 6)) as $ignored) {
                $template = fake()->randomElement($this->commentTemplates);
                Comment::create([
                    'job_id'  => $job->id,
                    'user_id' => $allUsers->random()->id,
                    'content' => sprintf($template, rand(2, 10), fake()->randomElement($this->skillPool)),
                ]);
                $commentCount++;
            }
        }

        while ($commentCount < 300) {
            $template = fake()->randomElement($this->commentTemplates);
            Comment::create([
                'job_id'  => $approvedJobs->random()->id,
                'user_id' => $allUsers->random()->id,
                'content' => sprintf($template, rand(2, 10), fake()->randomElement($this->skillPool)),
            ]);
            $commentCount++;
        }

        // ── Saved Jobs (300+) ─────────────────────────────────────────────────
        $savedPairs = [];
        $savedCount = 0;

        while ($savedCount < 320) {
            $candidate = $candidates->random();
            $job       = $approvedJobs->random();
            $pair      = $candidate->id . '-' . $job->id;
            if (isset($savedPairs[$pair])) continue;
            $savedPairs[$pair] = true;
            DB::table('saved_jobs')->insert([
                'user_id'    => $candidate->id,
                'job_id'     => $job->id,
                'created_at' => now()->subDays(rand(0, 60)),
                'updated_at' => now(),
            ]);
            $savedCount++;
        }

        // ── Summary ───────────────────────────────────────────────────────────
        $this->command->info('Seeding complete!');
        $this->command->info('  Users:        ' . User::count());
        $this->command->info('  Categories:   ' . Category::count());
        $this->command->info('  Jobs:         ' . Job::count());
        $this->command->info('  Job Skills:   ' . JobSkill::count());
        $this->command->info('  Applications: ' . Application::count());
        $this->command->info('  Payments:     ' . Payment::count());
        $this->command->info('  Comments:     ' . Comment::count());
        $this->command->info('  Saved Jobs:   ' . DB::table('saved_jobs')->count());
    }
}
