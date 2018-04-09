<?php

namespace App\Services;
use App\Repositories\Teamplus\Notices;
use App\TPSync\SchoolNotice;
use App\Notice;

class NoticeService
{
 
    public function __construct(Notices  $notices)
    {
        $this->with=['attachments','unit'];
        $this->notices = $notices;
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

    function getTeachers($units)
    {
        $units = explode(',', $units);
    
        $teachers=[];
        foreach($units as $unit){
            //102000
            
        }

        $teachers=['stephenchoe,ss355'];
        
        return $teachers;
    }

    public function sendNotice(Notice $notice)
    {
        $type_id=1;  // 純文字
		   
        //根據Notice資料, 取得需要通知的成員代號
        $teachers=[];
        if($notice['Teacher']) $students=$this->getTeachers($notice['Units']);

		
			//$notice['Classes'] => 需要通知的班級   格式:  GD41A,ID41A 
			$students=[];
			if($notice['Student']) $students=$this->getStudents($notice['Classes']);

			$staffs=[];
			if($notice['Staff']){
				$units=$notice['Units'];  //需要通知的單位
				$levels=$notice['Levels'];
				if(!$levels)  $staffs=$this->getStaffs($units);  
				else if($levels=='1,2') $staffs=$this->getManagers($units, true, true);   // 通知一級主管與二級主管
				else if($levels=='1') $staffs=$this->getManagers($units, true, false);   // 通知一級主管
				else if($levels=='2') $staffs=$this->getManagers($units, false, true);   // 通知一級主管

			} 
			//取得需要通知的成員帳號  格式:ss355,10545001,10622501 
			$members= array_merge($teachers,$students,$staffs);
			$members=join(',', $members);
		
			
			$created_by=$notice['CreatedBy'];  //建檔的單位代碼   例如:105010
			
			$content=$notice['Content'];
			
			$attachment = $this->findAttachment($notice_id);
			
			$attachment_id=(int)$attachment['Id'];			
			if($attachment_id){
				$type_id=2;   //有附加檔案
			}
			
			$now=date('Y-m-d H:i:s');
			
			$sync_conn = $this->sync_conn;
			
			$query = "INSERT INTO school_notice_sync ( text_content, type_id , members ,  created_at , updated_at ) "; 		  
			$query .= "VALUES (?,?,?,?,?); SELECT SCOPE_IDENTITY()"; 
			
			$arrParams[]=$content;  
			$arrParams[]=$type_id; 
			$arrParams[]=$members;
			$arrParams[]=$now; 
			$arrParams[]=$now; 
			
			
			$resource=sqlsrv_query($sync_conn, $query, $arrParams); 
			if( $resource === false ) {
				die( print_r( sqlsrv_errors(), true));
				throw new Exception('同步失敗');
			}

			
			
			sqlsrv_next_result($resource); 
			sqlsrv_fetch($resource); 
				
			$sync_notice_id= sqlsrv_get_field($resource, 0);   //取得剛才新增的 id
			if($attachment_id){  //有附加檔案
				$this->syncAttachment($attachment_id,$sync_notice_id);
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