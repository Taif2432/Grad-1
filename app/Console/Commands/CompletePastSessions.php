<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Session;
Use Illuminate\Support\Carbon;

class CompletePastSessions extends Command
{
    protected $signature = 'session:complete-past';
    protected $description = 'Mark scheduled sessions whose scheduled_at <now() as completed';

    /**
     * Execute the console command.
     */
    public function handle(): int 
    {
        $count = Session::Where('status', 'scheduled_at')
         ->where('scheduled_at', '<', Carbon::now())
         ->update(['status' => 'completed']);
         
         $this-> info("Marked {$count} sessions as completed .");
         return Command::SUCCESS;
    }
}
