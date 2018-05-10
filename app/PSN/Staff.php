<?php

namespace App\PSN;

use App\PSN\BasePSNModel;
use Carbon\Carbon;

class Staff extends BasePSNModel
{
	
    protected $table = 'PSNtb';
    protected $primaryKey = 'fRowId';
	
	public function getRole()
    {
        $type= strtolower(trim($this->fPsnType));
        if($type=='sa') return 'Staff';  //正式職員
        if($type=='ss') return 'Staff';  //約聘職員

        if($type=='st') return 'Teacher';  //正式教師
        if($type=='sp') return 'Teacher';  //約聘教師

        if($type=='sc') return 'Teacher';  //約聘教師
        if($type=='si') return 'Staff';  //職員
        
        
    }

    public function getName()
    {
        if($this->fcName) return trim($this->fcName);
        return $this->fcName;
    }
    public function getDOB()
    {
        if($this->fBirthday) return trim($this->fBirthday);
        return $this->fBirthday;
    }

    public function getCode()
    {
        return $this->getNumber();
    }

    public function getNumber() 
    {
        if($this->fPsnMail) return trim($this->fPsnMail);
        return $this->fPsnMail;
    }
    public function getEmail() 
    {
        if($this->getNumber()) return $this->getNumber() . '@tcust.edu.tw';
        return '';
    }
    public function getStatus()
    {
        return $this->fEmplStatus;
    }
    public function isActive()
    {
        return (int)$this->getStatus() == 1 ;
    }

    //代理人
    public function getSubs() 
    {
        return [];
    }

    
	
}
