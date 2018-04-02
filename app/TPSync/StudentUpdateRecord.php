<?php

namespace App\TPSync;

use App\BaseModel;

class StudentUpdateRecord extends BaseModel
{
    protected $table = 'student_update_records';
    protected $fillable = [ 
                            'name' ,'number', 'department' ,
                            'email', 'password',
                            'status',
						  ];
}
