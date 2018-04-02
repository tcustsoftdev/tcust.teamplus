<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Repositories\TPSync\Departments;
use Log;

class SyncDepartments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:departments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Departments';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Departments $departments)
    {
        parent::__construct();

        $this->departments = $departments;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->departments->syncDepartments();
        Log::info('Sync Departments Has Done.');
        $this->info('Sync Departments Has Done.');
    }
}
