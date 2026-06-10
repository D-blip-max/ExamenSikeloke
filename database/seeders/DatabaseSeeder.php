<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Turno;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Carrera;
use App\Models\Aula;
use App\Models\ConfigPorcentaje;
use App\Models\Gestion;
use App\Models\Dia;
use App\Models\Materia;


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

        //Turno seeder
        Turno::create(['nombre' => 'Mañana']);
        Turno::create(['nombre' => 'Tarde']);
        Turno::create(['nombre' => 'Noche']);

        // Grupos con capacidad para 280 postulantes distribuidos en 4 grupos
        Grupo::create(['nombre' => 'Grupo 1', 'cupo_maximo' => 70, 'inscritos' => 0, 'turno_id' => 1]);
        Grupo::create(['nombre' => 'Grupo 2', 'cupo_maximo' => 70, 'inscritos' => 0, 'turno_id' => 2]);
        Grupo::create(['nombre' => 'Grupo 3', 'cupo_maximo' => 70, 'inscritos' => 0, 'turno_id' => 3]);
        Grupo::create(['nombre' => 'Grupo 4', 'cupo_maximo' => 70, 'inscritos' => 0, 'turno_id' => 1]);
        //horario Seeder

        Horario::create(['horaInicio' => '07:00', 'horaFin' => '07:45']);
        Horario::create(['horaInicio' => '07:45', 'horaFin' => '08:30']);
        Horario::create(['horaInicio' => '08:30', 'horaFin' => '09:15']);
        Horario::create(['horaInicio' => '09:15', 'horaFin' => '10:00']);
        Horario::create(['horaInicio' => '10:00', 'horaFin' => '10:45']);
        Horario::create(['horaInicio' => '10:45', 'horaFin' => '11:30']);
        Horario::create(['horaInicio' => '11:30', 'horaFin' => '12:15']);
        Horario::create(['horaInicio' => '12:15', 'horaFin' => '13:00']);
        Horario::create(['horaInicio' => '13:00', 'horaFin' => '13:45']);
        Horario::create(['horaInicio' => '13:45', 'horaFin' => '14:30']);
        Horario::create(['horaInicio' => '14:30', 'horaFin' => '15:15']);
        Horario::create(['horaInicio' => '15:15', 'horaFin' => '16:00']);
        Horario::create(['horaInicio' => '16:00', 'horaFin' => '16:45']);
        Horario::create(['horaInicio' => '16:45', 'horaFin' => '17:30']);
        Horario::create(['horaInicio' => '17:30', 'horaFin' => '18:15']);
        Horario::create(['horaInicio' => '18:15', 'horaFin' => '19:00']);
        Horario::create(['horaInicio' => '19:00', 'horaFin' => '19:45']);
        Horario::create(['horaInicio' => '19:45', 'horaFin' => '20:30']);
        Horario::create(['horaInicio' => '20:30', 'horaFin' => '21:15']);
        Horario::create(['horaInicio' => '21:15', 'horaFin' => '22:00']);


        // carreras
        Carrera::create(['nombre' => 'Ingeniería de Sistemas', 'nota_minima' => 60, 'cupo_maximo' => 100, 'cupo_disponible' => 100]);
        Carrera::create(['nombre' => 'Ingeniería Informática', 'nota_minima' => 55, 'cupo_maximo' => 80, 'cupo_disponible' => 80]);
        Carrera::create(['nombre' => 'Redes y Telecomunicaciones', 'nota_minima' => 50, 'cupo_maximo' => 60, 'cupo_disponible' => 60]);
        Carrera::create(['nombre' => 'Robótica', 'nota_minima' => 50, 'cupo_maximo' => 60, 'cupo_disponible' => 60]);


        //Aulas
        Aula::create(['nombre' => 'Aula 101', 'capacidad' => 30, 'tipo' => 'Presencial']);
        Aula::create(['nombre' => 'Aula 102', 'capacidad' => 30, 'tipo' => 'Presencial']);
        Aula::create(['nombre' => 'Aula 103', 'capacidad' => 30, 'tipo' => 'Presencial']);
        Aula::create(['nombre' => 'Aula 104', 'capacidad' => 30, 'tipo' => 'Presencial']);
        Aula::create(['nombre' => 'Aula 105', 'capacidad' => 30, 'tipo' => 'Presencial']);
        Aula::create(['nombre' => 'Aula 106', 'capacidad' => 30, 'tipo' => 'Presencial']);
        Aula::create(['nombre' => 'Aula Virtual 1', 'capacidad' => 100, 'tipo' => 'Virtual']);
        Aula::create(['nombre' => 'Aula Virtual 2', 'capacidad' => 100, 'tipo' => 'Virtual']);

        //configuración de porcentajes de evaluación
        ConfigPorcentaje::create(['numero_examen' => 'Primer Parcial', 'ponderacion' => 33.33]);
        ConfigPorcentaje::create(['numero_examen' => 'Segundo Parcial', 'ponderacion' => 33.33]);
        ConfigPorcentaje::create(['numero_examen' => 'Tercer Parcial', 'ponderacion' => 33.33]);

        //Gestionar Gestiones
        Gestion::create(['nombre' => 'Gestión 1-2024']);
        Gestion::create(['nombre' => 'Gestión 2-2024']);
        Gestion::create(['nombre' => 'Gestión 1-2025']);
        Gestion::create(['nombre' => 'Gestión 2-2025']);

        //Días de la semana
        Dia::create(['nombre' => 'Lunes']);
        Dia::create(['nombre' => 'Martes']);
        Dia::create(['nombre' => 'Miércoles']);
        Dia::create(['nombre' => 'Jueves']);
        Dia::create(['nombre' => 'Viernes']);
        Dia::create(['nombre' => 'Sábado']);
        Dia::create(['nombre' => 'Domingo']);

        //Gestionar Materias
        Materia::create(['nombre' => 'Matematicas']);
        Materia::create(['nombre' => 'Computacion']);
        Materia::create(['nombre' => 'Fisica']);
        Materia::create(['nombre' => 'Ingles']);

        // Seed postulantes y usuarios asociados
        $this->call(\Database\Seeders\PostulanteSeeder::class);

        // Seed pagos confirmados para todos los postulantes
        $this->call(\Database\Seeders\PagoSeeder::class);

        // Seed notas y el resultado de admitidos / reprobados para los postulantes
        $this->call(\Database\Seeders\NotaSeeder::class);

        // Seed docentes, asignaciones y grupos de postulantes al Grupo 1
        $this->call(\Database\Seeders\DocenteSeeder::class);
        $this->call(\Database\Seeders\AsignacionDocenteGrupoSeeder::class);
        $this->call(\Database\Seeders\PostulanteGrupoSeeder::class);
    }
}
