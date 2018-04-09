<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\User;
use App\Repositories\TPSync\Users;
use Log;

class UsersController extends Controller
{
   
    public function __construct(AuthService $authService,Users $tpUsers) 
    {
        $this->api_key=config('app.api.key');
        $this->user_password=config('teamplus.user.password');
      
        $this->authService=$authService;
        $this->tpUsers=$tpUsers;
	}
   
    
    public function store()
    {
        $request=request();

        $key=$request->api_key;
      
        if($key!=$this->api_key)
        {
            Log::error('api_key錯誤.(UsersController@store)');
            abort(500);
        }
       

        $values=[
            'name' => $request->name,
            'dob' => $request->dob,
            'number' => $request->number,
            'email' => $request->email,

            'active' => $request->active,
            
        ];

        $user=User::where('number',$request->number)->first();
      
      
        if($user){
            $user->update($values);
        }else{
            Log::info('user number=' .  $request->number  . '不存在.(UsersController@store)');
            
        }
       
        $this->syncTPUser($user);
       
       
        return response()->json();
    }

    function syncTPUser($user)
    {
        
        $number=$user->number;
       
        $password='';
        $email=$user->email;
        $name=$user->name;
        $department_code=$user->unit->code;
        $status=$user->active ? 1 : 0;

       
        $exist= $this->tpUsers->userExist($number);
        
       
        $errMsg='';
        if($exist){
            $errMsg=$this->tpUsers->syncUser($number, $password ,$email, $name, $department_code, $status);
        }else{
            $password=  $this->user_password;
            if($user->dob)  $password=$user->dob;

            $errMsg=$this->tpUsers->syncUser($number, $password ,$email, $name, $department_code, $status);
           
        }
        

        if($errMsg) Log::info($errMsg);
    }

    
    

    

    

    
    
   
}
