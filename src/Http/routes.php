<?php

//Route::group(['middleware' => 'auth', 'prefix' => 'adm'], function()
//{
    Route::get('/file/test',    ['as' => 'clean_file', 'uses' => 'Interpro\FileAggr\Http\FileOperationController@testpage']);
    Route::get('/file/clean',   ['as' => 'clean_file',  'uses' => 'Interpro\FileAggr\Http\FileOperationController@clean']);
    Route::post('/file/upload', ['as' => 'upload_file', 'uses' => 'Interpro\FileAggr\Http\FileOperationController@upload']);
//});
