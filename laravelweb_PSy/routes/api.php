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

Route::group(['prefix'=>'auth', 'as'=>'auth.'], function()
{
	
	Route::name('login')->post('login', 'AuthController@login');
	Route::name('logout')->post('logout', 'AuthController@logout');
	Route::name('me')->post('isLogin', 'AuthController@me');


});

//for node device
Route::name('testConnectionIoT')->get('/acknowledges/testConnection', 'AcknowledgeController@testConnection');
Route::resource('acknowledges', 'AcknowledgeController');



Route::group(['prefix' => 'admin', 'middleware' => 'RoleAdmin', 'as'=>'admin.'], function()
{
	//iot
	Route::name('configGateway')->get('/iot/configGateway', 'IotController@configGateway');
	Route::name('runPython')->get('/iot/runPython', 'IotController@runPython');
	

	//dashboard
	Route::name('graph')->get('/shifts/graph', 'ShiftsController@graph');
	Route::name('getAllShifts')->get('/users/{id}/getAllShifts', 'UserController@getAllShifts');

	//shift today
	Route::name('getShiftToday')->get('/shifts/shifttoday', 'ShiftsController@getShiftToday');

	//get histories
	Route::name('getHistories')->get('/shifts/{id}/getHistories', 'ShiftsController@getHistories');

	//remove all shifts that have history
	Route::name('removeAndBackup')->post('/shifts/removeAndBackup', 'ShiftsController@removeAndBackup');

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

Route::group(['prefix' => 'guard', 'middleware' => 'RoleGuard', 'as'=>'guard.'], function()
{
	//additional route
	//user
	Route::name('shifts')->get('/users/shifts', 'UserController@shifts');
	Route::name('getMasterData')->get('/users/getMasterData', 'UserController@getMasterData');
	Route::name('viewHistoryScan')->get('/users/viewHistoryScan/{id}', 'UserController@viewHistoryScan');
	Route::name('submitScan')->post('/users/submitScan', 'UserController@submitScan');
	

	//Route::resource('shifts', 'AndroidShiftController');
});





//Route::post('')
