<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\PSN\SchoolDepartment;
use App\PSN\Staff;
use App\Services\UnitsService;
use App\Services\ClassesService;
use App\Unit;
use App\User;
use App\Student\Student;
use App\Repositories\Schools;
use App\Repositories\TPSync\Departments;
use App\Repositories\TPSync\Users;
use App\Teamplus\TPUserForSync;
use App\Repositories\Teamplus\Groups;

class TestController extends Controller
{
   
    public function __construct(UnitsService $unitsService,Schools $schools,ClassesService $classesService,
            Departments $departments, Users $TPUsers, Groups  $groups) 
    {
       
        $this->units=$unitsService;
        $this->classesService=$classesService;
        $this->schools=$schools;
        $this->departments=$departments;
        $this->TPUsers=$TPUsers;
        $this->groups=$groups;

        $this->company_admin=config('teamplus.system.company_admin');
        
       
    }

    public function test()
    {
        ini_set('max_execution_time', 1200);
       
        //更新現有職員與教師狀態
        $roles=[ User::staffRoleName(), User::teacherRoleName() ];
        $existStaffUsers=User::whereIn('role',$roles)->get();
        foreach($existStaffUsers as $staffUser){
            $this->updateStaffStatus($staffUser);
        }

        $allUnits = $this->units->getAll()->get();
        foreach($allUnits as $unit){
            
            //取得學校職員資料
            $staffsInUnit=$this->schools->getStaffsByUnit($unit->code)->get();

            foreach($staffsInUnit as $schoolStaff){
                if($schoolStaff->isActive()){
                    $this->syncSchoolStaff($schoolStaff,$unit);
                }

            }
            
        }
        
        
        
    }

    function syncSchoolStaff($schoolStaff,$unit)
    {
        $userValues=User::initFromStaff($schoolStaff,$schoolStaff->getRole());
        $userValues['unit_id'] = $unit->id;
        $userValues['active'] = true;
        $this->createOrUpdateUser($userValues);
    }


    function updateStaffStatus($staffUser)
    {
        //取得對應之學校職員/教師資料
        $staffNumber=$staffUser->number;
        $schoolStaff= $this->schools->getStaffByNumber($staffNumber);
        if($schoolStaff){
            $staffUser->active=$schoolStaff->isActive();
        }else{
            $staffUser->active=false;
        }

        $staffUser->save();
    }

    function createOrUpdateUser(array $userValues)
    {
        $number=$userValues['number'];
        $user=User::where('number',$number)->first();
        if($user){
            $user->update($userValues);
        }else{
           
            $userValues['password'] = config('app.auth.password');
            User::create($userValues);
        }
    }

   

    
   
    
    
   
}
