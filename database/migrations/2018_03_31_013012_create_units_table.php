<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent')->unsigned()->default(0);

            $table->integer('tp_id')->unsigned()->nullable();
            $table->string('code');	
			$table->string('name');			
            $table->text('description')->nullable(); 
            $table->integer('order')->default(0);

            $table->string('admin')->nullable();
            $table->text('level_ones')->nullable();
            $table->text('level_twos')->nullable();
            $table->boolean('is_class')->default(false);

            $table->boolean('active')->default(true);
            $table->boolean('removed')->default(false);	
           
            $table->string('err')->nullable();	
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('units');
    }
}
