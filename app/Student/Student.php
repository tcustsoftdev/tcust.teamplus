<?php

namespace App\Student;

use App\Student\BaseStudentModel;
use App\Student\StudentProfile;
use Carbon\Carbon;

class Student extends BaseStudentModel
{
    protected $table = 'TdStudentTb';
    protected $primaryKey = 'fRowNo';
    
    public function getProfile()
    {
        return StudentProfile::where('fID',$this->getSID())->first();
    }

    public function getName()
    {
        if($this->fName) return trim($this->fName);
        return $this->fName;
    }

    public function getSID()
    {
        if($this->fID) return trim($this->fID);
        return $this->fID;
    }

    public function getCode()
    {
        if($this->fScNo) return trim($this->fScNo);
        return $this->fScNo;
    }

    public function getNumber() 
    {
        return $this->getCode();
    }

    public function getEmail()
    {
        if($this->getCode()) return $this->getCode() . '@ems.tcust.edu.tw';
        return '';
    }

	public function getStatus()
    {
        return $this->fSTATUS;
    }

    public function isActive()
    {
        return (int)$this->getStatus() == 1 ;
    }

    public function getClassCode() 
    {
        if($this->fClsId) return trim($this->fClsId);
        return $this->fClsId;
    }

    public function hasClassCode() 
    {
        if($this->getClassCode()) return true;
        return false;
    }

    
   
	
}
