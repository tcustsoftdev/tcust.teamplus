<?php

namespace App\Services\Teamplus;

use App\Repositories\Teamplus\Groups;
use App\TPSync\GroupSync;

use Config;

class GroupService
{
 
    public function __construct(Groups  $groups)
    {
        $this->groups=$groups;
        $this->system_admin=Config::get('teamplus.system.system_admin');
    }

    public function syncGroups()
    {
        $need_sync_list=GroupSync::where('sync',false)->get();
      
        if(count($need_sync_list))
        {
            foreach($need_sync_list as $record)
            {

                if((int)$record->tp_id)
                {
                    $this->updateGroup($record);

                }else{
                   
                    $this->createGroup($record);
                }
                
            }

        }
    }

    
    private function createGroup(GroupSync $record)
    {
        $members=explode(',',strtolower($record->getMembers())); 
        $owner='';
        $manager=strtolower($record->admin);
        $name=$record->name;
        $result=$this->groups->create($members, $owner, $manager ,$name);

        if($result->IsSuccess)
        {
            $record->success=true;
            $record->msg='';
            $record->tp_id= $result->TeamSN;

        }else{
            $record->success=false;
            $record->msg=$result->Description;
        }

        $record->sync=true;
        $record->save();

    }
    private function updateGroup(GroupSync $record)
    {
       
        $team_id=(int)$record->tp_id;
        if(!$team_id){
            $this->noneTeamError($record);
            return;
        }

        $members=explode(',',strtolower($record->getMembers())); 
       
        $owner='';
        $manager=strtolower($record->admin);
       
        $name=$record->name;
        
        $this->updateGroupName($record,$team_id ,$manager, $name);
        
        $result=$this->groups->details($team_id);
        
        if(!$result->IsSuccess)
        {
            $record->success=false;
            $record->msg='取得GroupInfo失敗 ' . $result->Description;
            $record->sync=true;
            $record->save();

            return;
        }

        $teamInfo=$result->TeamInfo;

        $result=$this->updateGroupManager($record,$team_id,$manager,$teamInfo);
        if(!$result) return;
       
       
        $result=$this->updateGroupMembers($record,$members,$team_id,$teamInfo);

    }

    private function updateGroupMembers(GroupSync $record,array $members,$team_id,$teamInfo)
    {
        $old_member_list=$teamInfo->MemberList;
        
        $need_to_remove=array_diff($old_member_list, $members);
       
        $need_to_add=array_diff($members, $old_member_list);
        
        if(count($need_to_remove)){
            $need_to_remove=array_flatten($need_to_remove);
            $result=$this->groups->removeMembers($need_to_remove,$team_id);
            if(!$result->IsSuccess)
            {
                $record->success=false;
                $record->msg='移除GroupMember失敗 ' . $result->Description;
                $record->sync=true;
                $record->save();
    
                return false;
            }
        }
        
        
        if(count($need_to_add)){
            $need_to_add=array_flatten($need_to_add);
            $result=$this->groups->addMembers($need_to_add,$team_id);
            if(!$result->IsSuccess)
            {
                $record->success=false;
                $record->msg='新增GroupMember失敗 ' . $result->Description;
                $record->sync=true;
                $record->save();
    
                return false;
            }
        }
       

        return true;
    }

    private function updateGroupManager(GroupSync $record,$team_id,$manager,$teamInfo)
    {
        $managerList=$teamInfo->ManagerList;
        $exist=in_array($manager, $managerList);
     
        if(!$exist){
            $result=$this->groups->addManager($team_id,$manager);
            if(!$result->IsSuccess)
            {
                $record->success=false;
                $record->msg='加入管理者失敗 ' . $result->Description;
                $record->sync=true;
                $record->save();
    
                return false;
            }
        }

        for($i = 0; $i < count($managerList); ++$i) {
            if($managerList[$i] !=$manager && $managerList[$i] !=$this->system_admin){
                
                $this->groups->removeManager($team_id,$managerList[$i]);
            }
           
        }

        return true;
        
    }

    

    private function updateGroupName(GroupSync $record,$team_id ,$manager, $name)
    {
        $result=$this->groups->update($team_id ,$manager, $name);
       
        if($result->IsSuccess)
        {
            $record->success=true;
            $record->msg='';
        }else{
            $record->success=false;
            $record->msg=$result->Description;
        }

        $record->sync=true;
        $record->save();
    }

    private function noneTeamError($record)
    {
        $record->success=false;
        $record->msg='team_id = 0';
        $record->sync=true;
        $record->save();
       
    }
    
    
    
}