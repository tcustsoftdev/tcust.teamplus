<?php

namespace App\TPSync;

use App\TPSync\BaseTPSyncModel;

class StudentUpdateRecord extends BaseTPSyncModel
{
    protected $table = 'student_update_records';
    protected $fillable = [ 
                            'name' ,'number', 'department' ,
                            'email', 'password',
                            'status',
						  ];
}
