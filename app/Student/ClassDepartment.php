<?php

namespace App\Student;

use App\Student\BaseStudentModel;
use Carbon\Carbon;

class ClassDepartment extends BaseStudentModel
{
    protected $table = 'TdDptmTb';
    protected $primaryKey = 'fRowNo';
    
	public function getCode()
    {
        if($this->fDptNo) return trim($this->fDptNo);
        return $this->fDptNo;
    }

    public function hasCode()
    {
        $code=$this->getCode();
        if($code) return true;
        return false;
    }

   
	
}
