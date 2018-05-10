<?php

namespace App\Student;

use App\Student\BaseStudentModel;
use Carbon\Carbon;

class StudentProfile extends BaseStudentModel
{
    protected $table = 'TdSTbaseTb';
    protected $primaryKey = 'fRowNo';
    
    public function getDOB()
    {
        if($this->fBIRTH) return trim($this->fBIRTH);
        return $this->fBIRTH;
    }

    public function getName()
    {
        if($this->fNAME) return trim($this->fNAME);
        return $this->fNAME;
    }

	public function getStatus()
    {
        return 1;
    }

    public function isActive()
    {
        return true;
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

    public function getNumber() 
    {
        return $this->fPSNMail;
    }
    
}
