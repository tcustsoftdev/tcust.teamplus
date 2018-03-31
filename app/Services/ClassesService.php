<?php

namespace App\Services;

use App\TPSync\GroupSync;
use App\Unit;

class ClassesService
{
    public function getAll()
    {
        return Unit::where('is_class', true)->where('removed',false);
    }
    public function getClassByCode($code)
    {
        $code=strtolower($code);
        return $this->getAll()->where('code',$code)->first();
    }
    private function getParentId($parent_code)
    {
        $parent= Unit::where('code',$code)->first();
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
                Log::info('CreateClass Failed. parent not found parent_code=' . $parent_code);
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
                Log::info('UpdateClass Failed. parent not found parent_code=' . $parent_code);
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