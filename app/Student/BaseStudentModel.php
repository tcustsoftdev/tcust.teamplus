<?php

namespace App\Student;

use Illuminate\Database\Eloquent\Model;

class BaseStudentModel extends Model
{
   protected $connection = 'sqlsrv_student';
}
