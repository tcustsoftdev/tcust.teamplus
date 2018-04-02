<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolNoticeSyncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_notice_sync', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type_id');

            $table->text('text_content');
            $table->text('members');
            $table->text('media_content')->nullable();
            
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
        Schema::dropIfExists('school_notice_sync');
    }
}
