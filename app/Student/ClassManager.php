<?php

namespace App\Student;

use App\Student\BaseStudentModel;
use Carbon\Carbon;

class ClassManager extends BaseStudentModel
{
    protected $table = 'clstrtb';
    protected $primaryKey = 'frowno';
    
    public function getTeacherSID()
    {
        if($this->ftrnm)  return trim($this->ftrnm);
        return $this->ftrnm;
        
    }
	
}
