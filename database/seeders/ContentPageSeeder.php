<?php

namespace Database\Seeders;

use App\Models\ContentPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<h1>About CIPHER</h1><p>CIPHER is a community-driven platform where members pool contributions to fund innovative projects. Our mission is to provide transparency and shared value through collective growth.</p>',
                'meta_title' => 'About CIPHER - Community Growth',
                'meta_description' => 'Learn more about CIPHER and our mission to empower communities.',
                'is_published' => true,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h1>Privacy Policy</h1><p>Your privacy is important to us. This policy explains how we collect, use, and protect your personal information.</p>',
                'meta_title' => 'Privacy Policy - CIPHER',
                'meta_description' => 'Read our privacy policy to understand how we handle your data.',
                'is_published' => true,
            ],
            [
                'title' => 'Terms of Service',
                'slug' => 'terms-of-service',
                'content' => '<h1>Terms of Service</h1><p>By using CIPHER, you agree to our terms and conditions. Please read them carefully.</p>',
                'meta_title' => 'Terms of Service - CIPHER',
                'meta_description' => 'Read our terms of service to understand your rights and responsibilities.',
                'is_published' => true,
            ],
        ];

        foreach ($pages as $page) {
            ContentPage::updateOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
