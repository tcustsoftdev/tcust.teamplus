<?php

namespace App;

use App\BaseModel;

class Unit extends BaseModel
{
	protected $fillable = [ 'name', 'code', 'parent', 'tp_id','description',
							'admin','level_ones','level_twos',
						    'is_class','active', 'removed','updated_by'
						  ];
	
	public function setCodeAttribute($value) 
	{
		$this->attributes['code'] = strtolower($value);
	}
	public static function initialize()
    {
         return [
			 'name' => '',
			 'code' => '',
			 'parent' => '',
			 'description' => '',
			 'is_class' => false,
			 'admin' => '',
			 'level_ones' => '',
			 'level_twos' => '',
			 'active' => true,
			 'removed' => false,
			 'updated_by' => '',

			
			 
        ];
	}	
	
	public function notices() 
	{
		return $this->hasMany(Notice::class);
	}
	public function users() 
	{
		return $this->hasMany(User::class);
	}
    
	public function parentDepartment()
    {
		$parentId=(int)$this->parent;
		if($parentId)  return static::find($parentId);
		return null;
       
    }

	public function parentName()
    {
		$parent_department=$this->parentDepartment();
		if($parent_department)  return $parent_department->name;
		return '';
       
    }

	public function parentCode()
    {
		$parent_department=$this->parentDepartment();
		if($parent_department)  return $parent_department->code;
		return '';
       
	}
	
	public function rootUnit()
	{
		
		$parentUnit=null;
		$parentId=$this->parent;
		while ($parentId>0) {
			$parentUnit=static::find($parentId);
			$parentId=$parentUnit->parent;
		}

		return $parentUnit;
	}

	public function topManagers()
	{
		$rootUnit=$this->rootUnit();
		if($rootUnit){
			return $rootUnit->level_ones; 
		}else{
			return $this->level_ones; 
		}
	}


	public function getParents()
	{
		if(!$this->parent){
			
			$this->parentDepartment=null;
			return ;
		}
		$parentDepartment=static::find($this->parent);
		
		$hasParent=$parentDepartment->parent > 0;
		$parents = collect([$parentDepartment]);
		
		while ($hasParent) {
			$parentDepartment=static::find($parentDepartment->parent);
			$parents->push($parentDepartment);
			$hasParent=$parentDepartment->parent > 0;
		}

		$this->parentDepartment=$parents;
	}

	public function getChildren($hideMembers=true){
		
		$children=$this->childs($hideMembers);

		if(count($children)){
            foreach ($children as $unit) {
                $unit->getChildren($hideMembers);
            }
        }

		$this->children= $children;

		return $children;
		
	}
	
	public function childs($hideMembers=true)
	{
		return static::where('removed',false)
					  ->select('name','code','id','parent')
					  ->where('parent',$this->id)
					  ->get();
	}
	public function toOption()
	{
		$childrenOptions=[];
		if(count($this->children)){
			foreach ($this->children as $department) {
				array_push($childrenOptions,  $department->toOption());
       		}
		}
		return [ 'text' => $this->name , 
                 'value' => $this->id , 
				 'childrenOptions' => $childrenOptions
               ];
	}
	
	
}
