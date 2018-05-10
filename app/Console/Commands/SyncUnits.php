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

class SyncUnits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:units';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Units';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    

    public function __construct(UnitsService $unitsService,Schools $schools,ClassesService $classesService) 
    {
        parent::__construct();

        $this->units=$unitsService;
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
        ini_set('max_execution_time', 1200);
        
        //更新現有單位狀態
        $existUnits=$this->units->getAll()->get();
        foreach($existUnits as $existUnit){
            //取得對應之學校單位資料
            $code=$existUnit->code;
            $schoolDepartment= $this->schools->getSchoolDepartmentByCode($code);
           
            if($schoolDepartment){
                $existUnit->active=$schoolDepartment->isActive();
            }else{
                $existUnit->active=false;
            }

            $existUnit->save();
        }

        //取得學校所有單位
        $allSchoolDepartments=$this->schools->getAllSchoolDepartments()->get();
       
        foreach($allSchoolDepartments as $schoolDepartment){
            if($schoolDepartment->isEmpty()) continue;
            if($schoolDepartment->isClass()) continue;
            if(!$schoolDepartment->isActive()) continue;

            $parent=$this->schools->getParentSchoolDepartment($schoolDepartment);
           
            $unitValues = Unit::initFromSchoolDepartment($schoolDepartment,$parent);


            $managerNumber=$this->schools->getStaffNumberBySID($schoolDepartment->getBoss());
            $unitValues['admin'] = $managerNumber;

            if($schoolDepartment->getLevel() == 1 ){
                $unitValues['level_ones'] = $managerNumber;
            }else{
                $unitValues['level_twos'] = $managerNumber;
            }

            $this->createOrUpdateUnit($unitValues);
            
        }
       
        Log::info('Sync Units Has Done.');
        $this->info('Sync Units Has Done.');
    }

    function createOrUpdateUnit(array $unitValues)
    {
        
        $code=$unitValues['code'];
        $unit=$this->units->getUnitByCode($code);
        if($unit){
            $unit->update($unitValues);
        }else{
            Unit::create($unitValues);
        }
    }
}
