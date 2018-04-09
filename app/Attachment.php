<?php

namespace App;

use App\BaseModel;

class Attachment extends BaseModel
{
	protected $fillable = ['notice_id', 'title', 'name', 'type',
							'file_data','created_by', 'updated_by'
						  ];
	public static function initialize()
    {
         return [
			'id' => 0,
            'notice_id' => 0,
			'title' => '',
			'name' => '',
			'type' => '',
			
            'file_data' => '',
			'created_by' => '',
			'updated_by' => '',
			 
		];
		
		return [
			'Id' => 0 , 		
			'Notice_Id' => 0,
			'Title' => '',
			'Name' => '',
			'Type' => '',
			'FileData' => '',

		];
	
    }						  
    
	
    public function notice() 
	{
		return $this->belongsTo('App\Notice');
    }
	
}
