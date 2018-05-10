<?php

namespace App\Services;

use App\Repositories\Teamplus\Notices;
use App\TPSync\SchoolNotice;
use App\Notice;
use App\Unit;
use App\User;
use App\Services\UnitsService;
use App\Services\ClassesService;
use Log;

class NoticeService
{
 
    public function __construct(Notices  $notices,UnitsService  $unitsService,ClassesService $classesService)
    {
        $this->with=['unit'];

        $this->tpNotices = $notices;
        $this->unitsService = $unitsService;
        $this->classesService = $classesService;
    }

    public function getAll()    
    {
        return Notice::with($this->with); 
        
    }
    public function getById($id)    
    {
        return $this->getAll()->where('id',$id)->first();
        
    }

    public function fetchNotices(int $unitId =0 , bool $reviewed=true,    $keyword = '')
    {
       
        $notices=null;
        if($keyword) $notices=$this->getByKeyword($keyword);
        else $notices=$this->getAll();

        if ($unitId) $notices = $notices->where('unit_id',$unitId);
        

        return $notices->where('reviewed',$reviewed)
                       ->orderBy('created_at','desc');
    }
    public function  getByKeyword($keyword)
    {
        return $this->getAll()->where('content', 'LIKE', '%' .$keyword .'%');
       
    }

   

    public function sendNotice(Notice $notice)
    {
        
        $type_id=1;  // 純文字
        $unitCodes= explode(',', $notice->units);
        $unitIds=[];
		   
        //根據Notice資料, 取得需要通知的成員代號
        $teacherNumbers=[];
       
        if($notice->teacher)
        {
            $classCodes= explode(',', $notice->classes);
          
            $classes = $this->classesService->getClassesByCodes($classCodes);
           
            $parentUnitIds=array_unique($classes->pluck('parent')->toArray());
            
            $teacherNumbers = $this->unitsService->getTeachersByUnitIds($parentUnitIds)
                                            ->where('active',true)
                                            ->pluck('number')->toArray();

            $teacherNumbers = array_filter($teacherNumbers);
         
            
        }
        
        $staffNumbers=[];

        if($notice->staff)
        {
            $units=$this->unitsService->getUnitsByCodes($unitCodes);          
          
            if(!$notice->levels)
            {
                // 所有職員
                $unitIds= $units->pluck('id')->toArray();
               
                $staffNumbers = $this->unitsService->getStaffsByUnitIds($unitIds)
                                                    ->where('active',true)
                                                    ->pluck('number')->toArray();

                                             

            }else if($notice->levels=='1,2'){   // 通知一級主管與二級主管
                $level_ones = array_flatten($units->pluck('level_ones')->toArray());
              
                $level_twos=array_flatten($units->pluck('level_twos')->toArray());
                $staffNumbers= array_merge($level_ones,$level_twos); 
                $staffNumbers= array_unique(array_filter($staffNumbers));
               
            }else if($notice->levels=='1'){   // 通知一級主管
                $level_ones = array_flatten($units->pluck('level_ones')->toArray());
                $staffNumbers= array_unique(array_filter($level_ones));
            }else if($notice->levels=='2'){   // 通知二級主管
                $level_twos=array_flatten($units->pluck('level_twos')->toArray());
                $staffNumbers= array_unique(array_filter($level_twos));
            }   
            
        } 
       
        $studentNumbers=[];
        if($notice->student)
        {
            $classCodes= explode(',', $notice->classes);
            $classes = $this->classesService->getClassesByCodes($classCodes);
           
            $classIds= $classes->pluck('id')->toArray();
               
            $studentNumbers = $this->classesService->getStudentsByClassIds($classIds)
                                                 ->where('active',true)
                                                 ->pluck('number')->toArray();
           
        } 

        $accounts=array_unique(array_merge($teacherNumbers,$staffNumbers,$studentNumbers)); 
        
        $content=$notice->content;

        $attachment=$notice->attachments()->first();
        if($attachment){
           
            $file_type=$attachment->type;
            $file_data=$attachment->file_data;
            $fileReadName=$attachment->getReadName();
           

            $upload_result= $this->tpNotices->upload($file_type,$file_data);
           
            if($upload_result->IsSuccess){

                $file=$upload_result->FileName;
              
                $type=2;
               
                $notice_result=$this->tpNotices->create($accounts,$type, $content, $file, $fileReadName);
                
                if(!$notice_result->IsSuccess) Log::error($notice_result->Description);
              

            }else{
                Log::error($upload_result->Description);
                return;
            }


        }else{
            
            $type=1;
          
            $file='';
            $file_name='';
            
            $notice_result=$this->tpNotices->create($accounts,$type, $content, $file, $file_name);
            
            if(!$notice_result->IsSuccess) Log::error($notice_result->Description);
            
        }


		
			
		
    }

    public function syncNotices()
    {
        $need_sync_list=SchoolNotice::where('sync',false)->get();
        
        if(count($need_sync_list))
        {
             foreach($need_sync_list as $record){
                $success=true;
                $msg=''; 
                $hasFiles= count($record->attachments);
               
                if($hasFiles){
                    $attachment=$record->attachments[0];

                    $file_type=$attachment->file_type;
                    $file_data=$attachment->file_data;

                    $upload_result= $this->tpNotices->upload($file_type,$file_data);
                   
                    if($upload_result->IsSuccess){

                        $file=$upload_result->FileName;
                        $record->media_content=$file;

                        $accounts=explode(',',$record->members); 
                        $type=2;
                        $content=$record->text_content;
                       
                        $file_name=$attachment->display_name . '.' . $attachment->file_type;
                       
                        $notice_result=$this->tpNotices->create($accounts,$type, $content, $file, $file_name);

                      

                        $success=$notice_result->IsSuccess;
                        $msg==$notice_result->Description;

                    }else{
                        $success=false;
                        $msg=$upload_result->Description;
                    }

                    
                }else{
                    $accounts=explode(',',$record->members); 
                    $type=1;
                    $content=$record->text_content;
                    $file='';
                    $file_name='';
                    
                    $notice_result=$this->tpNotices->create($accounts,$type, $content, $file, $file_name);
                 
                    $success=$notice_result->IsSuccess;
                    $msg==$notice_result->Description;

                }

                $record->sync=true;
                $record->success=$success;
                $record->msg=$msg;
                $record->save();

                
             }

        }
    }
    
    
    
}