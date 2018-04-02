<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentUpdateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_update_records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');	
            $table->string('number');		
            $table->string('department');				
            $table->string('email');
            $table->string('password')->nullable();
            
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
        Schema::dropIfExists('student_update_records');
    }
}
