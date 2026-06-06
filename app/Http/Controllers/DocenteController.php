<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Turno;
use App\Models\User;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class DocenteController extends Controller
{
    public function index()
    {
        $docentes = Docente::with('turno')->get();
        $turnos = Turno::all();
        return view('admin.docentes.index', compact('docentes', 'turnos'));
    }

    public function buscar(Request $request)
    {
        $search = $request->input('search', '');
        $docentes = Docente::with('turno')
            ->where('nombre', 'like', '%' . $search . '%')
            ->orWhere('ci', 'like', '%' . $search . '%')
            ->orWhere('correo', 'like', '%' . $search . '%')
            ->orWhere('especialidad', 'like', '%' . $search . '%')
            ->get();
        
        return response()->json($docentes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ci_create' => 'required|unique:docentes,ci|numeric',
            'nombre_create' => 'required|max:255',
            'correo_create' => 'required|email|unique:docentes,correo|unique:users,email',
            'especialidad_create' => 'required|max:255',
            'turno_id_create' => 'required|exists:turnos,id',
            'maestria_create' => 'required|in:0,1',
            'diplomado_edu_create' => 'required|in:0,1',
            'estado_create' => 'required|in:ACTIVO,NO ACTIVO',
        ]);

        $docente = Docente::create([
            'ci' => $request->ci_create,
            'nombre' => $request->nombre_create,
            'correo' => $request->correo_create,
            'especialidad' => $request->especialidad_create,
            'turno_id' => $request->turno_id_create,
            'maestria' => (bool) $request->maestria_create,
            'diplomado_edu' => (bool) $request->diplomado_edu_create,
            'estado' => $request->estado_create,
        ]);

        // Generar contraseña: CI + Primera letra apellido (mayúscula) + Primera letra nombre (minúscula)
        $nombreParts = explode(' ', trim($request->nombre_create));
        $primerNombre = $nombreParts[0] ?? '';
        $primerApellido = $nombreParts[count($nombreParts) - 1] ?? '';
        
        $letraApellido = strtoupper(substr($primerApellido, 0, 1));
        $letraNombre = strtolower(substr($primerNombre, 0, 1));
        $password = $request->ci_create . $letraApellido . $letraNombre;

        // Crear usuario automáticamente
        User::create([
            'name' => $request->nombre_create,
            'email' => $request->correo_create,
            'password' => Hash::make($password),
            'email_verified_at' => now('America/La_Paz'),
        ])->assignRole('Docente');

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se creó un docente: ' . $docente->nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.docentes.index')
            ->with('mensaje', 'El docente se ha creado correctamente.')
            ->with('icono', 'success');
    }

    public function update(Request $request, $id)
    {
        $docente = Docente::find($id);
        
        $validate = Validator::make($request->all(), [
            'ci' => 'required|unique:docentes,ci,' . $id . '|numeric',
            'nombre' => 'required|max:255',
            'correo' => 'required|email|unique:docentes,correo,' . $id,
            'especialidad' => 'required|max:255',
            'turno_id' => 'required|exists:turnos,id',
            'maestria' => 'required|in:0,1',
            'diplomado_edu' => 'required|in:0,1',
            'estado' => 'required|in:ACTIVO,NO ACTIVO',
        ]);

        // Validar que el nuevo correo no exista en users (excepto el del usuario actual)
        $validate->after(function ($validator) use ($request, $docente) {
            if ($request->correo !== $docente->correo) {
                $usuarioConEseCorreo = User::where('email', $request->correo)->first();
                if ($usuarioConEseCorreo) {
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

        // Obtener usuario antes de modificar docente
        $usuario = User::where('email', $docente->correo)->first();
        
        $docente->ci = $request->ci;
        $docente->nombre = $request->nombre;
        $docente->correo = $request->correo;
        $docente->especialidad = $request->especialidad;
        $docente->turno_id = $request->turno_id;
        $docente->maestria = (bool) $request->maestria;
        $docente->diplomado_edu = (bool) $request->diplomado_edu;
        $docente->estado = $request->estado;
        $docente->save();

        // Sincronizar usuario asociado si existe
        if ($usuario) {
            // Generar nueva contraseña basada en CI + Primera letra apellido (mayús) + Primera letra nombre (minús)
            $nombreParts = explode(' ', trim($request->nombre));
            $primerNombre = $nombreParts[0] ?? '';
            $primerApellido = $nombreParts[count($nombreParts) - 1] ?? '';
            $letraApellido = strtoupper(substr($primerApellido, 0, 1));
            $letraNombre = strtolower(substr($primerNombre, 0, 1));
            $newPassword = $request->ci . $letraApellido . $letraNombre;

            $usuario->name = $request->nombre;
            $usuario->email = $request->correo;
            $usuario->password = Hash::make($newPassword);
            $usuario->save();
        }

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se editó un docente: ' . $docente->nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.docentes.index')
            ->with('mensaje', 'El docente se ha actualizado correctamente')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $docente = Docente::find($id);
        $nombre = $docente->nombre;
        $correoDocente = $docente->correo;
        
        // Eliminar usuario asociado
        User::where('email', $correoDocente)->delete();
        
        $docente->delete();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó un docente: ' . $nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.docentes.index')
            ->with('mensaje', 'El docente se ha eliminado correctamente')
            ->with('icono', 'success');
    }
}
