<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\NetworkHelper;
use Illuminate\Support\Facades\Log;

class CheckInternetConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'network:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the app is connected tot the internet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (NetworkHelper::isOnline()) {
            $this->info('Internet is available.');
            Log::info('Internet connection is available.');
        } else {
            $this->error('No internet connection.');
            Log::warning('No internet connection detected.');
        }
         return 0;
    }
}
