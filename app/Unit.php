<?php

namespace App;

use App\BaseModel;
use App\PSN\SchoolDepartment;
use App\Student\ClassTable;
use App\Support\Helper;

class Unit extends BaseModel
{
	public $timestamps = false;
	protected $fillable = [ 'name', 'code', 'parent', 'tp_id','description',
							'admin','level_ones','level_twos', 'err',
						    'is_class','active', 'removed',
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
			
			 
        ];
	}	

	public static function initFromSchoolDepartment(SchoolDepartment $department,SchoolDepartment $parent=null)
	{
		$parentId=0;
		$parentCode='';
		if($parent) $parentCode=$parent->getCode();

		if($parentCode){
			$parentUnit=static::where('code',$parentCode)->first();
			if($parentUnit) $parentId=(int)$parentUnit->id;
		} 


		return [
			'name' => $department->getName(),
			'code' => $department->getCode(),
			'parent' => $parentId > 0 ? $parentId : '',
			'description' => '',
			'is_class' => false,
			'admin' => '',
			'level_ones' => '',
			'level_twos' => '',
			'active' => $department->isActive() ,
			'removed' => false,
			
	   ];
	}

	public static function initFromSchoolClass(ClassTable $classTable,SchoolDepartment $parent=null)
	{
		$parentId=0;
		$parentCode='';
		if($parent) $parentCode=$parent->getCode();

		if($parentCode){
			$parentUnit=static::where('code',$parentCode)->first();
			if($parentUnit) $parentId=(int)$parentUnit->id;
		} 


		return [
			'name' => $classTable->getName(),
			'code' => $classTable->getCode(),
			'parent' => $parentId > 0 ? $parentId : '',
			'description' => '',
			'is_class' => true,
			'admin' => '',
			'level_ones' => '',
			'level_twos' => '',
			'active' => $classTable->isActive() ,
			'removed' => false,
			
			
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

	public function isDepartment()
	{
		return Helper::str_starts_with($this->code,'2');
		
	}

	public function getParent()
    {
		$parentId=(int)$this->parent;
		if($parentId)  return static::find($parentId);
		return null;
       
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

	public function topManager()
	{
		$rootUnit=$this->rootUnit();
		if($rootUnit){
			return $rootUnit->admin; 
		}else{
			return $this->admin; 
		}
	}

	


	public function getParents()
	{
		
		$parentDepartment=$this->getParent();
		if(!$parentDepartment) return ;
		
		$hasParent=$parentDepartment->parent > 0;
		$parents = collect([$parentDepartment]);
		
		while ($hasParent) {
			$parentDepartment=static::find($parentDepartment->parent);
			$parents->push($parentDepartment);
			$hasParent=$parentDepartment->parent > 0;
		}

		$this->parentDepartment=$parents;
	}

	public function getChildren($hideMembers=true,$is_class=false){
		
		$children=$this->childs($hideMembers,$is_class);

		if(count($children)){
            foreach ($children as $unit) {
                $unit->getChildren($hideMembers,$is_class);
            }
        }

		$this->children= $children;

		return $children;
		
	}
	
	public function childs($hideMembers=true , $is_class=false)
	{
		
		return static::where('removed',false)
					  ->select('name','code','id','parent')
					  ->where('parent',$this->id)
					  ->where('is_class',$is_class)
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

	public function getMembers(bool $activeOnly=false)
    {
		$members=[];
		if($activeOnly) $members=$this->users()->where('active',true)->pluck('number')->toArray();
		else $members=$this->users()->pluck('number')->toArray();
        
        
		$sub_members=$this->subMembers($activeOnly);
	
        $members= array_merge($members,$sub_members);

        return  array_unique($members);

	}
	
	public function subMembers(bool $activeOnly=false)
    {
		$childs = $this->childs();
		
        $sub_members=[];
        for($i = 0; $i < count($childs); ++$i) {
			$child = $childs[$i];
			
			
			if($activeOnly) $sub_members=array_merge($sub_members,$child->users()->where('active',true)->pluck('number')->toArray());
			else $sub_members=array_merge($sub_members,$child->users()->pluck('number')->toArray());

			
		}
        return $sub_members;
    }


	public function getLevelOneManagers()
	{
		if($this->level_ones) return $this->level_ones;

		$parentUnit=$this->getParent();
		if($parentUnit) return $parentUnit->getLevelOneManagers();
		return '';

	}

	
	
	
}
