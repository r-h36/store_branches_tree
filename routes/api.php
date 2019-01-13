<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * 
 */
Route::get('/branches', 'BranchController@index')->name('branch.index');
Route::get('/branches/{id}', 'BranchController@view')->name('branch.view');
Route::get('/branches/{id}/with-children', 'BranchController@viewWithChildren')->name('branch.viewWithChildren');

Route::post('/branches', 'BranchController@create')->name('branch.create');
Route::patch('/branches/{id}', 'BranchController@update')->name('branch.update');
Route::delete('/branches/{id}', 'BranchController@delete')->name('branch.delete');
Route::post('/branches/{id}/move-to/{toBranchId}', 'BranchController@move')->name('branch.move');