<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupSyncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_sync', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tp_id')->default(0);
            $table->string('parent')->nullable();
            $table->string('code');	
            $table->string('name');		
            $table->text('admin');				
            $table->text('members');
            $table->boolean('is_delete')->default(false);

            $table->boolean('sync')->default(false);
            $table->string('msg')->nullable();		
            $table->boolean('success')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('group_sync');
    }
}
