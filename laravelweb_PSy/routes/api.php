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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix'=>'auth'], function()
{
	
	Route::post('login', 'AuthController@login');
	Route::post('logout', 'AuthController@logout');
	Route::post('isLogin', 'AuthController@me');


});


Route::group(['prefix' => 'admin', 'middleware' => 'RoleAdmin'], function()
{
	//iot
	Route::get('/iot/configGateway', 'IotController@configGateway');
	Route::get('/iot/runPython', 'IotController@runPython');
	

	//dashboard
	Route::get('/shifts/graph', 'ShiftsController@graph');
	Route::get('/users/{id}/getAllShifts', 'UserController@getAllShifts');

	//shift today
	Route::get('/shifts/shifttoday', 'ShiftsController@getShiftToday');

	//get histories
	Route::get('/shifts/{id}/getHistories', 'ShiftsController@getHistories');

	//route resource
	Route::resource('shifts', 'ShiftsController');
	Route::resource('floors', 'FloorController');
	Route::resource('buildings', 'BuildingController');
	Route::resource('gateways', 'GatewayController');
	Route::resource('rooms', 'RoomController');
	Route::resource('photos', 'PhotoController');
	Route::resource('status_nodes', 'StatusNodeController');
	Route::resource('times', 'TimeController');
	Route::resource('users', 'UserController');

});

Route::group(['prefix' => 'guard', 'middleware' => 'RoleGuard'], function()
{
	//additional route
	//user
	Route::get('/users/shifts', 'UserController@shifts');
	Route::get('/users/getMasterData', 'UserController@getMasterData');
	Route::get('/users/viewHistoryScan/{id}', 'UserController@viewHistoryScan');
	Route::post('/users/submitScan', 'UserController@submitScan');
	

	//Route::resource('shifts', 'AndroidShiftController');
});





//Route::post('')
