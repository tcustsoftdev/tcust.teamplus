<?php

namespace App\PSN;

use App\PSN\BasePSNModel;
use Carbon\Carbon;
use App\Student\ClassDepartment;
use App\PSN\Staff;

class SchoolDepartment extends BasePSNModel
{
	
    protected $table = 'DptTb';
    protected $primaryKey = 'fRowId';

    
    public function isEmpty()
    {
        $boss=$this->getBoss();
        if(!$boss) return true;
        return false;
    }

    public function isClass()
    {
        $code=(int)$this->getCode();
       
        if($code) return false;
        return true;

    }
	
    public function getCode()
    {
        return $this->fDptNo;
    }

    public function getName()
    {
        if($this->fDptNm) return trim($this->fDptNm);
        return $this->fDptNm;
    }


    public function getParentCode()
    {
        $code=$this->getCode();
        if($code=='102000') return '';
        
       
        $levelOneCode=$this->levelOneCode();

        if($levelOneCode==$code) return '';
        return $levelOneCode;
    }
    public function getLevel()
    {
        $code=$this->getCode();
        if($code=='102000') return 1;
        
       
        $levelOneCode=$this->levelOneCode();

        if($levelOneCode==$code) return 1;
        return 2;
    }
    

    public function levelOneCode()
    {
        return $this->fLvl1Dpt;
    }

    public function getStatus()
    {
        return $this->fEDay;
    }

    public function isActive()
    {
        return (int)$this->getStatus() < 1 ;
    }
    
    public function getMembers()
    {
        return Staff::where('fEmplDpt',$this->getCode());
    }
    
	public function getBoss()
    {
        if($this->fManager) return trim($this->fManager);
        return $this->fManager;
    }

    public function getAgentSIDs()
    {
        $agents=[];
        if(trim($this->fAgnt0))  array_push($agents,trim($this->fAgnt0)); 
        // if(trim($this->fAgnt1))  array_push($agents,trim($this->fAgnt1)); 
        // if(trim($this->fAgnt2))  array_push($agents,trim($this->fAgnt2)); 
        // if(trim($this->fAgnt3)) array_push($agents,trim($this->fAgnt3)); 
        // if(trim($this->fAgnt4)) array_push($agents,trim($this->fAgnt4));

        return $agents;
        
    }

    public function getLevelOnes()
    {
        return [];
    }
    public function getLevelTwos()
    {
        return [];
    }

    public function getTeachers()
    {
        return [];
    }

    public function getStaffs()
    {
        return [];
    }
	
}
