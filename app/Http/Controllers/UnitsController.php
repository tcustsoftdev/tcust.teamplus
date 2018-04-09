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
       
        $this->test='ikik';
        $this->units=$unitsService;
       
    }
    
    public function index()
    {
        dd($this->units);
        $request = request();

        $units=$this->units->getAll()->get();

        return response()->json($units);
    }

    //api
    public function tree()
    {
        dd($this->units);
        dd($this->unitsService);
        $units = $this->$unitsService->getTree();

        return response()->json($units);
    }

    
    
   
}
