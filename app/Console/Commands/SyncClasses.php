<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\PSN\SchoolDepartment;
use App\Services\UnitsService;
use App\Services\ClassesService;
use App\Unit;
use App\User;
use App\Repositories\Schools;
use Log;

class SyncClasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:classes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Classes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Schools $schools,ClassesService $classesService) 
    {
        parent::__construct();
        
        $this->classesService=$classesService;
        $this->schools=$schools;
       
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('Sync Classes Has Done.');
        return;
        ini_set('max_execution_time', 1200);
        
        //更新現有班級狀態
        $existClasses=$this->classesService->getAll()->get();
        foreach($existClasses as $existClass){
            //取得對應之學校班級資料
            $code=$existClass->code;
            $schoolClass= $this->schools->getSchoolClassByCode($code);
            if($schoolClass){
                $existClass->active=$schoolClass->isActive();
            }else{
                $existClass->active=false;
            }

            $existClass->save();
        }

        //取得學校所有班級
        $allSchoolClasses=$this->schools->getAllSchoolClasses();
       
        foreach($allSchoolClasses as $schoolClass){
           
            if(!$schoolClass->isActive()) continue;

            $parent=$this->schools->getParentSchoolDepartment($schoolClass);
           
            if(!$parent)  continue;
            
            $unitValues =Unit::initFromSchoolClass($schoolClass,$parent);
            
            $managerNumber='';

            $classCode=$unitValues['code'];
            $classTeacher=$this->schools->getClassTeacher($classCode);
            if($classTeacher)  $managerNumber=$classTeacher->getNumber();
           
            $unitValues['admin'] = $managerNumber;

            $this->createOrUpdateClass($unitValues);
            
        }

        
        Log::info('Sync Classes Has Done.');
        $this->info('Sync Classes Has Done.');
    }

    function createOrUpdateClass(array $unitValues)
    {
        
        $code=$unitValues['code'];
        $classEntity=$this->classesService->getClassByCode($code);
        if($classEntity){
            $classEntity->update($unitValues);
        }else{
            Unit::create($unitValues);
        }
    }
}
