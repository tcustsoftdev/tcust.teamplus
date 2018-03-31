<?php

namespace App\TPSync;

use App\TPSync\BaseTPSyncModel;

class StaffUpdateRecord extends BaseTPSyncModel
{
    protected $table = 'staff_update_records';
    protected $fillable = ['name' ,'number', 'department' ,
                            'job_title','extend',
                            'email', 'password',
                             'status',
						  ];
}
