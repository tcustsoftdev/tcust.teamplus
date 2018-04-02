<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\Teamplus\GroupService;
use Log;

class SyncGroups extends Command
{
    
    protected $signature = 'sync:groups';

   
    protected $description = 'Sync Groups';

    
    public function __construct(GroupService  $groupService)
    {
        parent::__construct();

        $this->groupService = $groupService;
      
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       
        $text='Groups Sync Done';
       
        $this->groupService->syncGroups();
        Log::info('Sync Groups Has Done.');
        $this->info($text);
    }
}
