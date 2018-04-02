<?php

namespace App;

use App\BaseModel;

class Unit extends BaseModel
{
	protected $fillable = ['name', 'code', 'parent', 'description',
						    'is_class','active', 'removed','updated_by'
						  ];
	public static function initialize()
    {
         return [
			 'name' => '',
			 'code' => '',
			 'parent' => 0,
			 'description' => '',
			 'is_class' => 0,
			 
			 'active' => 1,
			 'removed' => 0,
			 'updated_by' => '',

			 'order' => 0,
			 'icon' => ''
			 
        ];
    }						  
    public function jobPositions() 
	{
		return $this->hasMany(JobPosition::class);
	}
	public function staffs() 
	{
		return $this->hasMany(Staff::class);
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

	public function getChildren(){
		
		$children=$this->childs();

		if(count($children)){
            foreach ($children as $unit) {
                $unit->getChildren();
            }
        }

		$this->children= $children;

		return $children;
		
	}
	
	public function childs()
	{
		return static::where('removed',false)
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
