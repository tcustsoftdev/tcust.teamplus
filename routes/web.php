<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

});

Route::get('/api/units', function () {
    
    $unitsService=new \App\Services\UnitsService();
    $units = $unitsService->getAll()->orderBy('code')->get();

    return response()->json($units);
   
});

Route::get('/api/classes', function () {
    
    $classesService=new \App\Services\ClassesService();
    $classes = $classesService->getAll()->orderBy('code')->get();

    return response()->json($classes);
   
});
