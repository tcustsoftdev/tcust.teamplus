<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\NoticeService;
use App\Unit;
use App\Notice;
use App\Attachment;
use App\Core\PagedList;
use App\Services\UnitsService;
use App\Support\Helper;
use DB;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Auth;

use App\Repositories\TPSync\Users;

class NoticesController extends Controller
{
   
    public function __construct(NoticeService $noticeService,UnitsService $unitsService,Users $tpUsers) 
    {
        $this->subs_api_url=config('app.school.subs_api');
        $this->noticeService=$noticeService;
        $this->unitsService=$unitsService;

        $this->tpUsers=$tpUsers;
    }

    function unitOptions($withEmpty=true)
    {
        $units= $this->unitsService->getAll()->orderBy('code')->get();
        
        $options = $units->map(function ($unit) {
            return ['text'=> $unit->name , 'value' => $unit->id ];
        })->all();

        if($withEmpty) array_unshift($options, ['text' => '-----------' , 'value' =>'0']);
        
       
       return $options;
    }

    function getManagers(Unit $unit)
    {
        $level_ones =  $unit->topManagers(); 

        $level_ones= explode(',',$level_ones);   

        $url= $this->subs_api_url;
        if(!$url) return $canReviewUsers;
      
        $client = new Client(); 
        $response = $client->request('POST', $url, [
            'form_params' => [
                'numbers' => $level_ones,
            ]
        ]);

        $subs = json_decode($response->getBody());

        return array_merge($subs,$level_ones);
      
        
    }

    function setAuthority($notice)
    {
       
        if(Helper::isTrue($notice->reviewed)){
         
            return [
                'canEdit' => false,
                'canReview' => false,
                'canDelete' => false,
            ]; 

        } 

        $currentUser=$this->currentUser();
        
        //一級主管與代理人
        $managers = $this->getManagers($notice->unit);

       
        $canReview = in_array($currentUser->number, $managers);

        if($notice->created_by == $currentUser->number)  $canEdit=true;
        if(!$canEdit)   $canEdit = $canReview;
       

        $canDelete=$canEdit;
       
        
        return [
            'canEdit' => $canEdit,
            'canReview' => $canReview,
            'canDelete' => $canDelete,
        ];
      
    }

    
    
    
    public function index()
    { 

        $request=request();

        $unit=0;
        if($request->unit)  $unit=(int)$request->unit;

        $reviewed=false;
        if($request->reviewed)  $reviewed=Helper::isTrue($request->reviewed);


        $keyword='';
        if($request->keyword)  $keyword=$request->keyword;

        $page=1;
        if($request->page)  $page=(int)$request->page;

        $pageSize=10;
        if($request->pageSize)  $pageSize=(int)$request->pageSize;
       
        $notices=$this->noticeService->fetchNotices($unit ,  $reviewed,$keyword);

      
      
        $pageList = new PagedList($notices,$page,$pageSize);
        

        if($this->isAjaxRequest()){
            return response()->json($pageList);
        }
       
        return view('notices.index')->with([
         
            'units' => $this->unitOptions(),
       
            'list' =>  $pageList
        ]);
       
       
       
       
    }

    public function create()
    {
        $notice=Notice::init();
        $notice = $this->initNotice();
		$attachment=$this->initAttachment();
			
       
        return view('notices.edit')->with([
            'notice' =>  $notice,
            'attachment' =>  $attachment,
            'canEdit'=> true,
            'canReview' => false,
            'canDelete' => false
        ]);
    }

    public function show($id)
    {
        $entity = $this->noticeService->getById($id);
        if(!$entity) abort(404);

        

        $model=$this->setAuthority($entity);

        $model['notice']=$this->initNotice($entity);

        if(count($entity->attachments)){
            $model['attachment']=$this->initAttachment($entity->attachments->first());
        }else{
            $model['attachment']=$this->initAttachment();
        }
       
      
        return view('notices.edit')->with($model);
    }

    public function store()
    {        
        $user = $this->currentUser();

        $values=$this->getPostedValues();
        $values['unit_id']=$user->unit->id;
        $values['created_by']=$user->number;

        $notice=new Notice($values);
        $attachment=$this->getPostedAttachment();
        if($attachment){
            DB::transaction(function() use($notice,$attachment) {
                $notice->save();
                $notice->attachments()->save($attachment);
            });
        }else{

            $notice->save();
           
        }

        return redirect('/notices');
       
    }

    public function update($id)
    {
        $notice = $this->noticeService->getById($id);
        if(!$notice) abort(404);

        $authority=$this->setAuthority($notice);
        
        if(!$authority['canEdit']) dd('資料無法修改');


        $user = $this->currentUser();

        $values=$this->getPostedValues();
        $values['unit_id']=$user->unit->id;
        $values['updated_by']=$user->number;

        $notice->update($values);

        $attachment=$this->getPostedAttachment();
        if($attachment){
       
            $notice->saveAttachments([$attachment]);
        }

        return redirect('/notices');
    }

   

    public function deleteAttachment($id)
    {
        $attachment=Attachment::findOrFail($id);

        $authority=$this->setAuthority($attachment->notice);

        if(!$authority['canEdit']) abort(500);   //dd('資料無法修改');

        $attachment->delete();

        return response()->json();
       
    }

    public function approve()
    {
        $id=$_POST['Id'];
        $notice = Notice::findOrFail($id);

        $authority=$this->setAuthority($notice);
        
        if(!$authority['canReview'])  abort(500);  //權限不足

        $user = $this->currentUser();

       
        $values['unit_id']=$user->unit->id;
        $values['updated_by']=$user->number;
        
        $notice->reviewed=true;
        $notice->reviewed_by=$user->number;

        $notice->save();

        
        //審核通過,發送訊息
        $this->noticeService->sendNotice($notice);
        
        return redirect('/notices'); 
    }


    function initNotice(Notice $notice=null)
    {
       
        if($notice){
            return array(
                'Id' => $notice->id , 
                'Content' => $notice->content , 
                'Staff' => $notice->staff ,
                'Teacher' => $notice->teacher , 
                'Student' => $notice->student , 
                'Reviewed' => $notice->reviewed , 
                'ReviewedBy' => $notice->reviewed_by , 
                'Units'  => $notice->units , 
                'Classes' => $notice->classes , 
                'Levels'  => $notice->levels , 
                'PS' => $notice->ps , 
                'UpdatedAt' => $notice->updated_at
                
            );
        }
        return array(
                'Id' => 0 , 
                'Content' => '',
                'Staff' => 0,
                'Teacher' => 0,
                'Student' => 0,
                'Reviewed' => 0,
                'Units' => '',
                'Classes' => '',
                'Levels' => '',
                
                'PS' => '',

            );
            
    }
    function initAttachment(Attachment $attachment=null)
    {
        if($attachment){
            return array(
                'Id' => $attachment->id , 		
                'Notice_Id' => $attachment->notice_id , 
                'Title' => $attachment->title , 
                'Name' => $attachment->name , 
                'Type' => $attachment->type , 
                'FileData' => $attachment->file_data , 

            );
        }
        return array(
                'Id' => 0 , 		
                'Notice_Id' => 0,
                'Title' => '',
                'Name' => '',
                'Type' => '',
                'FileData' => '',

        );
        
    }

    private function getPostedValues()
    {
       
        $content = $_POST['Content'];

        $staff=false;
        if (isset($_POST['Staff'])) $staff=true;

        $teacher=false;
        if (isset($_POST['Teacher'])) $teacher=true;

        $student=false;
        if (isset($_POST['Student'])) $student=true;

        $levels=false;
        if (isset($_POST['Student'])) $student=true;

        
        $units=$_POST['Units'];
        $classes=$_POST['Classes'];
        $levels=$_POST['Levels'];
        $ps=$_POST['PS'];
        $reviewed=$_POST['Reviewed'];

        $values=[
            'content' => $content,
            'staff' => $staff,
            'teacher' => $teacher,
            'student' => $student,
            'units' => $units,
            'classes' => $classes,
            'levels' => $levels,
            'ps' => $ps,
            'reviewed' => false,			
        ];
        
        return $values;
        
    }

    function getPostedAttachment()
    {
        $has_file= false;
        if(isset($_FILES['Attachment'])){
            if (is_uploaded_file($_FILES['Attachment']['tmp_name'])) $has_file=true;
        }
        
        if(!$has_file) return null;

        $file_name = $_FILES['Attachment']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $file_title = $file_name;
        if (isset($_POST['Attachment_Title'])){
            $file_title =$_POST['Attachment_Title'];
        } 

        $file_data=base64_encode(file_get_contents($_FILES['Attachment']['tmp_name']));

        return new Attachment([
            'title' => $file_title,
            'name' => $file_name,
            'type' => $file_ext,
            'file_data' => $file_data
        ]);


    }

    

    
    
   
}
