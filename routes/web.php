<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('pages.index');
});

Route::resource('/', 'MachineController');
Route::post('/', 'MachineController@index')->name('search', 'MachineController@index');
