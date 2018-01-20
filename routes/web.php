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
    return view('welcome');
});

use App\Events\TestEvent; 
Route::get('/event', function () { 
    event(new TestEvent('Hey how are you !')); 
}); 
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/auth', 'AuthController@index');
