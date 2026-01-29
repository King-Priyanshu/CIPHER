<?php

namespace App\Console\Commands;

use App\Models\ContentPage;
use App\Models\SubscriptionPlan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.xml file for SEO';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Generating sitemap...');

        $baseUrl = config('app.url');
        $now = now()->toAtomString();

        $urls = [];

        // Static pages
        $staticPages = [
            ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => '/faq', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/login', 'priority' => '0.5', 'changefreq' => 'yearly'],
            ['loc' => '/register', 'priority' => '0.5', 'changefreq' => 'yearly'],
        ];

        foreach ($staticPages as $page) {
            $urls[] = [
                'loc' => $baseUrl . $page['loc'],
                'lastmod' => $now,
                'changefreq' => $page['changefreq'],
                'priority' => $page['priority'],
            ];
        }

        // Dynamic content pages
        $contentPages = ContentPage::where('is_published', true)->get();
        foreach ($contentPages as $page) {
            $urls[] = [
                'loc' => $baseUrl . '/page/' . $page->slug,
                'lastmod' => $page->updated_at->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        // Subscription plans (checkout pages)
        $plans = SubscriptionPlan::where('is_active', true)->get();
        foreach ($plans as $plan) {
            $urls[] = [
                'loc' => $baseUrl . '/checkout/' . $plan->slug,
                'lastmod' => $plan->updated_at->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ];
        }

        // Build XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $url) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . PHP_EOL;
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>' . PHP_EOL;

        // Write to public directory
        $sitemapPath = public_path('sitemap.xml');
        File::put($sitemapPath, $xml);

        $this->info("Sitemap generated with " . count($urls) . " URLs.");
        $this->info("Saved to: {$sitemapPath}");

        return Command::SUCCESS;
    }
}
