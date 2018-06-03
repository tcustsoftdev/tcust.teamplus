<?php

namespace App\PSN;

use App\PSN\BasePSNModel;
use Carbon\Carbon;
use App\User;

class Staff extends BasePSNModel
{
	
    protected $table = 'PSNtb';
    protected $primaryKey = 'fRowId';
	
	public function getRole()
    {
        $type= strtolower(trim($this->fPsnType));
        if($type=='sa') return User::staffRoleName();  //正式職員
        if($type=='ss') return User::staffRoleName();  //約聘職員

        if($type=='st') return User::teacherRoleName();  //正式教師
        if($type=='sp') return User::teacherRoleName();  //約聘教師

        if($type=='sc') return User::teacherRoleName();  //約聘教師
        if($type=='si') return User::staffRoleName();   //職員
        
        
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
    public function getSID()
    {
        if($this->fPsnId) return trim($this->fPsnId);
        return $this->fPsnId;
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
