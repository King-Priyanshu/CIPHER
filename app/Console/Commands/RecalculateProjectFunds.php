<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\ProjectInvestment;
use Illuminate\Support\Facades\DB;

class RecalculateProjectFunds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cipher:sync-funds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate and sync project current funds based on active investments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting fund synchronization...');

        $projects = Project::all();
        $bar = $this->output->createProgressBar(count($projects));

        $bar->start();

        foreach ($projects as $project) {
            $realTotal = ProjectInvestment::where('project_id', $project->id)
                ->whereIn('status', ['allocated', 'active'])
                ->sum('amount');

            if (abs($project->current_fund - $realTotal) > 0.01) {
                $project->update(['current_fund' => $realTotal]);
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Project funds synchronized successfully.');
    }
}
