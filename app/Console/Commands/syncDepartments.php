<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Repositories\TPSync\Departments;
use App\Unit;
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
        Log::info('Sync Departments Has Done.');
        return;

        ini_set('max_execution_time', 1200);
        
        $unitsAndClasses=Unit::all();
        foreach($unitsAndClasses as $unit){
            $parent_code='';
            $parentUnit=$unit->getParent();
         
            if($parentUnit) $parent_code=strtolower($parentUnit->code);
            
            if($parent_code){
                $parentDepartment=$this->departments->getTPDepartmentByCode($parent_code);
                if(!$parentDepartment){
                    $unit->update([
                        'err' => '父部門 ' . $parent_code . ' 不存在'
                    ]);
                    continue;
                }

            }

            $name=$unit->name;
            $code=strtolower($unit->code);
            $delete=!$unit->active;
            
            $this->departments->syncDepartment($name, $code,$parent_code,$delete);

            $unit->update([
                'err' => ''
            ]);
            
        }
        
        Log::info('Sync Departments Has Done.');
        $this->info('Sync Departments Has Done.');
    }
}
