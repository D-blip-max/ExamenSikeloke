<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Gestion;
use App\Models\Pago;
use App\Models\Postulante;
use App\Models\User;
use App\Models\Bitacora;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PostulanteController extends Controller
{
    public function index()
    {
        $postulantes = Postulante::with(['gestion', 'carrera1', 'carrera2'])->get();
        $carreras = Carrera::all();
        $gestiones = Gestion::all();

        return view('admin.postulantes.index', compact('postulantes', 'carreras', 'gestiones'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ci_create' => 'required|unique:postulantes,ci|numeric',
            'nombres_create' => 'required|max:255',
            'apellidos_create' => 'required|max:255',
            'sexo_create' => 'required|in:M,F',
            'correo_create' => 'required|email|unique:postulantes,correo|unique:users,email',
            'telefono_create' => 'required|max:20',
            'ciudad_create' => 'required|max:255',
            'colegio_create' => 'required|max:255',
            'fecha_nac_create' => 'required|date',
            'titulo_bachiller_create' => 'required|in:1',
            'carrera1_id_create' => 'required|exists:carreras,id',
            'carrera2_id_create' => 'required|exists:carreras,id|different:carrera1_id_create',
            'gestion_id_create' => 'required|exists:gestions,id',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->filled('fecha_nac_create')) {
                $edad = Carbon::parse($request->fecha_nac_create)->age;
                if ($edad < 18) {
                    $validator->errors()->add('fecha_nac_create', 'El postulante debe ser mayor de edad.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal_id', null);
        }

        $postulante = Postulante::create([
            'ci' => $request->ci_create,
            'nombres' => $request->nombres_create,
            'apellidos' => $request->apellidos_create,
            'sexo' => $request->sexo_create,
            'correo' => $request->correo_create,
            'telefono' => $request->telefono_create,
            'ciudad' => $request->ciudad_create,
            'colegio' => $request->colegio_create,
            'fecha_nac' => $request->fecha_nac_create,
            'titulo_bachiller' => (bool) $request->titulo_bachiller_create,
            'carrera1_id' => $request->carrera1_id_create,
            'carrera2_id' => $request->carrera2_id_create,
            'gestion_id' => $request->gestion_id_create,
            'pago_confirmado' => 'FALSO',
            'estado_inscripcion' => 'PENDIENTE_PAGO',
        ]);

        Pago::create([
            'postulante_id' => $postulante->id,
            'comprobante' => 'AUTO-' . $postulante->id,
            'monto' => 200,
            'fecha' => now('America/La_Paz')->format('Y-m-d'),
            'estado' => 'PENDIENTE',
        ]);

        $nombreCompleto = trim($request->nombres_create . ' ' . $request->apellidos_create);
        
        // Generar contraseña: CI + Primera letra primer apellido (mayúscula) + Primera letra primer nombre (minúscula)
        $letraApellido = strtoupper(substr($request->apellidos_create, 0, 1));
        $letraNombre = strtolower(substr($request->nombres_create, 0, 1));
        $password = $request->ci_create . $letraApellido . $letraNombre;

        User::create([
            'name' => $nombreCompleto,
            'email' => $request->correo_create,
            'password' => Hash::make($password),
            'email_verified_at' => now('America/La_Paz'),
        ])->assignRole('Estudiante');

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se creó un postulante: ' . $nombreCompleto,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.postulantes.index')
            ->with('mensaje', 'El postulante se ha creado correctamente.')
            ->with('icono', 'success');
    }

    public function update(Request $request, $id)
    {
        $postulante = Postulante::findOrFail($id);
        
        $validate = Validator::make($request->all(), [
            'ci' => 'required|unique:postulantes,ci,' . $id . '|numeric',
            'nombres' => 'required|max:255',
            'apellidos' => 'required|max:255',
            'sexo' => 'required|in:M,F',
            'correo' => 'required|email|unique:postulantes,correo,' . $id,
            'telefono' => 'required|max:20',
            'ciudad' => 'required|max:255',
            'colegio' => 'required|max:255',
            'fecha_nac' => 'required|date',
            'titulo_bachiller' => 'required|in:1',
            'carrera1_id' => 'required|exists:carreras,id',
            'carrera2_id' => 'required|exists:carreras,id|different:carrera1_id',
            'pago_confirmado' => 'required|in:VERDADERO,FALSO',
            'estado_inscripcion' => 'required|in:INSCRITO,PENDIENTE_PAGO,BLOQUEADO',
            'gestion_id' => 'required|exists:gestions,id',
        ]);

        // Validar que el nuevo correo no exista en users (excepto el del usuario actual)
        $validate->after(function ($validator) use ($request, $postulante) {
            if ($request->filled('fecha_nac')) {
                $edad = Carbon::parse($request->fecha_nac)->age;
                if ($edad < 18) {
                    $validator->errors()->add('fecha_nac', 'El postulante debe ser mayor de edad.');
                }
            }
            
            // Validar correo único en users (si cambió el correo)
            if ($request->correo !== $postulante->correo) {
                $usuarioConEseCorrerno = User::where('email', $request->correo)->first();
                if ($usuarioConEseCorrerno) {
                    $validator->errors()->add('correo', 'Este correo ya está registrado en el sistema.');
                }
            }
        });

        if ($validate->fails()) {
            return redirect()
                ->back()
                ->withErrors($validate)
                ->withInput()
                ->with('modal_id', $id);
        }

        $nombreCompleto = trim($request->nombres . ' ' . $request->apellidos);
        
        // Obtener usuario antes de modificar postulante
        $usuario = User::where('email', $postulante->correo)->first();
        
        // Actualizar postulante
        $postulante->ci = $request->ci;
        $postulante->nombres = $request->nombres;
        $postulante->apellidos = $request->apellidos;
        $postulante->sexo = $request->sexo;
        $postulante->correo = $request->correo;
        $postulante->telefono = $request->telefono;
        $postulante->ciudad = $request->ciudad;
        $postulante->colegio = $request->colegio;
        $postulante->fecha_nac = $request->fecha_nac;
        $postulante->titulo_bachiller = (bool) $request->titulo_bachiller;
        $postulante->carrera1_id = $request->carrera1_id;
        $postulante->carrera2_id = $request->carrera2_id;
        $postulante->pago_confirmado = $request->pago_confirmado;
        $postulante->estado_inscripcion = $request->estado_inscripcion;
        $postulante->gestion_id = $request->gestion_id;
        $postulante->save();

        // Sincronizar usuario asociado si existe
        if ($usuario) {
            // Generar nueva contraseña basada en CI + Letra apellido (mayús) + Letra nombre (minús)
            $letraApellido = strtoupper(substr($request->apellidos, 0, 1));
            $letraNombre = strtolower(substr($request->nombres, 0, 1));
            $newPassword = $request->ci . $letraApellido . $letraNombre;
            
            $usuario->name = $nombreCompleto;
            $usuario->email = $request->correo;
            $usuario->password = Hash::make($newPassword);
            $usuario->save();
        }

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se editó un postulante: ' . $nombreCompleto,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.postulantes.index')
            ->with('mensaje', 'El postulante se ha actualizado correctamente')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $postulante = Postulante::findOrFail($id);
        $nombre = $postulante->nombres . ' ' . $postulante->apellidos;
        $correoPostulante = $postulante->correo;
        
        // Eliminar usuario asociado
        User::where('email', $correoPostulante)->delete();
        
        $postulante->delete();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó un postulante: ' . $nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.postulantes.index')
            ->with('mensaje', 'El postulante se ha eliminado correctamente')
            ->with('icono', 'success');
    }
}
