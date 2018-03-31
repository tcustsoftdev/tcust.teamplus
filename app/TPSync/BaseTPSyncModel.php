<?php

namespace App\TPSync;

use Illuminate\Database\Eloquent\Model;

class BaseTPSyncModel extends Model
{
   protected $connection = 'sqlsrv_tp_sync';
}
