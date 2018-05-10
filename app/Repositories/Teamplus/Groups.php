<?php

namespace App\Repositories\Teamplus;

use App\Repositories\Teamplus\BaseTeamPlusRepo;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

use Config;


class Groups extends BaseTeamPlusRepo
{
    protected $api_key;
    protected $api_sn;

    public function __construct()
    {
        parent::__construct();
        $this->api_key=Config::get('teamplus.system.api_key');
        $this->api_sn=Config::get('teamplus.system.api_sn');
        $this->company_admin=Config::get('teamplus.system.company_admin');
          
    }

   
    
    public function create(array $members, $owner, $manager ,$name)
    {
        $url= $this->api_url . '/SystemService.ashx?ask=createTeam';
        $manager_list=[$manager];

        $subject=$name . '成員專屬團隊';
        $description=$name . '成員的訊息交換平台';

        if(!$owner) $owner=$this->company_admin;
        
       
        $client = new Client(); 
        $response = $client->request('POST', $url, [
            'form_params' => [
                'system_sn' => $this->api_sn,
                'api_key' => $this->api_key,
                'owner' => $owner,
                'name' => $name,
                'team_type' => 1,    //封閉式
                'subject' => $subject,
                'description' => $description,
                'icon' => '',
                'member_list' => json_encode($members),
                'manager_list' => json_encode($manager_list),
            ]
        ]);

        $body =  json_decode($response->getBody());

        return $body->TeamSN;

    }
    public function update($team_id , $name)
    {
       
        $url= $this->api_url . '/SystemService.ashx?ask=modifyTeamInfo';

        $subject=$name . '成員專屬團隊';
        $description=$name . '成員的訊息交換平台';

        $operator=$this->company_admin;
        
        $client = new Client(); 
        $response = $client->request('POST', $url, [
            'form_params' => [
                'system_sn' => $this->api_sn,
                'api_key' => $this->api_key,
                'team_sn' => $team_id,
                'operator' => $this->company_admin,
                'name' => $name,
                'team_type' => 1,    //封閉式
                'subject' => $subject,
                'description' => $description,
                
            ]
        ]);

        $body =  json_decode($response->getBody());
       
        return $body;

    }
    public function details($team_id)
    {
        $url= $this->api_url . '/SystemService.ashx?ask=getTeamInfo';
        $client = new Client(); 
        $response = $client->request('POST', $url, [
            'form_params' => [
                'system_sn' => $this->api_sn,
                'api_key' => $this->api_key,
                'team_sn'=>$team_id,
            ]
        ]);

        $body =  json_decode($response->getBody()); 
        return $body;
    }
    
    public function addMembers(array $members,$team_id)
    {
        
        
        $url= $this->api_url . '/SystemService.ashx?ask=inviteTeamMember';
       
        $client = new Client(); 
        $response = $client->request('POST', $url, [
            'form_params' => [
                'system_sn' => $this->api_sn,
                'api_key' => $this->api_key,
                'team_sn' => $team_id,
                'operator' => $this->company_admin,
                'member_list' => json_encode($members)
            ]
        ]);

       

        $body =  json_decode($response->getBody());
     
        return $body;
    }
    public function removeMembers(array $members,$team_id)
    {
       
        $url= $this->api_url . '/SystemService.ashx?ask=deleteTeamMember';

        $client = new Client(); 
        $response = $client->request('POST', $url, [
            'form_params' => [
                'system_sn' => $this->api_sn,
                'api_key' => $this->api_key,
                'team_sn' => $team_id,
                'operator' => $this->company_admin,
                'member_list' => json_encode($members)
            ]
        ]);

        $body =  json_decode($response->getBody());
        return $body;
    }

    public function addManager($team_id,$manager)
    {
        
        $url= $this->api_url . '/SystemService.ashx?ask=assignTeamManager';

        $manager_list=[$manager];
       
        $client = new Client(); 
        $response = $client->request('POST', $url, [
            'form_params' => [
                'system_sn' => $this->api_sn,
                'api_key' => $this->api_key,
                'team_sn' => $team_id,
                'operator' => $this->company_admin,
                
                'manager_list' => json_encode($manager_list)
                
            ]
        ]);

        $body =  json_decode($response->getBody());
       
        return $body;
    }
    public function removeManager($team_id,$manager)
    {
        
        if($manager==$this->company_admin) return;

        $url= $this->api_url . '/SystemService.ashx?ask=cancelTeamManager';

        $manager_list=[$manager];
            
        $client = new Client(); 
        $response = $client->request('POST', $url, [
            'form_params' => [
                'system_sn' => $this->api_sn,
                'api_key' => $this->api_key,
                'team_sn' => $team_id,
                'operator' => $this->company_admin,
                
                'manager_list' => json_encode($manager_list)
                
            ]
        ]);

        $body =  json_decode($response->getBody());
        return $body;
    }
    public function delete($team_id, $owner='')
    {
        if(!$owner) $owner=$this->company_admin;

        $url= $this->api_url . '/SystemService.ashx?ask=deleteTeam';
        $client = new Client(); 
        $response = $client->request('POST', $url, [
            'form_params' => [
                'system_sn' => $this->api_sn,
                'api_key' => $this->api_key,
                'team_sn'=>$team_id,
                'owner' => $owner,
            ]
        ]);

        $body =  json_decode($response->getBody()); 
        return $body;

    }
    
}