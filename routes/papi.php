<?php

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

Route::group(['namespace' => 'Api', 'middleware' => 'auth'], function() {
	Route::get('users/current', 'UsersApiController@getCurrentUser');

	Route::get('courses/{id}/nav', 'CoursesApiController@getNavigation');
});
