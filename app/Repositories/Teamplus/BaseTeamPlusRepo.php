<?php

namespace App\Repositories\Teamplus;
use Config;

class BaseTeamPlusRepo
{
    protected $api_url;
    public function __construct()
    {
        $this->api_url=Config::get('teamplus.main.api_url');
        
	}
    
   
}
