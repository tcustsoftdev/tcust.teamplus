<?php


Route::post('/auth', 'SessionsController@store');


Route::post('/subs', function () {
    $numbers=request()->numbers;
    $numbers=explode(',',$numbers);

    return response() ->json($numbers);
   
});

Route::get('/', function () {
  
    // $service =new \App\Repositories\Teamplus\Groups();
    // $service->create(['ss355'],'','ss355','plpcsssssssl');


});




Route::group(['middleware' => 'admin'], function(){

    Route::resource('/notices', 'NoticesController');
    Route::post('/notices/approve', 'NoticesController@approve');
    Route::delete('/attachment/{id}', 'NoticesController@deleteAttachment');
   

});
