<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use App\Services\NoticeService;
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
    
    public function index()
    {
        $units = $this->unitsService->getTree();
      
        return response()->json($units);
    }

    public function getByCodes($codes)
    {
        $codes=explode(',', $codes);
        $units=$this->unitsService->getAll()->whereIn('code',$codes)
                            ->select('name','code','id','parent')
                            ->orderBy('code')->get();
                            
        return response()->json($units);                   
    }

   
    
    
   
}
