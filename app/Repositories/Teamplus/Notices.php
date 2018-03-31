<?php

namespace App\Repositories\Teamplus;

use App\Repositories\Teamplus\BaseTeamPlusRepo;

use App\Teamplus\TPChannelReceiver;
use App\Teamplus\TPSuperGroupMapping;
use App\Teamplus\TPUserGroup;
use App\Teamplus\TPUser;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

use Config;
use Carbon\Carbon;


class Notices extends BaseTeamPlusRepo
{
    protected $api_key;
    protected $api_sn;
    public function __construct()
    {
        parent::__construct();
        $this->api_key=Config::get('teamplus.notice.api_key');
        $this->ch_sn=Config::get('teamplus.notice.ch_sn');

        $this->channel_id=Config::get('teamplus.notice.channel_id');
          
    }

    private function checkReceivers(array $accounts)
    {
          $receiver_ids=$this->getReceivers();

          $account_ids=TPUser::whereIn('LoginName',$accounts)->pluck('UserNo')->toArray();

          $not_in_users = array_diff ($account_ids, $receiver_ids);

          if(count($not_in_users)){
              $not_in_users=array_flatten($not_in_users);
              for($i = 0; $i < count($not_in_users); ++$i) {
                  $this->addReceiver($not_in_users[$i]);
              }

          }

    }

    private function getReceivers()
    {
         $receivers=TPChannelReceiver::where('ChannelID',$this->channel_id)->pluck('SG_ID')->toArray();
         $group_ids=TPSuperGroupMapping::whereIn('SG_ID',$receivers)->pluck('GROUP_ID')->toArray();
         $user_ids=TPUserGroup::whereIn('GROUP_ID',$group_ids)->pluck('USER_NO')->toArray();
         return $user_ids;

    }
    private function addReceiver($user_id)
    {
        $user_group=TPUserGroup::where('USER_NO',$user_id)->where('GROUP_TYPE',1)->first();
        if(!$user_group) return;

        $mapping=TPSuperGroupMapping::where('GROUP_ID',$user_group->GROUP_ID)->first();
        if(!$mapping) return;

        $sg_id=$mapping->SG_ID;
        
        return TPChannelReceiver::create([
            'ChannelID'=>$this->channel_id,
            'SG_ID' => $sg_id,
            'CreateTime' => Carbon::now(),
            'UpdateTime' => Carbon::now()
        ]);

    }

    public function upload($file_type,$file_data)
    {
        $url= $this->api_url . '/SuperHubService.ashx?ask=uploadFile';

        $client = new Client(); 
        $response = $client->request('POST', $url, [
            'form_params' => [
                'ch_sn' => $this->ch_sn,
                'api_key' => $this->api_key,
                'file_type' => $file_type,
                'data_binary' => $file_data
                
            ]
        ]);

        $body =  json_decode($response->getBody());
        return $body;

    }

    public function create(array $accounts,$type, $content, $file, $file_name)
    {
        
        $url= $this->api_url . '/SuperHubService.ashx?ask=sendMessage';
      
       // $this->checkReceivers($accounts);
        $client = new Client(); 
        $response = $client->request('POST', $url, [
            'form_params' => [
                'ch_sn' => $this->ch_sn,
                'api_key' => $this->api_key,
                'content_type' => $type,
                'text_content' => $content,
                'media_content' => $file,
                'file_show_name' => $file_name,
                'msg_push' => '',
                'account_list' => json_encode($accounts),
            ]
        ]);

        

        $body =  json_decode($response->getBody());
      
        return $body;

    }
    

    // private function saveFileToDB()
    // {
    //      $path= $_SERVER['DOCUMENT_ROOT'].'/images/uploads/test.png';
    //      if(file_exists($path))
    //      {
    //         $ext = pathinfo($path, PATHINFO_EXTENSION);
    //         $fileData = file_get_contents($path);
           
    //          return base64_encode($fileData);
    //      }  

    // }
    
}