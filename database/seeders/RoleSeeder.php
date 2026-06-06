<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'Administrador']);
        Role::create(['name' => 'Docente']);
        Role::create(['name' => 'Estudiante']);
        Role::create(['name' => 'Coordinador Academico']);
        $admin = Role::findByName('Administrador');


        // --- PERMISOS PARA GESTIONES CRUD---
        Permission::create(['name' => 'admin.gestiones.index'])->syncRoles($admin);
        Permission::create(['name' => 'admin.gestiones.create'])->syncRoles($admin);
        Permission::create(['name' => 'admin.gestiones.store'])->syncRoles($admin);
        Permission::create(['name' => 'admin.gestiones.edit'])->syncRoles($admin);
        Permission::create(['name' => 'admin.gestiones.update'])->syncRoles($admin);
        Permission::create(['name' => 'admin.gestiones.destroy'])->syncRoles($admin);



        // --- PERMISOS PARA BITACORAS CRUD---
        Permission::create(['name' => 'admin.bitacora.index'])->syncRoles($admin);


        // --- PERMISOS PARA ROLES CRUD---
        Permission::create(['name' => 'admin.roles.index'])->syncRoles($admin);
        Permission::create(['name' => 'admin.roles.create'])->syncRoles($admin);
        Permission::create(['name' => 'admin.roles.store'])->syncRoles($admin);
        Permission::create(['name' => 'admin.roles.edit'])->syncRoles($admin);
        Permission::create(['name' => 'admin.roles.update'])->syncRoles($admin);
        Permission::create(['name' => 'admin.roles.destroy'])->syncRoles($admin);
        Permission::create(['name' => 'admin.roles.permisos'])->syncRoles($admin);
        Permission::create(['name' => 'admin.roles.update_permisos'])->syncRoles($admin);


        // --- PERMISOS PARA GRUPOS CRUD---
        Permission::create(['name' => 'admin.grupos.index'])->syncRoles($admin);
        Permission::create(['name' => 'admin.grupos.create'])->syncRoles($admin);
        Permission::create(['name' => 'admin.grupos.update'])->syncRoles($admin);
        Permission::create(['name' => 'admin.grupos.destroy'])->syncRoles($admin);


        // --- PERMISOS PARA CARRERAS CRUD---
        Permission::create(['name' => 'admin.carreras.index'])->syncRoles($admin);
        Permission::create(['name' => 'admin.carreras.create'])->syncRoles($admin);
        Permission::create(['name' => 'admin.carreras.update'])->syncRoles($admin);
        Permission::create(['name' => 'admin.carreras.destroy'])->syncRoles($admin);


        // --- PERMISOS PARA AULAS CRUD---
        Permission::create(['name' => 'admin.aulas.index'])->syncRoles($admin);
        Permission::create(['name' => 'admin.aulas.create'])->syncRoles($admin);
        Permission::create(['name' => 'admin.aulas.update'])->syncRoles($admin);
        Permission::create(['name' => 'admin.aulas.destroy'])->syncRoles($admin);


        // --- PERMISOS PARA HORARIOS CRUD---
        Permission::create(['name' => 'admin.horarios.index'])->syncRoles($admin);
        Permission::create(['name' => 'admin.horarios.create'])->syncRoles($admin);
        Permission::create(['name' => 'admin.horarios.update'])->syncRoles($admin);
        Permission::create(['name' => 'admin.horarios.destroy'])->syncRoles($admin);

        // --- PERMISOS PARA TURNOS CRUD---
        Permission::create(['name' => 'admin.turnos.index'])->syncRoles($admin);
        Permission::create(['name' => 'admin.turnos.create'])->syncRoles($admin);
        Permission::create(['name' => 'admin.turnos.store'])->syncRoles($admin);
        Permission::create(['name' => 'admin.turnos.edit'])->syncRoles($admin);
        Permission::create(['name' => 'admin.turnos.update'])->syncRoles($admin);
        Permission::create(['name' => 'admin.turnos.destroy'])->syncRoles($admin);

         // --- PERMISOS PARA DIAS CRUD---
        Permission::create(['name' => 'admin.dias.index'])->syncRoles($admin);
        Permission::create(['name' => 'admin.dias.create'])->syncRoles($admin);
        Permission::create(['name' => 'admin.dias.update'])->syncRoles($admin);
        Permission::create(['name' => 'admin.dias.destroy'])->syncRoles($admin);

        // --- PERMISOS PARA MATERIAS CRUD---
        Permission::create(['name' => 'admin.materias.index'])->syncRoles($admin);
        Permission::create(['name' => 'admin.materias.create'])->syncRoles($admin);
        Permission::create(['name' => 'admin.materias.update'])->syncRoles($admin);
        Permission::create(['name' => 'admin.materias.destroy'])->syncRoles($admin);

        // --- PERMISOS PARA CONFIG_PORCENTAJES CRUD---
        Permission::create(['name' => 'admin.config_porcentajes.index'])->syncRoles($admin);
        Permission::create(['name' => 'admin.config_porcentajes.create'])->syncRoles($admin);
        Permission::create(['name' => 'admin.config_porcentajes.update'])->syncRoles($admin);
        Permission::create(['name' => 'admin.config_porcentajes.destroy'])->syncRoles($admin);

        // --- PERMISOS PARA DOCENTES CRUD---
        Permission::create(['name' => 'admin.docentes.index'])->syncRoles($admin);
        Permission::create(['name' => 'admin.docentes.create'])->syncRoles($admin);
        Permission::create(['name' => 'admin.docentes.update'])->syncRoles($admin);
        Permission::create(['name' => 'admin.docentes.destroy'])->syncRoles($admin);

        // --- PERMISOS PARA POSTULANTES CRUD---
        Permission::create(['name' => 'admin.postulantes.index'])->syncRoles($admin);
        Permission::create(['name' => 'admin.postulantes.create'])->syncRoles($admin);
        Permission::create(['name' => 'admin.postulantes.update'])->syncRoles($admin);
        Permission::create(['name' => 'admin.postulantes.destroy'])->syncRoles($admin);

    }
}
