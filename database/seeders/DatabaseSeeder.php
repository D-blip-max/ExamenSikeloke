<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(RoleSeeder::class);
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'dylancossioaguilera@gmail.com',
            'password' => Hash::make('12345678Aa'),
        ]);
        $admin->assignRole('Administrador');
        $admin->email_verified_at = now('America/La_Paz');
        $admin->save();

        $estudiante = User::create([
            'name' => 'Estudiante',
            'email' => 'estudiante@example.com',
            'password' => Hash::make('12345678Aa'),
        ]);
        $estudiante->assignRole('Estudiante');
        $estudiante->email_verified_at = now('America/La_Paz');
        $estudiante->save();

    }
}
