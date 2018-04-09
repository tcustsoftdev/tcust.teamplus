<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;

use App\Services\ClassesService;
use Illuminate\Http\Request;
use Route;
use App\Unit;

class ClassesController extends Controller
{
   
    public function __construct(ClassesService $classesService) 
    {
        $this->classesService=$classesService;
       
    }
    
    public function index()
    {
        $classes =$this->classesService->getAll()
                        ->select('name','code','id','parent')
                        ->orderBy('code')->get();

        return response()->json($classes);
    }

    public function getByCodes($codes)
    {
        $codes=explode(',', $codes);
        $classes=$this->classesService->getAll()->whereIn('code',$codes)
                            ->select('name','code','id','parent')
                            ->orderBy('code')->get();
                            
        return response()->json($classes);                   
    }

   
    
    
   
}
