<?php

namespace App\Services;

use App\TPSync\GroupSync;
use App\Unit;

class UnitsService
{
    public function getAll()
    {
        return Unit::where('is_class', false)->where('removed',false);
    }
    public function getUnitByCode($code)
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
            ->where('code','!=', '102000')
            ->where('active',true)
            ->where('parent',0)
            ->orderBy('order','desc')
            ->orderBy('updated_at','desc')
            ->get();  
        if(count($units)){
            foreach ($units as $unit) {
               $unit->getChildren();
            }
        }

        return $units;
    }


    private function get_need_sync_list()
    {
        $all=GroupSync::orderBy('parent')->get();
        $need_sync_list=[];
        foreach($all as $record)
        {
            if(is_numeric($record->code))
            {
               array_push($need_sync_list, $record);
            }
        }

        return $need_sync_list;
    }

    public function syncUnits()
    {
        $need_sync_list=$this->get_need_sync_list();
       
        if(count($need_sync_list))
        {
            foreach($need_sync_list as $record)
            {
                if($record->isdelete){
                    $this->deleteUnit($record);
                }else{
                    $exist=$this->getUnitByCode($record->code);
                    if($exist){
                        $this->updateUnit($exist,$record);
                    }else{
                        $this->createUnit($record);
                    }
                }
            }

        }
    }

    private function deleteUnit(GroupSync $record)
    {
        $unit=$this->getUnitByCode($record->code);
        if($unit){
            $values=[
                'removed' => 1,
            ];
            
            $unit->update($values);
        }

    }

    
    private function createUnit(GroupSync $record)
    {
        $parent_code=$record->parent;
        $parentId=0;
        if($parent_code){
            $parentId=$this->getParentId($parent_code);
         
            if(!$parentId) {
                Log::info('createUnit Failed. parent not found parent_code=' . $parent_code);
                return;
            }
          
        }


        Unit::create([
            'name' => $record->name,
            'code' => $record->code,
            'parent' => $parentId,
            'is_class' => false
        ]);

    }
    private function updateUnit($entity ,GroupSync $record)
    {
        $parent_code=$record->parent;
       
        $parentId=0;
        if($parent_code){
            $parentId=$this->getParentId($parent_code);
         
            if(!$parentId) {
                Log::info('UpdateUnit Failed. parent not found parent_code=' . $parent_code);
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