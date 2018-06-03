<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Services\ClassesService;
use App\Repositories\Schools;
use Log;

class SyncStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:students';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Students';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Schools $schools,ClassesService $classesService) 
    {
        parent::__construct();

        $this->classesService=$classesService;
        $this->schools=$schools;
    }
    

    public function handle()
    {
        ini_set('max_execution_time', 1200);

        //更新現有學生狀態
        $role=User::studentRoleName();
        $existStudentUsers=User::where('role',$role)->get();
        foreach($existStudentUsers as $studentUser){
            //取得對應之學校學生資料
            $this->updateStudentStatus($studentUser);
        }

        $allClasses = $this->classesService->getAll()->get();
        foreach($allClasses as $classEntity){
            
            //取得學校學生資料
            $studentsInClass=$this->schools->getStudentsByClass($classEntity->code)->get();
           

            foreach($studentsInClass as $schoolStudent){

                if($schoolStudent->isActive()){
                    $this->syncSchoolStudent($schoolStudent,$classEntity);
                }

            }
            
        }

        Log::info('Sync Students Has Done.');
        $this->info('Sync Students Has Done.');
    }

    

    function syncSchoolStudent($schoolStudent,$classEntity)
    {
        $userValues=User::initFromSchoolStudent($schoolStudent);
        $userValues['unit_id'] = $classEntity->id;
        $userValues['active'] = true;
        $this->createOrUpdateUser($userValues);
    }

    function updateStudentStatus($studentUser)
    {
        //取得對應之學校學生資料
        $studentNumber=$studentUser->number;
        $schoolStudent= $this->schools->getStudentByNumber($studentNumber);
        if($schoolStudent){
            $studentUser->active=$schoolStudent->isActive();
        }else{
            $studentUser->active=false;
        }

        $studentUser->save();
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
