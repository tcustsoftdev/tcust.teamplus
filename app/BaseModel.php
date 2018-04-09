<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BaseModel extends Model
{
	protected $connection = 'sqlsrv_app';

	
	public function getCreatedAtAttribute($attr) {        
        return Carbon::parse($attr)->format('Y-m-d - h:i'); //Change the format to whichever you desire
    }
	
}
