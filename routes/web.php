<?php


Route::post('/auth', 'SessionsController@store');


Route::group(['middleware' => 'admin'], function(){

    Route::resource('/notices', 'NoticesController');
    Route::post('/notices/approve', 'NoticesController@approve');
   
    Route::delete('/attachment/{id}', 'NoticesController@deleteAttachment');

});
