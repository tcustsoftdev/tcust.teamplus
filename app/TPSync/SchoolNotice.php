<?php

namespace App\TPSync;

use App\TPSync\BaseTPSyncModel;

class SchoolNotice extends BaseTPSyncModel
{
    protected $table = 'school_notice_sync';
    protected $fillable = [  
        'type_id', 'text_content' ,'members',
        'media_content',
     
    ];

    public function attachments() 
	{ 
		return $this->hasMany('App\TPSync\NoticeAttachment', 'notice_id');
	}

    
                          

            
}
