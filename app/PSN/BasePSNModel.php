<?php

namespace App\PSN;

use Illuminate\Database\Eloquent\Model;

class BasePSNModel extends Model
{
   protected $connection = 'sqlsrv_PSN';
}
