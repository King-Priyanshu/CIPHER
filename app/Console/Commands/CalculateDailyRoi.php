<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InvestmentService;
use Illuminate\Support\Facades\Log;

class CalculateDailyRoi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'investment:daily-roi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate and distribute daily ROI for active project investments';

    protected $investmentService;

    public function __construct(InvestmentService $investmentService)
    {
        parent::__construct();
        $this->investmentService = $investmentService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Daily ROI Calculation...');
        Log::info('Daily ROI Calculation Started');

        try {
            // Process ALL active investments
            $this->investmentService->processDailyRoi(); // Need to implement bulk processing or loop in service
            
            // Note: InvestmentService::processDailyRoi was implemented to loop all active investments.
            
            $this->info('Daily ROI Calculation Completed Successfully.');
            Log::info('Daily ROI Calculation Completed');
        } catch (\Exception $e) {
            $this->error('Error processing ROI: ' . $e->getMessage());
            Log::error('Daily ROI Calculation Failed: ' . $e->getMessage());
        }
    }
}
