<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alert;
use Carbon\Carbon;

class expiredAlerts extends Command
{
    protected $signature = 'alerts:expire';
    protected $description = 'Expire alerts that are pending for more than 2 minutes';

    public function handle()
    {
        $now = Carbon::now('Asia/Manila');
        $this->info("[{$now}] Starting alerts:expire command");

        $alerts = Alert::expiredPending()->get();
        $this->info("Found {$alerts->count()} alerts to expire");

        $expiredCount = 0;
        foreach ($alerts as $alert) {
            $alert->update([
                'status' => 'expired',
                'expired_at' => $now,
            ]);
            $expiredCount++;
            $this->info("Expired alert ID: {$alert->id}");
        }

        $this->info("Successfully expired {$expiredCount} alerts");
        
        if ($expiredCount === 0) {
            $this->info("No alerts needed to be expired at this time");
        }
    }
}