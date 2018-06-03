<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Student\Student;
use App\Student\StudentProfile;
use App\PSN\Staff;

class User extends Authenticatable
{
	use Notifiable;
	public $timestamps = false;
	
    protected $fillable = [
        'number', 'name', 'dob' ,'email','password','unit_id','role' , 'active'
    ];
    protected $hidden = [
        'password', 'remember_token',
	];
	
	public static function teacherRoleName()
	{
		return 'Teacher';
	}

	public static function staffRoleName()
	{
		return 'Staff';
	}

	public static function studentRoleName()
	{
		return 'Student';
	}

    public function setPasswordAttribute($value) 
    {
		$this->attributes['password'] = bcrypt($value);
	}
	
	public function setRoleAttribute($value) 
    {
		if($value) $this->attributes['role'] = ucfirst($value);
		
    }

    public static function initFromSchoolStudent(Student $student)
	{
        $profile = $student->getProfile();

		return [
			'number' => $student->getNumber(),
			'name' => $profile->getName(),
			'dob' => $profile->getDOB(),
			'email' => $student->getEmail(),
			'unit_id' => '',
			'role' => static::studentRoleName(),
		
			'active' => $student->isActive() ,
			'removed' => false,
			
			
	   ];
	}

	public static function test()
	{
		return static::studentRoleName();
	}

	public static function initFromStaff(Staff $staff,$role)
	{
		
		return [
			'number' => $staff->getNumber(),
			'name' => $staff->getName(),
			'dob' => $staff->getDOB(),
			'email' => $staff->getEmail(),
			'unit_id' => '',
			'role' => $role,
		
			'active' => $staff->isActive() ,
			'removed' => false,
			
			
	   ];
	}
    
    public function unit() 
    {
		return $this->belongsTo('App\Unit');
	}
    
    
    

   
   

}
