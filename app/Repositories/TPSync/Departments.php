<?php

namespace App\Repositories\TPSync;

use DB;
use App\TPSync\GroupSync;

use App\Teamplus\TPDepartment;
use App\Teamplus\TPDepartmentForSync;
use App\Support\Helper;
use Carbon\Carbon;

class Departments 
{
    

    public function syncDepartments()
    {
        $records=GroupSync::orderBy('parent')->get();
       
        foreach($records as $record){
               $parent_code='';
               $parent_checked=true;
               if($record->parent){
                   $parent_code=strtolower($record->parent);
                   $parent_department=$this->getTPDepartmentByCode($parent_code);
                   if(!$parent_department)  $parent_checked=false;
               }
               if(!$parent_checked){
                   $record->msg ='父部門 ' . $record->parent . ' 不存在'; 
                
                   $record->success=false;
                   $record->save();                   
               }else{
                    
                    $code=strtolower($record->code);
                    $name=$record->name;
                    $delete=$record->is_delete;
                   
                    $saved= $this->syncDepartment($name, $code,$parent_code,$delete);
                    if($saved){
                        $record->msg ='';
                    
                        $record->success=true;
                        $record->save();
                    }else{
                        $record->msg ='新增DepartmentForSync失敗';
                      
                        $record->success=false;
                        $record->save();
                    }
               }


               
        }
        
    }

    
   
    public function syncDepartment($name, $code,$parent_code,$delete)
    {
        $values=TPDepartmentForSync::initialize();
        $values['Name']=$name;
        $values['Code']=$code;
        $values['ParentCode']=$parent_code;
        $values['IsDelete']=$delete;
        
        return $this->saveDepartmentForSync($values);
    }
    private function saveDepartmentForSync($values)
    {
       
        $exist_record=$this->existDepartmentForSync($values['Name']);
      
        if($exist_record){
           return  $exist_record->update($values);
        }else{
           return TPDepartmentForSync::create($values);
        }
    }
    
    public function existDepartmentForSync($code)
    {
          return TPDepartmentForSync::where('SyncStatus',0)
                             ->where('Code',strtolower($code))->first();
    }
  

    public function getTPDepartmentByCode($code)
    {
         return TPDepartment::where('Code',strtolower($code))->first();
    }
    
}