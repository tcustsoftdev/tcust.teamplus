<?php

use Illuminate\Http\Request;


Route::post('/notices', '\App\Http\Controllers\Api\NoticesController@store');

Route::post('/departments', '\App\Http\Controllers\Api\DepartmentsController@store');
Route::post('/users', '\App\Http\Controllers\Api\UsersController@store');

Route::get('/units', '\App\Http\Controllers\Api\UnitsController@index');
Route::get('/units/getByCodes/{codes}', '\App\Http\Controllers\Api\UnitsController@getByCodes');

Route::get('/classes', '\App\Http\Controllers\Api\ClassesController@index');
Route::get('/classes/getByCodes/{codes}', '\App\Http\Controllers\Api\ClassesController@getByCodes');


