<?php

namespace App\Services\Teamplus;

use App\Repositories\Teamplus\Notices;
use App\TPSync\SchoolNotice;

class NoticeService
{
 
    public function __construct(Notices  $notices)
    {
        $this->notices=$notices;
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

                    $upload_result= $this->notices->upload($file_type,$file_data);

                   
                    if($upload_result->IsSuccess){

                        $file=$upload_result->FileName;
                        $record->media_content=$file;

                        $accounts=explode(',',$record->members); 
                        $type=2;
                        $content=$record->text_content;
                       
                        $file_name=$attachment->display_name . '.' . $attachment->file_type;
                       
                        $notice_result=$this->notices->create($accounts,$type, $content, $file, $file_name);

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
                    
                    $notice_result=$this->notices->create($accounts,$type, $content, $file, $file_name);
                 
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