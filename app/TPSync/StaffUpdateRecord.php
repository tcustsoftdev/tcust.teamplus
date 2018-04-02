<?php

namespace App\TPSync;

use App\BaseModel;

class StaffUpdateRecord extends BaseModel
{
    protected $table = 'staff_update_records';
    protected $fillable = ['name' ,'number', 'department' ,
                            'job_title','extend',
                            'email', 'password',
                             'status',
						  ];
}
