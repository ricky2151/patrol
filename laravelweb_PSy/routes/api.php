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
	Route::name('isLogin')->post('isLogin', 'AuthController@isLogin');


});

//for node device
Route::name('testConnectionIoT')->get('/acknowledges/testConnection', 'AcknowledgeController@testConnection');
Route::resource('acknowledges', 'AcknowledgeController')->only([
	'index', 'store', 'destroy'
]);


Route::group(['prefix' => 'admin', 'middleware' => 'RoleAdmin', 'as'=>'admin.'], function()
{
	//iot
	Route::name('configGateway')->get('/iot/configGateway', 'IotController@configGateway');

	//dashboard
	Route::name('graph')->get('/shifts/graph', 'ShiftsController@graph');

	//get detail shift on specific user
	Route::name('getAllShifts')->get('/users/{user}/getAllShifts', 'UserController@getAllShifts');

	//shift today
	Route::name('getShiftToday')->get('/shifts/shifttoday', 'ShiftsController@getShiftToday');

	//get histories
	Route::name('getHistories')->get('/shifts/{shift}/getHistories', 'ShiftsController@getHistories');

	//remove all shifts that have history
	Route::name('removeAndBackup')->post('/shifts/removeAndBackup', 'ShiftsController@removeAndBackup');

	//route resource
	Route::resource('shifts', 'ShiftsController')->only([
		'index', 'destroy'
	]);
	Route::resource('floors', 'FloorController')->except([
		'create', 'show'
	]);
	Route::resource('buildings', 'BuildingController')->except([
		'create', 'show'
	]);
	Route::resource('gateways', 'GatewayController')->except([
		'create', 'show'
	]);
	Route::resource('rooms', 'RoomController')->except([
		'show'
	]);
	Route::resource('photos', 'PhotoController')->only([
		'index', 'destroy'
	]);
	Route::resource('status_nodes', 'StatusNodeController')->except([
		'create', 'show'
	]);
	Route::resource('times', 'TimeController')->except([
		'create', 'show'
	]);
	Route::resource('users', 'UserController')->except([
		'show'
	]);
});

Route::group(['prefix' => 'guard', 'middleware' => 'RoleGuard', 'as'=>'guard.'], function()
{
	//additional route
	//user
	Route::name('shifts')->get('/users/shifts', 'UserController@shifts');
	Route::name('getMasterData')->get('/users/getMasterData', 'UserController@getMasterData');
	Route::name('viewHistoryScan')->get('/users/viewHistoryScan/{shift}', 'UserController@viewHistoryScan');
	Route::name('submitScan')->post('/users/submitScan', 'UserController@submitScan');
	

});

Route::get('/debug-sentry', function () {
    throw new \App\Exceptions\LoginFailedException("lskadf");
});



Route::any('{any}', function(){
    return response()->json([
        'status'    => false,
        'message'   => 'API Not Found.',
    ], 404);
})->where('any', '.*');
