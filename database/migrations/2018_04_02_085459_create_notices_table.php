<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('unit_id')->unsigned();

            $table->text('content');	
            $table->boolean('staff')->default(false);
            $table->boolean('teacher')->default(false);
            $table->boolean('student')->default(false);
            $table->text('units')->nullable();
            $table->text('departments')->nullable();
            $table->text('classes')->nullable();
            $table->string('levels');

            $table->boolean('reviewed')->default(false);
            $table->string('reviewed_by')->nullable();

            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
		
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
        Schema::dropIfExists('notices');
    }
}
