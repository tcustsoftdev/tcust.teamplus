<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Route;
use App\Services\UnitsService;
use App\Unit;

class UnitsController extends Controller
{
   
    public function __construct(UnitsService $unitsService) 
    {
		 $this->unitsService=$unitsService;
	}
   
    
    protected function index()
    {
        $request = request();

        $units=$this->units->getAll()->get();

        return response()->json($units);
    }

    
    
   
}
