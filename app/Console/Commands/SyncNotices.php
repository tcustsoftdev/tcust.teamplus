<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\Teamplus\NoticeService;
use Log;

class SyncNotices extends Command
{
    
    protected $signature = 'sync:notices';

   
    protected $description = 'Sync Notices';

    
    public function __construct(NoticeService  $noticeService)
    {
        parent::__construct();

        $this->noticeService = $noticeService;
      
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
       
        $text='Notices Sync Done';
        $this->noticeService->syncNotices();
        Log::info('Sync Notices Has Done.');
        $this->info($text);
    }
}
