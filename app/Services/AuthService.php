<?php

namespace App\Services;

use App\User;
use App\Unit;
use Auth;

class AuthService
{
    public function __construct()
    {
        $this->password = config('app.auth.password');
    }
    public function createUser($number,$unit_id,$role)
    {
        $user=$this->findUser($number);
       
        if($user){
            $user->update([
                'unit_id' => $unit_id,
                'role' => $role
            ]);
        }else{
            $password = $this->password;
            User::create([
                'number' => $number,
                'password' => $password,
                'unit_id' => $unit_id,
                'role' => $role
            ]);
        }
    }
    
    function findUser($number)
    {
        return User::where('number',$number)->first();
    }


    public function login($number,$unit_code,$role)
    {
        $unit=Unit::where('code',$unit_code)->first();

        if(!$unit) dd('找不到單位代碼 : ' . $unit_code);

        $this->createUser($number,$unit->id,$role);


        $values=[
			'number' => $number,
			'password' => 'secret',
        ];
      
        $remember=true;
        return Auth::attempt($values,$remember);
       

    }
    
    
    
}