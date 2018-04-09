<?php

namespace App\Teamplus;

use App\Teamplus\TPModel;
use Carbon\Carbon;

class TPDepartmentForSync extends TPModel
{
    
    protected $table = 'DepartmentForSync';
    protected $primaryKey = 'SN';
    public $timestamps = false;

    protected $fillable =  ['Code',  'Name', 'ParentCode',   'Description','UpdateTime',
                              'SyncStatus' ,   'IsDelete' ,  'SyncUpdateTime' 
                            
                        	];

    public static function initialize()
    {
       return [
            'Code'=>'',
            'Name' => '',
            'ParentCode' => '',
            'Description' => '',
            'UpdateTime' => Carbon::today(),
            'IsDelete' => 0,
            'SyncStatus' => 0,
            'SyncUpdateTime' =>''

        ];
    }                              
}
