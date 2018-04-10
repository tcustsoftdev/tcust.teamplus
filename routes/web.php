<?php


Route::post('/auth', 'SessionsController@store');


Route::post('/subs', function () {
    $numbers=request()->numbers;
    $numbers=explode(',',$numbers);

    return response() ->json($numbers);
   
});

Route::get('/', function () {
   


});




Route::group(['middleware' => 'admin'], function(){

    Route::resource('/notices', 'NoticesController');
    Route::post('/notices/approve', 'NoticesController@approve');
    Route::delete('/attachment/{id}', 'NoticesController@deleteAttachment');
   

});
