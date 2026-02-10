<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\InvestmentPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            [
                'title' => 'Green Energy Solar Park',
                'description' => 'A large-scale solar farm project generating renewable energy for industrial hubs. This project provides guaranteed returns through government PPA agreements and carbon credit sales.',
                'business_type' => 'Energy',
                'fund_goal' => 5000000,
                'current_fund' => 3250000,
                'roi_percentage' => 12.5,
                'duration_months' => 36,
                'location' => 'Rajasthan, India',
                'risk_level' => 'low',
                'is_featured' => true,
                'image_url' => 'https://images.unsplash.com/photo-1509391366360-fe5bb58583bb?auto=format&fit=crop&w=1200',
                'plans' => [
                    [
                        'name' => 'Monthly Yield',
                        'slug' => 'p1-monthly-yield',
                        'type' => 'onetime',
                        'min_investment' => 1000,
                        'duration_months' => 12,
                        'expected_return_percentage' => 12,
                        'description' => 'Stable monthly dividends. Perfect for regular income.'
                    ],
                    [
                        'name' => 'Growth Accelerator',
                        'slug' => 'p1-growth-accel',
                        'type' => 'sip',
                        'frequency' => 'monthly',
                        'min_investment' => 500,
                        'duration_months' => 24,
                        'expected_return_percentage' => 15,
                        'description' => 'Higher risk with significant growth potential. Compounded returns.'
                    ]
                ]
            ],
            [
                'title' => 'Urban Vertical Farm',
                'description' => 'Modern sustainable agriculture in the heart of the city. We utilize hydroponic technology to grow organic produce with 90% less water than traditional farming. High demand from local restaurants.',
                'business_type' => 'Agriculture',
                'fund_goal' => 2000000,
                'current_fund' => 850000,
                'roi_percentage' => 15.0,
                'duration_months' => 18,
                'location' => 'Singapore',
                'risk_level' => 'medium',
                'is_featured' => true,
                'image_url' => 'https://images.unsplash.com/photo-1558449028-b53a39d100fc?auto=format&fit=crop&w=1200',
                'plans' => [
                     [
                        'name' => 'Eco Harvest',
                        'slug' => 'p2-eco-harvest',
                        'type' => 'onetime',
                        'min_investment' => 5000,
                        'duration_months' => 12,
                        'expected_return_percentage' => 14,
                        'description' => 'Get a share of the quarterly harvest profits.'
                    ]
                ]
            ],
            [
                'title' => 'FinTech AI Core',
                'description' => 'The next generation of AI-driven fraud detection for emerging markets. Our scalable API is already being piloted by three major banks in Southeast Asia. Rapid growth potential.',
                'business_type' => 'Technology',
                'fund_goal' => 10000000,
                'current_fund' => 6400000,
                'roi_percentage' => 22.0,
                'duration_months' => 60,
                'location' => 'Bangalore, India',
                'risk_level' => 'high',
                'is_featured' => true,
                'image_url' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1200',
                'plans' => [
                     [
                        'name' => 'Venture Stake',
                        'slug' => 'p3-venture-stake',
                        'type' => 'onetime',
                        'min_investment' => 10000,
                        'duration_months' => 36,
                        'expected_return_percentage' => 25,
                        'description' => 'High-impact growth for long-term investors.'
                    ]
                ]
            ]
        ];

        foreach ($projects as $projData) {
            $plans = $projData['plans'] ?? [];
            unset($projData['plans']);

            $project = Project::updateOrCreate(
                ['slug' => Str::slug($projData['title'])],
                array_merge($projData, [
                    'status' => 'active',
                    'visibility_status' => 'visible',
                ])
            );
            
            foreach ($plans as $planData) {
                InvestmentPlan::updateOrCreate(
                    ['slug' => $planData['slug']],
                    array_merge($planData, ['project_id' => $project->id])
                );
            }
        }
    }
}
