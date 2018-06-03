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
       
        $roles=[ \App\User::teacherRoleName(), \App\User::staffRoleName()];
        dd($roles);
        
        
        
    }

    

   

    
   
    
    
   
}
