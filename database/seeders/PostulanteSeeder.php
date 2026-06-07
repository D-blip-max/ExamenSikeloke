<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Postulante;
use App\Models\User;
use App\Models\Gestion;
use App\Models\Carrera;

class PostulanteSeeder extends Seeder
{
    public function run()
    {
        $nombres = [
            'juan','pedro','carlos','luis','marco','david','jorge','ricardo','samuel','felipe',
            'esteban','hernan','ramiro','roberto','andres','alvaro','milton','edgar','oscar','alfredo',
            'manuel','martin','ismael','julio','hernando','guillermo','fabian','antonio','alex',
            'sergio','ezequiel','pablo','nicolas','emilio','ivan','raul','santiago','sebastian','cesar',
            'eduardo','victor','enrique','omar','francisco','javier','benjamin','miguel','german','adrian',
            'cristian','gonzalo','daniel','leo','gustavo','marcos','alejandro','gaston','horacio','fabio',
            'ismael2','hernan2','roberto2','jorge2','cesar2','ismael3','ismael4','ismael5','huanca','choque'
        ];

        $apellidos = [
            'gonzalez','perez','rodriguez','lopez','garcia','martinez','sanchez','ramirez','fernandez','gomez',
            'diaz','torres','ruiz','medina','ortiz','mendoza','juarez','silva','vargas','carrasco',
            'rojas','castro','soto','caceres','quiroz','avila','herrera','reyes','pineda','marquez',
            'cortes','guerra','blanco','salazar','montoya','arias','paz','villarroel','alvarez','camacho',
            'carrion','salgado','palacios','chavez','molina','santillan','palma','espinoza','cornejo','condori',
            'mallku','quintana','choque2','apu','tito','huanca2','quilla','maza','torrico','soria'
        ];

        $carreras = Carrera::pluck('id')->toArray();
        $gestiones = Gestion::pluck('id')->toArray();

        // Intentar obtener específicamente la gestión 'Gestión 1-2025'
        $gestionDeseada = Gestion::where('nombre', 'Gestión 1-2025')->first();

        if (empty($carreras) || empty($gestiones)) {
            return;
        }

        for ($i = 1; $i <= 70; $i++) {
            $n = $nombres[array_rand($nombres)];
            $a = $apellidos[array_rand($apellidos)];

            $ci = (string)(7000000 + $i);
            $correo = 'postulante' . $i . '@gmail.com';

            $c1 = $carreras[array_rand($carreras)];
            do {
                $c2 = $carreras[array_rand($carreras)];
            } while ($c2 == $c1 && count($carreras) > 1);

            $postulante = Postulante::create([
                'ci' => $ci,
                'nombres' => ucfirst($n),
                'apellidos' => ucfirst($a),
                'sexo' => (rand(0,1) ? 'M' : 'F'),
                'correo' => $correo,
                'telefono' => '7' . str_pad((string)rand(1000000,9999999),7,'0',STR_PAD_LEFT),
                'ciudad' => 'La Paz',
                'colegio' => 'Colegio Nacional',
                'fecha_nac' => now()->subYears(rand(17,25))->format('Y-m-d'),
                'carrera1_id' => $c1,
                'carrera2_id' => $c2,
                'pago_confirmado' => 'FALSO',
                'estado_inscripcion' => 'PENDIENTE_PAGO',
                'gestion_id' => $gestionDeseada ? $gestionDeseada->id : $gestiones[array_rand($gestiones)],
            ]);

            $user = User::create([
                'name' => $postulante->nombres . ' ' . $postulante->apellidos,
                'email' => $postulante->correo,
                'password' => Hash::make($postulante->ci),
            ]);
            $user->assignRole('Estudiante');
            $user->email_verified_at = now('America/La_Paz');
            $user->save();
        }
    }
}
