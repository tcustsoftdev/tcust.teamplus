<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Unit;
use App\User;
use App\Services\UnitsService;
use App\Services\Teamplus\GroupService;
use App\Repositories\Teamplus\Groups;
use Log;

class SyncGroups extends Command
{
    
    protected $signature = 'sync:groups';

   
    protected $description = 'Sync Groups';

    
    public function __construct(UnitsService $unitsService,Groups  $groups)
    {
        parent::__construct();

        $this->units=$unitsService;
        $this->groups=$groups;

        $this->company_admin=config('teamplus.system.company_admin');
      
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        $allUnits=Unit::all();
        foreach($allUnits as $unit){
            if((int)$unit->tp_id){
                $this->updateGroup($unit);
            }else{
                $this->createGroup($unit);
            }
        }

        $text='Sync Groups Has Done.';
        Log::info($text);
       
        $this->info($text);
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
