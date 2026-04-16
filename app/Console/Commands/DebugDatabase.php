<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dataforuser;

class DebugDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:debug-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug user data in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Debugging user data...');
        
        $users = Dataforuser::take(5)->get();
        
        foreach ($users as $user) {
            $this->line("User ID: {$user->id}");
            $this->line("Name: {$user->name}");
            $this->line("Last Seen: " . ($user->last_seen ? $user->last_seen->format('Y-m-d H:i:s') : 'NULL'));
            $this->line("Is Online: " . ($user->is_online ? 'true' : 'false'));
            $this->line("Is Subscribed: " . ($user->is_subscribed ? 'true' : 'false'));
            $this->line("---");
        }
        
        return 0;
    }
}
