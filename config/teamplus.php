<?php

return [

    'main' =>[
        'api_url' =>  'http://203.64.37.41/API',
    ],

    'system' => [
        'api_key' => env('TEAMPLUS_SYSTEM_API_KEY', ''),
        'api_sn' => 1,
        'company_admin' => 'tpowner'
    ],

    'notice' => [
       
        'api_key' => env('TEAMPLUS_NOTICE_API_KEY', ''),        
        'ch_sn' => 1,
        'channel_id'=> '+886900000101',  //table: SuperHubManager
       
       
    ],

    'user' => [
        'password'=> '000000',

    ],

   

];
