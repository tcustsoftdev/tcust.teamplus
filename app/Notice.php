<?php

namespace App;

use App\BaseModel;
use Carbon\Carbon;

class Notice extends BaseModel
{
	protected $fillable = ['unit_id','content', 'staff', 'teacher', 'student',
							'units','departments','classes', 'levels','reviewed',
							'reviewed_by','created_by', 'updated_by'
						  ];
	public static function init()
    {
         return [
			'unit_id' => '',
			'content' => '',
			
			'staff' => false,
			'teacher' => false,
			'student' => false,
			
			'units' => '',
			'departments' => '',
			'classes' => '',
			'levels' => '',
            
            'reviewed' => false,
            'reviewed_by' => '',
			'created_by' => '',
			'updated_by' => '',
			 
        ];
    }						  
    
	public function attachments() 
	{
		return $this->hasMany(Attachment::class);
	}

	public function unit() 
	{
		return $this->belongsTo('App\Unit');
	}

	public function saveAttachments($attachments)
	{
		$this->attachments()->delete();

		$this->attachments()->saveMany($attachments);
		
	}
	
	public function getUpdatedAtAttribute($attr) {        
        return Carbon::parse($attr)->format('Y-m-d - h:i'); 
    }
	
}
