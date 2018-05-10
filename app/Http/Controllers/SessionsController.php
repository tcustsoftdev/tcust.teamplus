<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Services\AuthService;

class SessionsController extends Controller
{
	
    public function __construct(AuthService $authService)
    {
		$this->authService=$authService;
	}
   
	public function store()
    {  
		Auth::logout();
		
		$ip=$this->getUserIpAddress();
	
		$number = $_POST['number'];
		$unit_code = $_POST['unit'];
		$role = $_POST['role'];

		

		$login=$this->authService->login($number,$unit_code,$role);
		

		if($login){
			return redirect('/notices');
		}else{
			dd('無法登入帳號 : ' . $number);
		}
	
                
	}

	
}
