<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolNoticeAttachmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_notice_attachment', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('notice_id');

            $table->string('display_name');
            $table->string('file_type');
            $table->text('file_data');
            
           
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
        Schema::dropIfExists('school_notice_attachment');
    }
}
