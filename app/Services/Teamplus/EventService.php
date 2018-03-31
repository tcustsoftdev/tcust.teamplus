<?php

namespace App\Services\Teamplus;

use App\Repositories\Teamplus\Events;
use App\Repositories\EventCalendars;
use App\TPSync\SchoolEventCalendar;

class EventService
{
 
    public function __construct(EventCalendars  $eventCalendars)
    {
        $this->eventCalendars=$eventCalendars;
    }

    public function syncEvents()
    {
        $need_sync_list=SchoolEventCalendar::where('sync',false)
                                            ->orderBy('created_at')
                                            ->get();
                                            
        if(count($need_sync_list))
        {
             foreach($need_sync_list as $record){

                if($record->is_delete){
                    $this->eventCalendars->delete($record->code);
                }else{
                    $code=$record->code;
                    $name=$record->name;
                    $description=$record->description;
                    $start_time=$record->start_time;
                    $end_time=$record->end_time;
                    $members=$record->members;
    
                    $eventCalendar=$this->eventCalendars->createOrUpdate($code,$name,$description,$start_time,$end_time,$members);
                    
                }

                $record->sync=true;
                $record->save();

                
             }

        }
    }
    
    
    
}