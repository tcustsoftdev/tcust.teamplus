<?php

namespace App\Repositories;

use App\PSN\Staff;
use App\PSN\SchoolDepartment;
use App\Student\ClassTable;
use App\Student\ClassDepartment;
use App\Student\Student;
use App\Student\ClassManager;

use App\Support\Helper;
use Carbon\Carbon;

class Schools 
{
    public function __construct() 
    {
       
    } 
    public function getAllSchoolDepartments()
    {
        return SchoolDepartment::where('fLvl0Dpt','102000');
    }

    public function getSchoolDepartmentByCode($code)
    {
        return $this->getAllSchoolDepartments()->where('fDptNo',$code)->first();
    }

    public function getParentSchoolDepartment($department)
    {
        $parentCode=$department->getParentCode();
        if(!$parentCode) return null;
        return $this->getSchoolDepartmentByCode($parentCode);
    }

    public function getDepartmentSubsByCode($departmentCode)
    {
        $department=$this->getSchoolDepartmentByCode($departmentCode);
        $agentSIDs=$department->getAgentSIDs();
       
        return $this->getStaffNumbersBySIDs($agentSIDs);
    }

    public function getParentClassDepartment(ClassTable $classTable)
    {
        $parentCode=$classTable->getParentCode();
        if(!$parentCode) return null;
        
        $classDepartments=ClassDepartment::all();
        $classDepartments = $classDepartments->filter(function ($item) {
            return $item->hasCode();
        })->values();

        $parent = $classDepartments->filter(function($item) use($parentCode) {
            return trim($item->fDptmNo) == $parentCode;
        })->first();

        if(!$parent)  return null;

        return $this->getSchoolDepartmentByCode($parent->getCode());
    }


    public  function getStaffBySID($sid)
    {
        return Staff::where('fPsnId',$sid)->first();
    }

    public  function getStaffsBySIDs(array $sids)
    {
        return Staff::whereIn('fPsnId',$sids)->get();
    }

    public  function getStaffByNumber($number)
    {
        return Staff::where('fPsnMail',$number)->first();
    }

    public  function getStaffNumberBySID($sid)
    {
        $staff=$this->getStaffBySID($sid);
      
        if($staff) return $staff->getNumber();
        return '';
    }

    public  function getStaffNumbersBySIDs(array $sids)
    {
        $staffs=$this->getStaffsBySIDs($sids);

       
        return $staffs->map(function ($item) {
            return $item->getNumber();
        })->all();
        
    }


    public function getAllSchoolClasses()
    {
        return ClassTable::all();       
    }

    public function getSchoolClassByCode($code)
    {
        $code=strtoupper($code);
        return ClassTable::where('fClsId', $code)->first();       
    }

    public function getStaffsByUnit($code)
    {
        
        return Staff::where('fEmplDpt', strtoupper($code));
    }

    public function getStudentsByClass($classCode)
    {
        return Student::where('fClsId', strtoupper($classCode));
    }
    public function getStudentByNumber($number)
    {
        return Student::where('fScNo', $number)->first();
    }

    public function getClassTeacher($classCode)
    {
        
        $sid=$this->getClassTeacherSID($classCode);
        if(!$sid) return null;
       
        return $this->getStaffBySID($sid);
    }

    function getClassTeacherSID($classCode)
    {
       
        $classManager= ClassManager::where('fclsid', strtoupper($classCode))
                                    ->orderBy('frowno','desc')->first();
        if(!$classManager) return '';                            
        return $classManager->getTeacherSID();
    }

    

   
    
}