<?php

namespace App\TPSync;

use App\TPSync\BaseTPSyncModel;
use App\Support\Helper;

class GroupSync extends BaseTPSyncModel
{
    protected $table = 'group_sync';
    protected $fillable = [  
        'code', 'parent' ,'name' ,'members', 'admin', 
        'tp_id','is_delete', 'sync'
     
    ];

    public function getMembers()
    {
        $members=[];
        if($this->members){
           $members= explode(',', $this->members);
        }
        $sub_members=$this->subMembers();
        $members= array_merge($members,$sub_members);

        return Helper::strFromArray($members);

    }

    public function subGroups()
    {
        return static::where('parent',$this->code)->get();
      
    }

    public function subMembers()
    {
        $subGroups = $this->subGroups();
        $sub_members=[];
        if( count($subGroups)){
            for($i = 0; $i < count($subGroups); ++$i) {
                $sub_group = $subGroups[$i];
                if($sub_group->members){
                    $sub_members=array_merge($sub_members,explode(',', $sub_group->members));
                   
                }
            }
            
        }

        return $sub_members;
    }



    
            
}
