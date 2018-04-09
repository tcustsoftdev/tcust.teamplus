<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    protected $fillable = [
        'number', 'name', 'dob' ,'email','password','unit_id','role' , 'active'
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setPasswordAttribute($value) 
    {
		$this->attributes['password'] = bcrypt($value);
    }
    
    public function unit() 
    {
		return $this->belongsTo('App\Unit');
	}
    
    
    

   
   

}
