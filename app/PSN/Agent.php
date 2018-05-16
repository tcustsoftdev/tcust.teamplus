<?php

namespace App\PSN;

use App\PSN\BasePSNModel;
use Carbon\Carbon;

class Agent extends BasePSNModel
{
	
    protected $table = 'PnDayAgntTb';
    protected $primaryKey = 'fRowNo';

    public function getAgentPerson()
    {
        if($this->fAgntPsn) return trim($this->fAgntPsn);
        return $this->fAgntPsn;
    }

    
	
}
