<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dataforuser;

class UpdateOfflineUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-offline-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update users to offline status if they have not been active for more than 5 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating offline users...');
        
        // Set users as offline if they haven't been active for more than 5 minutes
        $updatedCount = Dataforuser::where('is_online', true)
            ->where('last_seen', '<', now()->subMinutes(5))
            ->update(['is_online' => false]);
        
        $this->info("Updated {$updatedCount} users to offline status");
        
        return 0;
    }
}
