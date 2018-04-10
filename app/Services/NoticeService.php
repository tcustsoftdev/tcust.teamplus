<?php

namespace App\Services;
use App\Repositories\Teamplus\Notices;
use App\TPSync\SchoolNotice;
use App\Notice;
use App\Unit;
use App\User;
use Log;

class NoticeService
{
 
    public function __construct(Notices  $notices)
    {
        $this->with=['attachments','unit'];
        $this->tpNotices = $notices;
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
        $unitCodes=[];
        $unitIds=[];

        
		   
        //根據Notice資料, 取得需要通知的成員代號
        $teacherNumbers=[];
       
        if($notice->teacher)
        {
            $unitCodes= explode(',', $notice->units);
            $unitIds=Unit::whereIn('code',$unitCodes)->pluck('id')->toArray();
            $teacherNumbers=User::whereIn('unit_id',$unitIds)->where('role','teachers')
                                ->where('active',true)->pluck('number')->toArray();
            
        }

        

        $staffNumbers=[];
       
        if($notice->staff)
        {
           
            if(!$unitCodes)   $unitCodes= explode(',', $notice->units);
            if(!$unitIds)  $unitIds=Unit::whereIn('code',$unitCodes)->pluck('id')->toArray();
           
            if(!$notice->levels)
            {
                // 所有職員
                $units=Unit::whereIn('id', $unitIds);
                $staffNumbers=User::whereIn('unit_id',$unitIds)->where('role','staff')
                                ->where('active',true)->pluck('number')->toArray();

            }else if($levels=='1,2'){   // 通知一級主管與二級主管
                $level_ones = array_flatten($units->pluck('level_ones')->toArray());
                $level_twos=array_flatten($units->pluck('level_twos')->toArray());
                
                $staffNumbers= array_merge($level_ones,$level_twos); 
                $staffNumbers= array_unique(array_filter($staffNumbers));
            }else if($levels=='1'){   // 通知一級主管
                $level_ones = array_flatten($units->pluck('level_ones')->toArray());
                $staffNumbers= array_unique(array_filter($level_ones));
            }else if($levels=='2'){   // 通知二級主管
                $level_twos=array_flatten($units->pluck('level_twos')->toArray());
                $staffNumbers= array_unique(array_filter($level_twos));
            }   
            
        } 

        $studentNumbers=[];
        if($notice->student)
        {
            $classCodes= explode(',', $notice->classes);
            $classIds=Unit::whereIn('code',$classCodes)->pluck('id')->toArray();
           
            $studentNumbers=User::whereIn('unit_id',$unitIds)->where('role','student')
                                ->where('active',true)->pluck('number')->toArray();
            
        } 

        $accounts=array_unique(array_merge($teacherNumbers,$staffNumbers,$studentNumbers)); 

        $attachment=$notice->attachments()->first();
        if($attachment){
            $file_type=$attachment->type;
            $file_data=$attachment->file_data;

            $upload_result= $this->tpNotices->upload($type,$file_data);
            if($upload_result->IsSuccess){

                $file=$upload_result->FileName;
              
                $type=2;
                $content=$notice->content;
               
                $file_name=$attachment->title . '.' . $attachment->type;
               
                $notice_result=$this->tpNotices->create($accounts,$type, $content, $file, $file_name);

              

                $success=$notice_result->IsSuccess;
                $msg==$notice_result->Description;

            }else{
                Log::error($upload_result->Description);
                return;
            }


        }else{
           
            
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