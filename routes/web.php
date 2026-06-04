<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {    
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//ruta para vitacora
Route::get('/admin/bitacora', [App\Http\Controllers\BitacoraController::class, 'index'])->name('admin.vitacora.index')->middleware('auth');
