<?php

namespace App\Teamplus;

use App\Teamplus\TPModel;

class TPChannelReceiver extends TPModel
{
    
    protected $table = 'SuperHubReceiver';
    public $timestamps = false;

    protected $fillable = ['ChannelID', 'SG_ID',
        'CreateTime', 'UpdateTime'
    ];
}
