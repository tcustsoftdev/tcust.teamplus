<?php

namespace App\Repositories\Teamplus;

use App\Repositories\Teamplus\BaseTeamPlusRepo;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

use Config;


class Events extends BaseTeamPlusRepo
{
    protected $api_key;
    protected $api_sn;
    public function __construct()
    {
        parent::__construct();
        $this->api_key=Config::get('teamplus.system.api_key');
        $this->api_sn=Config::get('teamplus.system.api_sn');
          
    }
    
   
    
    public function create($account, $name, $description, $start_time, $end_time)
    {
        $url= $this->api_url . '/CalendarService.ashx?ask=createEvent';
        
        $start_time=$start_time . ' +0800';
        $end_time=$end_time . ' +0800';

        

        $client = new Client(); 
        $response = $client->request('POST', $url, [
            'form_params' => [
                'system_sn' => $this->api_sn,
                'api_key' => $this->api_key,
                'account' => $account,
                'event_title' => $name,
                'event_description' => $description, 
                'event_start_time' => $start_time,
                'event_end_time' => $end_time,
                'event_remind_time' => ''
            ]
        ]);

        $body =  json_decode($response->getBody());
        return $body->EventID;

    }
    public function delete($event_id)
    {
        $url= $this->api_url . '/CalendarService.ashx?ask=deleteEvent';
        $client = new Client(); 
        $response = $client->request('POST', $url, [
            'form_params' => [
                'system_sn' => $this->api_sn,
                'api_key' => $this->api_key,
                'event_id'=>$event_id
            ]
        ]);

        $body =  json_decode($response->getBody()); 
        return $body->IsSuccess;

    }
    
}