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
        
        
        
    }

    
   
    function syncSchoolStudent($schoolStudent,$classEntity)
    {
        $userValues=User::initFromSchoolStudent($schoolStudent);
        $userValues['unit_id'] = $classEntity->id;
        $userValues['active'] = true;
        $this->createOrUpdateUser($userValues);
    }

    function updateStudentStatus($studentUser)
    {
        //取得對應之學校學生資料
        $studentNumber=$studentUser->number;
        $schoolStudent= $this->schools->getStudentByNumber($studentNumber);
        if($schoolStudent){
            $studentUser->active=$schoolStudent->isActive();
        }else{
            $studentUser->active=false;
        }

        $studentUser->save();
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
