<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffUpdateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_update_records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');	
            $table->string('number');		
            $table->string('department');				
            $table->string('email');
            $table->string('password')->nullable();
            $table->string('job_title')->nullable();
            $table->string('extend')->nullable();

            $table->integer('status');

            $table->boolean('done')->default(false);
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
        Schema::dropIfExists('staff_update_records');
    }
}
