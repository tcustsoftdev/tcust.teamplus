<?php

namespace App\TPSync;

use App\TPSync\BaseTPSyncModel;

class NoticeAttachment extends BaseTPSyncModel
{
    protected $table = 'school_notice_attachment';
    protected $fillable = [  
        'notice_id','file_type', 'file_data' ,
        'display_name',
     
    ];
    
    public function notice() {
       
        return $this->belongsTo('App\TPSync\SchoolNotice', 'notice_id');
    }
                          

            
}
