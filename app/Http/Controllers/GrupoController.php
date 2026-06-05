<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Turno;
use Illuminate\Http\Request;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Validator;

class GrupoController extends Controller
{
    public function index()
    {
        $grupos = Grupo::all();
        $turnos = Turno::all();
        return view('admin.grupos.index', compact('grupos', 'turnos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_create' => 'required|max:255|unique:grupos,nombre',
            'cupo_maximo_create' => 'required|integer|min:1',
            'inscritos_create' => 'required|integer|min:0|max:' . $request->input('cupo_maximo_create'),
            'turno_id_create' => 'required|exists:turnos,id',
        ]);

        $grupo = new Grupo();
        $grupo->nombre = $request->nombre_create;
        $grupo->cupo_maximo = $request->cupo_maximo_create;
        $grupo->inscritos = $request->inscritos_create;
        $grupo->turno_id = $request->turno_id_create;
        $grupo->save();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se creó un grupo: ' . $grupo->nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.grupos.index')
            ->with('mensaje', 'El grupo se ha creado correctamente.')
            ->with('icono', 'success');
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'nombre' => 'required|max:255|unique:grupos,nombre,' . $id,
            'cupo_maximo' => 'required|integer|min:1',
            'inscritos' => 'required|integer|min:0|max:' . $request->input('cupo_maximo'),
            'turno_id' => 'required|exists:turnos,id',
        ]);

        if ($validate->fails()) {
            return redirect()
                ->back()
                ->withErrors($validate)
                ->withInput()
                ->with('modal_id', $id);
        }

        $grupo = Grupo::find($id);
        $grupo->nombre = $request->nombre;
        $grupo->cupo_maximo = $request->cupo_maximo;
        $grupo->inscritos = $request->inscritos;
        $grupo->turno_id = $request->turno_id;
        $grupo->save();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se editó un grupo: ' . $grupo->nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.grupos.index')
            ->with('mensaje', 'El grupo se ha actualizado correctamente')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $grupo = Grupo::find($id);
        $nombre = $grupo->nombre;
        $grupo->delete();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó un grupo: ' . $nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.grupos.index')
            ->with('mensaje', 'El grupo se ha eliminado correctamente')
            ->with('icono', 'success');
    }
}
