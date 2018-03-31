<?php

namespace App\Teamplus;

use App\Teamplus\TPModel;

class TPUserGroup extends TPModel
{
    
    protected $table = 'USER_GROUP';
    protected $primaryKey = 'GROUP_ID';
    public $timestamps = false;

    
}
