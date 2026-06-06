<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');

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


// rutas que van a ser de post_grupos del sistema CreateReadUpdateDelete
Route::get('/admin/post_grupos', [App\Http\Controllers\PostGrupoController::class, 'index'])->name('admin.post_grupos.index')->middleware('auth','can:admin.post_grupos.index');
Route::post('/admin/post_grupos/create', [App\Http\Controllers\PostGrupoController::class, 'store'])->name('admin.post_grupos.create')->middleware('auth','can:admin.post_grupos.create');
Route::put('/admin/post_grupos/{id}', [App\Http\Controllers\PostGrupoController::class, 'update'])->name('admin.post_grupos.update')->middleware('auth','can:admin.post_grupos.update');
Route::delete('/admin/post_grupos/{id}', [App\Http\Controllers\PostGrupoController::class, 'destroy'])->name('admin.post_grupos.destroy')->middleware('auth','can:admin.post_grupos.destroy');//Delete


// rutas que van a ser de asignaciones del sistema CreateReadUpdateDelete
// Trabajando con Modals
Route::get('/admin/asignaciones', [App\Http\Controllers\AsignacionController::class, 'index'])->name('admin.asignaciones.index')->middleware('auth','can:admin.asignaciones.index');
Route::post('/admin/asignaciones/create', [App\Http\Controllers\AsignacionController::class, 'store'])->name('admin.asignaciones.create')->middleware('auth','can:admin.asignaciones.create');
Route::put('/admin/asignaciones/{id}', [App\Http\Controllers\AsignacionController::class, 'update'])->name('admin.asignaciones.update')->middleware('auth','can:admin.asignaciones.update');
Route::delete('/admin/asignaciones/{id}', [App\Http\Controllers\AsignacionController::class, 'destroy'])->name('admin.asignaciones.destroy')->middleware('auth','can:admin.asignaciones.destroy');//Delete


//rutas que van a ser de carreras del sitema CreateReadUpdateDelete
//Trabajando con Modals
Route::get('/admin/carreras', [App\Http\Controllers\CarreraController::class, 'index'])->name('admin.carreras.index')->middleware('auth','can:admin.carreras.index');
Route::post('/admin/carreras/create', [App\Http\Controllers\CarreraController::class, 'store'])->name('admin.carreras.create')->middleware('auth','can:admin.carreras.create');//Create
Route::put('/admin/carreras/{id}', [App\Http\Controllers\CarreraController::class, 'update'])->name('admin.carreras.update')->middleware('auth','can:admin.carreras.update');//Update
Route::delete('/admin/carreras/{id}', [App\Http\Controllers\CarreraController::class, 'destroy'])->name('admin.carreras.destroy')->middleware('auth','can:admin.carreras.destroy');//Delete



//rutas que van a ser de aulas del sitema CreateReadUpdateDelete
//Trabajando con Modals
Route::get('/admin/aulas', [App\Http\Controllers\AulaController::class, 'index'])->name('admin.aulas.index')->middleware('auth','can:admin.aulas.index');
Route::post('/admin/aulas/create', [App\Http\Controllers\AulaController::class, 'store'])->name('admin.aulas.create')->middleware('auth','can:admin.aulas.create');//Create
Route::put('/admin/aulas/{id}', [App\Http\Controllers\AulaController::class, 'update'])->name('admin.aulas.update')->middleware('auth','can:admin.aulas.update');//Update
Route::delete('/admin/aulas/{id}', [App\Http\Controllers\AulaController::class, 'destroy'])->name('admin.aulas.destroy')->middleware('auth','can:admin.aulas.destroy');//Delete



//rutas que van a ser de horarios del sitema CreateReadUpdateDelete
//Trabajando con Modals
Route::get('/admin/horarios', [App\Http\Controllers\HorarioController::class, 'index'])->name('admin.horarios.index')->middleware('auth','can:admin.horarios.index');
Route::post('/admin/horarios/create', [App\Http\Controllers\HorarioController::class, 'store'])->name('admin.horarios.create')->middleware('auth','can:admin.horarios.create');//Create
Route::put('/admin/horarios/{id}', [App\Http\Controllers\HorarioController::class, 'update'])->name('admin.horarios.update')->middleware('auth','can:admin.horarios.update');//Update
Route::delete('/admin/horarios/{id}', [App\Http\Controllers\HorarioController::class, 'destroy'])->name('admin.horarios.destroy')->middleware('auth','can:admin.horarios.destroy');//Delete



//rutas que van a ser de Turnos del sitema CreateReadUpdateDelete
//trabajando con vistas
Route::get('/admin/turnos', [App\Http\Controllers\TurnoController::class, 'index'])->name('admin.turnos.index')->middleware('auth','can:admin.turnos.index');
Route::get('/admin/turnos/create', [App\Http\Controllers\TurnoController::class, 'create'])->name('admin.turnos.create')->middleware('auth','can:admin.turnos.create');//retorna la vista
Route::post('/admin/turnos/create', [App\Http\Controllers\TurnoController::class, 'store'])->name('admin.turnos.store')->middleware('auth','can:admin.turnos.store');//Create
Route::get('/admin/turnos/{id}/edit', [App\Http\Controllers\TurnoController::class, 'edit'])->name('admin.turnos.edit')->middleware('auth','can:admin.turnos.edit');//Read
Route::put('/admin/turnos/{id}', [App\Http\Controllers\TurnoController::class, 'update'])->name('admin.turnos.update')->middleware('auth','can:admin.turnos.update');//Update
Route::delete('/admin/turnos/{id}', [App\Http\Controllers\TurnoController::class, 'destroy'])->name('admin.turnos.destroy')->middleware('auth','can:admin.turnos.destroy');//Delete


//rutas que van a ser de dias del sitema CreateReadUpdateDelete
//Trabajando con Modals
Route::get('/admin/dias', [App\Http\Controllers\DiaController::class, 'index'])->name('admin.dias.index')->middleware('auth','can:admin.dias.index');
Route::post('/admin/dias/create', [App\Http\Controllers\DiaController::class, 'store'])->name('admin.dias.create')->middleware('auth','can:admin.dias.create');//Create
Route::put('/admin/dias/{id}', [App\Http\Controllers\DiaController::class, 'update'])->name('admin.dias.update')->middleware('auth','can:admin.dias.update');//Update
Route::delete('/admin/dias/{id}', [App\Http\Controllers\DiaController::class, 'destroy'])->name('admin.dias.destroy')->middleware('auth','can:admin.dias.destroy');//Delete


//rutas que van a ser de config_porcentajes del sistema CreateReadUpdateDelete
//Trabajando con Modals
Route::get('/admin/config-porcentajes', [App\Http\Controllers\ConfigPorcentajeController::class, 'index'])->name('admin.config_porcentajes.index')->middleware('auth','can:admin.config_porcentajes.index');
Route::post('/admin/config-porcentajes/create', [App\Http\Controllers\ConfigPorcentajeController::class, 'store'])->name('admin.config_porcentajes.create')->middleware('auth','can:admin.config_porcentajes.create');//Create
Route::put('/admin/config-porcentajes/{id}', [App\Http\Controllers\ConfigPorcentajeController::class, 'update'])->name('admin.config_porcentajes.update')->middleware('auth','can:admin.config_porcentajes.update');//Update
Route::delete('/admin/config-porcentajes/{id}', [App\Http\Controllers\ConfigPorcentajeController::class, 'destroy'])->name('admin.config_porcentajes.destroy')->middleware('auth','can:admin.config_porcentajes.destroy');//Delete

//materias rutas
Route::get('/admin/materias', [App\Http\Controllers\MateriaController::class, 'index'])->name('admin.materias.index')->middleware('auth','can:admin.materias.index');
Route::post('/admin/materias/create', [App\Http\Controllers\MateriaController::class, 'store'])->name('admin.materias.create')->middleware('auth','can:admin.materias.create');//Create
Route::put('/admin/materias/{id}', [App\Http\Controllers\MateriaController::class, 'update'])->name('admin.materias.update')->middleware('auth','can:admin.materias.update');//Update
Route::delete('/admin/materias/{id}', [App\Http\Controllers\MateriaController::class, 'destroy'])->name('admin.materias.destroy')->middleware('auth','can:admin.materias.destroy');//Delete

//rutas que van a ser de docentes del sistema CreateReadUpdateDelete
//Trabajando con Modals y búsqueda en tiempo real
Route::get('/admin/docentes', [App\Http\Controllers\DocenteController::class, 'index'])->name('admin.docentes.index')->middleware('auth','can:admin.docentes.index');
Route::get('/admin/docentes/buscar', [App\Http\Controllers\DocenteController::class, 'buscar'])->name('admin.docentes.buscar')->middleware('auth','can:admin.docentes.index');
Route::post('/admin/docentes/create', [App\Http\Controllers\DocenteController::class, 'store'])->name('admin.docentes.create')->middleware('auth','can:admin.docentes.create');//Create
Route::put('/admin/docentes/{id}', [App\Http\Controllers\DocenteController::class, 'update'])->name('admin.docentes.update')->middleware('auth','can:admin.docentes.update');//Update
Route::delete('/admin/docentes/{id}', [App\Http\Controllers\DocenteController::class, 'destroy'])->name('admin.docentes.destroy')->middleware('auth','can:admin.docentes.destroy');//Delete

// rutas que van a ser de postulantes del sistema CreateReadUpdateDelete
// Trabajando con Modals y creación automática de usuario
Route::get('/admin/postulantes', [App\Http\Controllers\PostulanteController::class, 'index'])->name('admin.postulantes.index')->middleware('auth','can:admin.postulantes.index');
Route::post('/admin/postulantes/create', [App\Http\Controllers\PostulanteController::class, 'store'])->name('admin.postulantes.create')->middleware('auth','can:admin.postulantes.create');
Route::put('/admin/postulantes/{id}', [App\Http\Controllers\PostulanteController::class, 'update'])->name('admin.postulantes.update')->middleware('auth','can:admin.postulantes.update');
Route::delete('/admin/postulantes/{id}', [App\Http\Controllers\PostulanteController::class, 'destroy'])->name('admin.postulantes.destroy')->middleware('auth','can:admin.postulantes.destroy');//Delete

// rutas que van a ser de pagos del sistema CreateReadUpdateDelete
Route::get('/admin/pagos', [App\Http\Controllers\PagoController::class, 'index'])->name('admin.pagos.index')->middleware('auth','can:admin.pagos.index');
Route::post('/admin/pagos/create', [App\Http\Controllers\PagoController::class, 'store'])->name('admin.pagos.create')->middleware('auth','can:admin.pagos.create');
Route::put('/admin/pagos/{id}', [App\Http\Controllers\PagoController::class, 'update'])->name('admin.pagos.update')->middleware('auth','can:admin.pagos.update');
Route::delete('/admin/pagos/{id}', [App\Http\Controllers\PagoController::class, 'destroy'])->name('admin.pagos.destroy')->middleware('auth','can:admin.pagos.destroy');//Delete








