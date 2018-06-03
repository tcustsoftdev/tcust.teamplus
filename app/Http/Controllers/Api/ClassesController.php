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
        $classes = $this->classesService->getActiveClasses();
        $parentIds=array_unique($classes->pluck('parent')->toArray());
     
        $parents=Unit::whereIn('id',$parentIds)->get();
        
            

        $tree=$this->classesService->getTree($parents);
      
        return response()->json($tree);
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
