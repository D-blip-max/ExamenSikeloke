<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
   // return view('welcome');
     return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//ruta para bitacora
Route::get('/admin/bitacora', [App\Http\Controllers\BitacoraController::class, 'index'])->name('admin.bitacora.index')->middleware('auth','can:admin.bitacora.index');


//rutas que van a ser de gestiones del sitema CreateReadUpdateDelete
//trabajando con vistas
Route::get('/admin/gestiones', [App\Http\Controllers\GestionController::class, 'index'])->name('admin.gestiones.index')->middleware('auth','can:admin.gestiones.index');
Route::get('/admin/gestiones/create', [App\Http\Controllers\GestionController::class, 'create'])->name('admin.gestiones.create')->middleware('auth','can:admin.gestiones.create');//retorna la vista
Route::post('/admin/gestiones/create', [App\Http\Controllers\GestionController::class, 'store'])->name('admin.gestiones.store')->middleware('auth','can:admin.gestiones.store');//Create
Route::get('/admin/gestiones/{id}/edit', [App\Http\Controllers\GestionController::class, 'edit'])->name('admin.gestiones.edit')->middleware('auth','can:admin.gestiones.edit');//Read
Route::put('/admin/gestiones/{id}', [App\Http\Controllers\GestionController::class, 'update'])->name('admin.gestiones.update')->middleware('auth','can:admin.gestiones.update');//Update
Route::delete('/admin/gestiones/{id}', [App\Http\Controllers\GestionController::class, 'destroy'])->name('admin.gestiones.destroy')->middleware('auth','can:admin.gestiones.destroy');//Delete



//rutas que van a ser de Roles del sitema CreateReadUpdateDelete
//trabajando con vistas
Route::get('/admin/roles', [App\Http\Controllers\RoleController::class, 'index'])->name('admin.roles.index')->middleware('auth','can:admin.roles.index');
Route::get('/admin/roles/create', [App\Http\Controllers\RoleController::class, 'create'])->name('admin.roles.create')->middleware('auth','can:admin.roles.create');//retorna la vista
Route::post('/admin/roles/create', [App\Http\Controllers\RoleController::class, 'store'])->name('admin.roles.store')->middleware('auth','can:admin.roles.store');//Create
Route::get('/admin/roles/{id}/edit', [App\Http\Controllers\RoleController::class, 'edit'])->name('admin.roles.edit')->middleware('auth','can:admin.roles.edit');//Read
Route::put('/admin/roles/{id}', [App\Http\Controllers\RoleController::class, 'update'])->name('admin.roles.update')->middleware('auth','can:admin.roles.update');//Update
Route::delete('/admin/roles/{id}', [App\Http\Controllers\RoleController::class, 'destroy'])->name('admin.roles.destroy')->middleware('auth','can:admin.roles.destroy');//Delete
//el metodo que da permisos
Route::get('/admin/roles/{id}/permisos', [App\Http\Controllers\RoleController::class, 'permisos'])->name('admin.roles.permisos')->middleware('auth','can:admin.roles.permisos');//el que da permisos che
Route::post('/admin/roles/{id}', [App\Http\Controllers\RoleController::class, 'update_permisos'])->name('admin.roles.update_permisos')->middleware('auth','can:admin.roles.update_permisos');//el que da permisos che



//rutas que van a ser de grupos del sitema CreateReadUpdateDelete
//Trabajando con Modals
Route::get('/admin/grupos', [App\Http\Controllers\GrupoController::class, 'index'])->name('admin.grupos.index')->middleware('auth','can:admin.grupos.index');
Route::post('/admin/grupos/create', [App\Http\Controllers\GrupoController::class, 'store'])->name('admin.grupos.create')->middleware('auth','can:admin.grupos.create');//Create
Route::put('/admin/grupos/{id}', [App\Http\Controllers\GrupoController::class, 'update'])->name('admin.grupos.update')->middleware('auth','can:admin.grupos.update');//Update
Route::delete('/admin/grupos/{id}', [App\Http\Controllers\GrupoController::class, 'destroy'])->name('admin.grupos.destroy')->middleware('auth','can:admin.grupos.destroy');//Delete




