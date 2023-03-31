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


Auth::routes([
    'register' => false, // Register Routes...
  
    'reset' => false, // Reset Password Routes...
  
    'verify' => false, // Email Verification Routes...
  ]);


Route::get('/', function () {
    return response()->redirectTo('/login');
});

Route::get('/offline', function () {
    return view('vendor/laravelpwa/offline');
});

Route::get('dashboard', 'HomeController@index')->name('home');
Route::get('lock', 'PageController@lock')->name('page.lock')->middleware('guest');

Route::group(['middleware' => 'auth'], function () {

    Route::get('logs/export/', 'HomeController@export')->name('export.logs');

    Route::get('profile', 'ProfileController@edit')->name('profile.edit');
    Route::patch('profile/update', 'ProfileController@update')->name('profile.update');
    Route::patch('profile/password', 'ProfileController@password')->name('profile.password');   
    Route::resource('role', 'RoleController', ['except' => ['show']]);
    Route::resource('user', 'UserController', ['except' => ['show']]);
    Route::resource('hotel', 'HotelController', ['except' => ['show']]);
    Route::get('hotel/report', 'HotelController@report')->name("hotel.report");
   

    Route::get('{page}', 'PageController@index')->name('page.index');

    
});
