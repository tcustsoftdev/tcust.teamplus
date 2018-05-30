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
        //$existStaffUsers=User::whereIn('role',['Staff','Teacher'])->get();

        //$user=User::find(3226);

        //$this->syncActive([$user]);
       
         //$this->syncActive($existStaffUsers);
         //$this->syncStaffsByUnits();
       
        
        
    }

    function syncActive($users)
    {
        foreach($users as $staffUser){

           
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

    }

    function syncStaffsByUnits()
    {
        //$allUnits = $this->units->getAll()->get();

        $unit=Unit::where('code','118000')->first();
       
        $allUnits=[$unit];
        foreach($allUnits as $unit){
            
            //取得學校職員資料
            $staffsInUnit=$this->schools->getStaffsByUnit($unit->code)->get();
          
           
            $staffsInUnit = $staffsInUnit->filter(function ($item) {
                return $item->isActive();
            })->all();
           

            foreach($staffsInUnit as $schoolStaff){
                
                $userValues=User::initFromStaff($schoolStaff,$schoolStaff->getRole());
                $userValues['unit_id'] = $unit->id;

                $this->createOrUpdateUser($userValues);

            }
            
        }
    }

    public function syncGroups()
    {
        
        $allUnits=Unit::all();
        foreach($allUnits as $unit){
            if((int)$unit->tp_id){
                $this->updateGroup($unit);
            }else{
                $this->createGroup($unit);
            }
        }
    }

    function createGroup(Unit $unit)
    {
        $activeOnly=true;
        $members =$this->membersToLower($unit->getMembers($activeOnly));   

        $owner ='';
        $manager = $unit->admin;

        $name=$unit->name;
        $TeamSN=$this->groups->create($members, $owner, $manager ,$name);

        $unit->tp_id=$TeamSN;
        $unit->save();
        
    }

    function updateGroup(Unit $unit)
    {
        $activeOnly=true;
        $members =$this->membersToLower($unit->getMembers($activeOnly));   
       
        $owner='';
        $manager = $unit->admin;
        $name=$unit->name;
        $team_id=$unit->tp_id;
        
        $this->updateGroupName($unit,$team_id , $name);
        
        $result=$this->groups->details($team_id);
        $teamInfo=$result->TeamInfo;

        $this->updateGroupManager($unit,$team_id,$manager,$teamInfo);
       
        $this->updateGroupMembers($unit,$members,$team_id,$teamInfo);

    }

    function updateGroupName(Unit $unit,$team_id ,$name)
    {
        $result=$this->groups->update($team_id ,$name);
        
        
    }

    function membersToLower(array $members)
    {
        return array_map(function($item){
                            return strtolower($item);
                        }, $members);
    }

    function updateGroupManager(Unit $unit,$team_id,$manager,$teamInfo)
    {
        $managerList=$teamInfo->ManagerList;
        $managerList=$this->membersToLower($managerList);  
        
        
        
        $exist=in_array($manager, $managerList);
     
        if(!$exist){
            $result=$this->groups->addManager($team_id,$manager);
        }
        

        for($i = 0; $i < count($managerList); ++$i) {
            if($managerList[$i] !=$manager && $managerList[$i] !=$this->company_admin){
                $this->groups->removeManager($team_id,$managerList[$i]);
            }
           
        }

        return true;
        
    }

    function updateGroupMembers(Unit $unit ,array $members,$team_id,$teamInfo)
    {
        
        if(!in_array($this->company_admin, $members)){
            array_push($members,$this->company_admin);
        }

        $old_member_list=$teamInfo->MemberList;
       
       
        $need_to_remove=array_diff($old_member_list, $members);
        
        $need_to_add=array_diff($members, $old_member_list);
        
        if(count($need_to_remove)){
            $need_to_remove=array_flatten($need_to_remove);
           
            $result=$this->groups->removeMembers($need_to_remove,$team_id);
           
        }
        
        
        if(count($need_to_add)){
            $need_to_add=array_flatten($need_to_add);
            $result=$this->groups->addMembers($need_to_add,$team_id);
            
            
        }
       
    }

   

    
   
    
    
   
}
