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
    return view('home');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/listarEventos', 'EventoController@listar')->name('listar_evento')->middleware('auth');

Route::get('/cadastrarEvento', 'EventoController@AddView')->name('view_add_evento')->middleware('auth');
Route::get('/editarEvento/{id}', 'EventoController@EditView')->name('view_edit_evento')->middleware('auth');
Route::post('/salvarNovoEvento', 'EventoController@insert')->name('add_evento')->middleware('auth');