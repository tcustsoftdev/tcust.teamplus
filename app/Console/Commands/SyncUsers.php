<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Unit;
use App\User;
use App\Repositories\TPSync\Users;
use App\Teamplus\TPUserForSync;
use Log;

class Syncusers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    

    public function __construct(Users $TPUsers) 
    {
        parent::__construct();

        $this->TPUsers=$TPUsers;
       
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('max_execution_time', 1200);
        
        
        TPUserForSync::truncate();


        $units=Unit::all();
        foreach($units as $unit){

            foreach($unit->users as $user){
                //teamplus 帳號是否存在
                $number=$user->number;
                $existTPUser=$this->TPUsers->isUserExist($number);

                
                $password= $existTPUser ? '' : $user->password;
                $email=$user->email;
                $name=$user->name;
                $departmentCode=$unit->code;
                $status= $user->active ? 1 : 3;   //1在職 2停用 3離職

                $this->TPUsers->syncUser($number, $password ,$email, $name, $departmentCode, $status);

            }

        }
       
        Log::info('Sync users Has Done.');
        $this->info('Sync users Has Done.');
    }

    
}
