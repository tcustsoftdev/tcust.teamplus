<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Services\UnitsService;
use App\Repositories\Schools;
use App\User;
use Log;

class SyncStaff extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:staff';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Staff';

    /**
     * Create a new command instance.
     *
     * @return void
     */
   

    public function __construct(UnitsService $unitsService,Schools $schools)            
    {
        parent::__construct();

        $this->units=$unitsService;
        $this->schools=$schools;
       
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('max_execution_time', 1200);
        
        //更新現有職員與教師狀態
        $existStaffUsers=User::whereIn('role',['Staff','Teacher'])->get();
        $this->syncActive($existStaffUsers);
        $this->syncStaffsByUnits();


        Log::info('Sync Staff Has Done.');
        $this->info('Sync Staff Has Done.');
    }

    function syncActive($users)
    {
        foreach($users as $staffUser){
            //取得對應之學校職員/教師資料
            $staffNumber=$staffUser->number;
            $schoolStaff= $this->schools->getStaffByNumber($staffNumber);
            if($schoolStaff){
                $staffUser->active=$schoolStaff->isActive();
            }else{
                $staffUser->active=false;
            }

            $staffUser->save();
        }

    }

    function syncStaffsByUnits()
    {
        $allUnits = $this->units->getAll()->get();
        
        foreach($allUnits as $unit){
            
            //取得學校職員資料
            $staffsInUnit=$this->schools->getStaffsByUnit($unit->code)->get();
            
           
            $staffsInUnit = $staffsInUnit->filter(function ($item) {
                return $item->isActive();
            })->all();
           

            foreach($staffsInUnit as $schoolStaff){
                
                $userValues=User::initFromStaff($schoolStaff,$schoolStaff->getRole());
                $userValues['unit_id'] = $unit->id;

                $this->createOrUpdateUser($userValues);

            }
            
        }
    }

    function createOrUpdateUser(array $userValues)
    {
        $number=$userValues['number'];
        $user=User::where('number',$number)->first();
        if($user){
            $user->update($userValues);
        }else{
           
            $userValues['password'] = config('app.auth.password');
            User::create($userValues);
        }
    }


}
