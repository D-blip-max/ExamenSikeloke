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

        


    }
}
