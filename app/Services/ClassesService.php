<?php

namespace App\Services;

use App\TPSync\GroupSync;
use App\Unit;
use App\User;

class ClassesService
{
    public function getAll()
    {
        return Unit::where('is_class', true)->where('removed',false);
    }
    public function getClassByCode($code)
    {
        return $this->getAll()->where('code', strtolower($code))->first();
    }
    public function getClassesByCodes(array $codes)
    {
        $codes=array_map(function($item){
            return strtolower($item);
        }, $codes);
        return $this->getAll()->whereIn('code', $codes);
    }

    public function getStudentsByClassIds(array $classIds)
    {
        $roleName=User::studentRoleName();
        
        return User::whereIn('unit_id',$classIds)->where('role',$roleName);
    }

    public function rootUnits()
    {
         return $this->getAll()->where('active',true)
                               ->where('parent',0);                     
    }

    public function getTree()
    {
       
        $parentIds=array_unique($this->getAll()->pluck('parent')->toArray());
        $parents=Unit::whereIn('id',$parentIds)->get();
        
            
       
        if(count($parents)){
            foreach ($parents as $parent) {
               $parent->getChildren(true,true);
            }
        }

        return $parents;
    }


    private function getParentId($parent_code)
    {
        $parent= $this->getUnitByCode($parent_code);
        if($parent) return $parent->id;
        return 0;
    }
    private function get_need_sync_list()
    {
        $all=GroupSync::orderBy('parent')->get();
        $need_sync_list=[];
        foreach($all as $record)
        {
            if(!is_numeric($record->code))
            {
               array_push($need_sync_list, $record);
            }
        }

        return $need_sync_list;
    }

    public function syncClasses()
    {
        $need_sync_list=$this->get_need_sync_list();
       
        if(count($need_sync_list))
        {
            foreach($need_sync_list as $record)
            {
                if($record->isdelete){
                   
                    $this->deleteClass($record);
                }else{
                    $exist=$this->getClassByCode($record->code);
                    
                    if($exist){
                        $this->updateClass($exist,$record);
                    }else{
                      
                        $this->createClass($record);
                    }
                }
            }

        }
    }

    private function deleteClass(GroupSync $record)
    {
        $exist=$this->getClassByCode($code);
        if($exist){
            $values=[
                'removed' => 1,
            ];
            
            $exist->update($values);
        }

    }

    
    private function createClass(GroupSync $record)
    {
        $parent_code=$record->parent;
        $parentId=0;
        if($parent_code){
            $parentId=$this->getParentId($parent_code);
         
            if(!$parentId) {
                
                return;
            }
          
        }


        Unit::create([
            'name' => $record->name,
            'code' => $record->code,
            'parent' => $parentId,
            'is_class' => true
        ]);

     

    }
    private function updateClass($entity ,GroupSync $record)
    {
        $parent_code=$record->parent;
       
        $parentId=0;
        
        if($parent_code){
            $parentId=$this->getParentId($parent_code);
           
            if(!$parentId) {
               
                return;
            }
          
        }

        $entity->update([
            'name' => $record->name,
            'code' => $record->code,
            'parent' => $parentId,
        ]);
       
    }

    
    
    
}