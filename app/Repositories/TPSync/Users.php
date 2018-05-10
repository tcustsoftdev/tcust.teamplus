<?php

namespace App\Repositories\TPSync;

use DB;

use App\TPSync\StudentUpdateRecord;
use App\TPSync\StaffUpdateRecord;

use App\Teamplus\TPUser;
use App\Teamplus\TPDepartment;
use App\Teamplus\TPUserForSync;

use App\Support\Helper;
use Carbon\Carbon;

class Users 
{
    public function syncUsers()
    {
        $this->syncStaffs();  
        $this->syncStudents();  
    }
    public function syncStudents()
    {
        $records=StudentUpdateRecord::where('done', false )->get();
       
        foreach($records as $record){
               $number=$record->number;
               $password=$record->password;
               $email=$record->email;
               $name=$record->name;
               $class=$record->department;
               $status=$record->status;
               
               $msg=$this->syncUserFromStudent($number,$password, $email,$name, $class,$status);
               if($msg){
                    $record->success=false;
                    $record->msg=$msg;
               }else{
                    $record->success=true;
                    $record->msg='';
               }

               $record->done=true;
               $record->save();
               
        }
    }
    public function syncStaffs()
    {
        $records=StaffUpdateRecord::where('done', false )->get();
       
        foreach($records as $record){
               $number=$record->number;
               $password=$record->password;
               $email=$record->email;
               $name=$record->name;
               $department=$record->department;
               $job_title=$record->job_title;
               $extend=$record->extend;
               $status=$record->status;
               
               $msg=$this->syncUserFromStaff($number, $password,$email, $name, $department, $job_title, $extend  ,$status);
               
               if($msg){
                   $record->success=false;
                   $record->msg=$msg;
               }else{
                   $record->success=true;
                   $record->msg='';
               }

               $record->done=true;
               $record->save();
        }
    }
    
    public function syncUserFromStudent($number, $password ,$email, $name, $class,$status)
    {
       
        $code= $class;

        $tp_department=TPDepartment::where('Code',$code)->first();
        
        if($tp_department){
            $values=TPUserForSync::initialize();
            $values['LoginAccount']=$number;
            $values['Password']=$password;
            $values['Email']=$email;
            $values['EmpID']=$number;
            $values['Name']=$name;
            $values['DeptCode']=$tp_department->Code;
            $values['Status']=$status;
            
           
            $save = $this->saveUserForSync($values);
            if($save){
                return '';
            }else{
                return '新增UserForSync失敗';
            }
        
        }else{
            
            return '班級 ' . $code . ' 不存在';
        }

        
    }
    public function syncUserFromStaff($number, $password ,$email, $name, $department, $job_title, $extend  ,$status)
    {
        $code= $department;
        $tp_department=TPDepartment::where('Code',$code)->first();
       
        if($tp_department){
            $values=TPUserForSync::initialize();
            $values['LoginAccount']=$number;
            $values['Password']=$password;
            
            $values['Email']=$email;
            $values['EmpID']=$number;
            $values['Name']=$name;
            $values['DeptCode']=$tp_department->Code;
            $values['JobTitle']=$job_title;
            $values['Extend']=$extend;
            $values['Status']=$status;
            
            $save = $this->saveUserForSync($values);
            if($save){
                return '';
            }else{
                return '新增UserForSync失敗';
            }
            
        
        }else{
            return '部門 ' . $code . ' 不存在';
        }

        
        
    }

    public function syncUser($number, $password ,$email, $name, $departmentCode, $status)
    {
        $departmentCode=strtolower($departmentCode);

        $values=TPUserForSync::initialize();
        $values['LoginAccount']=$number;
        $values['Password']=$password;
        
        $values['Email']=$email;
        $values['EmpID']=$number;
        $values['Name']=$name;
        $values['DeptCode']= $departmentCode;
        
        $values['Status']=$status;
        
        return TPUserForSync::create($values);
        
        
    }

    private function saveUserForSync($values)
    {
        $exist_record=$this->existUserForSync($values['LoginAccount']);
        if($exist_record){
           return $exist_record->update($values);
        }else{
           return TPUserForSync::create($values);
        }
    }
  
    
    private function getTPDepartmentByName($name)
    {
         return TPDepartment::where('Name',$name)->first();
    }
    
    public function getTPUserByAccount($account)
    {
        return TPUser::where('LoginName',$account)->first();
    }

    public function isUserExist($account)
    {
         if($this->getTPUserByAccount($account)) return true;
         return false;
    }
   
    public function userExist($account)
    {
          return TPUser::where('LoginName',$account)->first();
    }

    public function existUserForSync($account)
    {
          return TPUserForSync::where('SyncStatus',0)
                             ->where('LoginAccount',$account)->first();
    }

    


    

     

    
   
   
    
}