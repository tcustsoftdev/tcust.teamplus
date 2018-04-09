<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UnitsService;
use App\Services\AuthService;
use App\Repositories\TPSync\Departments;
use App\Services\Teamplus\GroupService;
use App\Repositories\TPSync\Users;
use App\Unit;
use App\User;
use Log;

class DepartmentsController extends Controller
{
   
    public function __construct(UnitsService $unitsService,AuthService $authService,
        Departments $tpDepartments, GroupService $tpGroupService,Users $tpUsers) 
    {
        $this->api_key=config('app.api.key');

        $this->unitsService=$unitsService;
        $this->authService=$authService;

        $this->tpDepartments=$tpDepartments;
        $this->tpGroupService=$tpGroupService;
        $this->tpUsers=$tpUsers;
    }
    
    public function test()
    {
       
        $unit=Unit::find(2);
        $this->unitsService->syncGroup($unit);

        //$this->unitsService->syncGroup($unit);
       
    }

    
    
    public function store()
    {
        $request=request();
      
        $key=$request->api_key;
      
        if($key!=$this->api_key)
        {
            Log::error('api_key錯誤.(DepartmentsController@store)');
            abort(500);
        }

        $is_delete=$request->is_delete;
       

        $values=[
            'removed' => $is_delete,
            'active' => !$is_delete,
           
            'name' => $request->name,
            'code' => $request->code,
            'admin' => $request->admin,
            'level_ones' => $request->level_ones,
            'level_twos' => $request->level_twos,

            'is_class' => $request->is_class
            
        ];

        

        if($request->parent){
            $parentUnit = $this->getParentUnit($request->parent);
            if(!$parentUnit) {
                Log::error('parent ' . $request->parent .'不存在.');
                abort(500);
            }

            $values['parent'] = $parentUnit->id;

        }

        $unit=Unit::where('code',$request->code)->first();
       
        if($unit){
            $unit->update($values);
        }else{
            $unit=Unit::create($values);
        }
       

        $students=$request->students;
        if($students) $this->createUsers($unit->id, explode(',',  $students), 'student');


        $staffs=$request->staffs;
        if($staffs) $this->createUsers($unit->id, explode(',',  $staffs), 'staff');

        $teachers=$request->teachers;
        if($teachers) $this->createUsers($unit->id, explode(',',  $teachers), 'teacher');


        $this->syncTPDepartment($unit);
      

        return response()->json();
    }

    function getParentUnit($parenr_code)
    {
        if(!$parenr_code) return null;
        return  Unit::where('code',$parenr_code)->first();
    }

    function createUsers(int $unit_id , array $numbers , $role)
    {
        foreach($numbers as $number){
            $this->authService->createUser($number,$unit_id,$role);
        }
    }

    function syncTPDepartment(Unit $unit)
    {
        $name=$unit->name;
        $code=$unit->code;

        $parent_code='';
        $parent = $unit->parentDepartment();
        if($parent) $parent_code=$parent->code;

        $delete=!$unit->active;

        $this->tpDepartments->syncDepartment($name, $code,$parent_code,$delete);
    }

    

    

    

    

    
    
   
}
