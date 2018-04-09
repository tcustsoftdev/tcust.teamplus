<?php

namespace App\Services;

use App\Unit;
use App\Repositories\Teamplus\Groups;

class UnitsService
{
    public function __construct(Groups  $groups)
    {
        $this->groups=$groups;
        $this->company_admin=config('teamplus.system.company_admin');
    }

    public function getAll()    
    {
        return Unit::where('is_class', false)->where('removed',false);
    }
    public function getUnitByCode($code)
    {
        return $this->getAll()->where('code',$code)->first();
    }
    private function getParentId($parent_code)
    {
        $parent= Unit::where('code',$parent_code)->first();
        if($parent) return $parent->id;
        return 0;
    }

    public function rootUnits()
    {
         return $this->getAll()->where('active',true)
                               ->where('parent',0);                     
    }

    public function getChildrenIds($id)
    {
        $children=$this->getAll()->where('parent',$id)->get()->pluck('id')->toArray();
        if($children){
            $ids=$children;
            for($i = 0; $i < count($children); ++$i){
                $ids=array_merge($this->getChildrenIds($children[$i]),$ids);
            }
            return $ids;
        }else{
            return [];
        }
    }

    public function getTree()
    {
       
        $units=$this->getAll()
            //->where('code','!=', '102000')
            ->where('parent',0)
            ->select('name','code','id','parent')
            ->orderBy('code')
            ->get(); 
       
        if(count($units)){
            foreach ($units as $unit) {
               $unit->getChildren();
            }
        }

        return $units;
    }
    

    private function deleteUnit(GroupSync $unit)
    {
        $unit=$this->getUnitByCode($unit->code);
        if($unit){
            $values=[
                'removed' => 1,
            ];
            
            $unit->update($values);
        }

    }

    
    private function createUnit(GroupSync $unit)
    {
        $parent_code=$unit->parent;
        $parentId=0;
        if($parent_code){
            $parentId=$this->getParentId($parent_code);
         
            if(!$parentId) {
                return;
            }
          
        }

        Unit::create([
            'name' => $unit->name,
            'code' => $unit->code,
            'parent' => $parentId,
            'is_class' => false,
            'level_ones' => $unit->level_ones,
            'level_twos' => $unit->level_twos,
        ]);

    }
    private function updateUnit($entity ,GroupSync $unit)
    {
       
        $parent_code=$unit->parent;
       
        $parentId=0;
        if($parent_code){
            $parentId=$this->getParentId($parent_code);
          
            if(!$parentId) {
              
                return;
            }
          
        }

        $entity->update([
            'name' => $unit->name,
            'code' => $unit->code,
            'parent' => $parentId,
            'level_ones' => $unit->level_ones,
            'level_twos' => $unit->level_twos,
        ]);
       
    }


    //syncGroup

    public function syncGroup(Unit $unit)
    {
        if($unit->tp_id)
        {
            $this->updateGroup($unit);

        }else{
            
            $this->createGroup($unit);
        }
    }



    private function createGroup(Unit $unit)
    {
        $members=$unit->users->pluck('number')->toArray();
       
        $owner='';
        $manager=$unit->admin;
      
        $name=$unit->name;
        $teamSN=$this->groups->create($members, $owner, $manager ,$name);

        if((int)$teamSN > 0) {
            $unit->tp_id=$teamSN;
            $unit->save();
        }

    }
    private function updateGroup(Unit $unit)
    {
       
        $team_id=$unit->tp_id;
        $members=$unit->users->pluck('number')->toArray();
     
        $owner='';
        $manager=$unit->admin;
       
        $name=$unit->name;
        
        $this->updateGroupName($team_id , $name);
        
        $result=$this->groups->details($team_id);
        
        $teamInfo=$result->TeamInfo;

        $result=$this->updateGroupManager($unit,$team_id,$manager,$teamInfo);
        if(!$result) return;
       
       
        $result=$this->updateGroupMembers($unit,$members,$team_id,$teamInfo);

    }

    private function updateGroupMembers(Unit $unit,array $members,$team_id,$teamInfo)
    {
      
        $old_member_list=$teamInfo->MemberList;
       
        $need_to_remove=array_diff($old_member_list, $members);
       
        $need_to_add=array_diff($members, $old_member_list);
       
        
        if(count($need_to_remove)){
            $need_to_remove=array_flatten($need_to_remove);
            $result=$this->groups->removeMembers($need_to_remove,$team_id);
            if(!$result->IsSuccess)
            {
                return false;
            }
        }
        
        
        if(count($need_to_add)){
            $need_to_add=array_flatten($need_to_add);
            $result=$this->groups->addMembers($need_to_add,$team_id);
            if(!$result->IsSuccess)
            {
                return false;
            }
        }
       

        return true;
    }

    private function updateGroupManager(Unit $unit,$team_id,$manager,$teamInfo)
    {
        $managerList=$teamInfo->ManagerList;
        
        $exist=in_array($manager, $managerList);
        
     
        if(!$exist){
            $result=$this->groups->addManager($team_id,$manager);
            if(!$result->IsSuccess)
            {
                return false;
            }
        }

        for($i = 0; $i < count($managerList); ++$i) {
            if($managerList[$i] !=$manager && $managerList[$i] !=$this->company_admin){
                
                $this->groups->removeManager($team_id,$managerList[$i]);
            }
           
        }

        return true;
        
    }

    

    private function updateGroupName($team_id , $name)
    {
        $manager='';
        $result=$this->groups->update($team_id ,$manager, $name);
       
    }
    
    
}