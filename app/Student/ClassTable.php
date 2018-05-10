<?php

namespace App\Student;

use App\Student\BaseStudentModel;
use Carbon\Carbon;

class ClassTable extends BaseStudentModel
{
    protected $table = 'TdClsTb';
    protected $primaryKey = 'fRowNo';
    
	public function getCode()
    {
        if($this->fClsId) return trim($this->fClsId);
        return $this->fClsId;
    }

    public function getName()
    {
        if($this->fClsNm) return trim($this->fClsNm);
        return $this->fClsNm;
    }
    
    public function getParentCode()
    {
        if($this->fDptNo) return trim($this->fDptNo);
        return $this->fDptNo;

    }

    public function isActive()
    {
       
        if(!$this->fEday) return true;
        return (int)$this->fEday < 1 ;
    }

    public function getStatus()
    {
        return $this->fEDay;
    }
    
    public function getMembers()
    {
        return [];
    }
    
	public function getBoss()
    {
        return null;
    }
	
}
