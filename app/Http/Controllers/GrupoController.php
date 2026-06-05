<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use Illuminate\Http\Request;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Validator;

class GrupoController extends Controller
{
    public function index()
    {
        $grupos = Grupo::all();
        return view('admin.grupos.index', compact('grupos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_create' => 'required|max:255|unique:grupos,nombre',
        ]);

        $grupo = new Grupo();
        $grupo->nombre = $request->nombre_create;
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
