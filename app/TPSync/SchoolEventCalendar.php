<?php

namespace App\TPSync;

use App\BaseModel;

class SchoolEventCalendar extends BaseModel
{
    protected $table = 'school_event_calendar';
    protected $fillable = [ 'code', 'name', 'description','members',
						    'start_time', 'end_time', 'is_delete',  'sync'
                          ];
                          

            
}
