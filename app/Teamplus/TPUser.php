<?php

namespace App\Teamplus;

use App\Teamplus\TPModel;

class TPUser extends TPModel
{
    protected $primaryKey = 'UserNo';
    protected $table = 'Users';
    public $timestamps = false;

    
}
