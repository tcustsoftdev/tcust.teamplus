<?php


Route::post('/auth', 'SessionsController@store');


Route::post('/subs', function () {
    $numbers=request()->numbers;
    $numbers=explode(',',$numbers);

    return response() ->json($numbers);
   
});

Route::get('/', function () {
    $members=['ss355','ss678'];
    if(!in_array("ss3cc55", $members)){
        array_push( $members, 'ikikik');
    }
  dd($members);
    // $service =new \App\Repositories\Teamplus\Groups();
    // $service->create(['ss355'],'','ss355','plpcsssssssl');


});




Route::group(['middleware' => 'admin'], function(){

    Route::resource('/notices', 'NoticesController');
    Route::post('/notices/approve', 'NoticesController@approve');
    Route::delete('/attachment/{id}', 'NoticesController@deleteAttachment');
   

});
