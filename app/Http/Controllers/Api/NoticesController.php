<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Teamplus\Notices;
use Log;

class NoticesController extends Controller
{
   
    public function __construct(Notices  $notices) 
    {
        $this->api_key=config('app.api.key');

        $this->tpNotices = $notices;
    }

    public function store()
    {
        $api_key = 'api_key';
        if(isset($_POST['api_key'])) $api_key=$_POST['api_key'];

        $content = '';
        if(isset($_POST['content'])) $content=$_POST['content'];
        
        $members = '';
        if(isset($_POST['members'])) $members=$_POST['members'];
        
        $errors=[];
		if(!$content){
			$errors['content'] = ['內容不可空白'];
        }
        if(!$members){
			$errors['members'] = ['接收成員名單不可空白'];
        }
        if($api_key!=config('app.api.key')){
			$errors['api_key'] = ['api_key錯誤'];
		}
        
        if($errors) return $this->requestError($errors);



        $type=1;
          
        $file='';
        $file_name='';

        $accounts=array_unique(explode(',', $members)); 
    
        
        $notice_result=$this->tpNotices->create($accounts,$type, $content, $file, $file_name);
        
        if(!$notice_result->IsSuccess) Log::error($notice_result->Description);
            

        return  response()->json();
    }

    
    
   
}
