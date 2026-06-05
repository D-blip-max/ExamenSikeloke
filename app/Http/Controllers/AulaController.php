<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use Illuminate\Http\Request;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Validator;

class AulaController extends Controller
{
    public function index()
    {
        $aulas = Aula::all();
        return view('admin.aulas.index', compact('aulas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_create' => 'required|max:255|unique:aulas,nombre',
            'capacidad_create' => 'required|integer|min:1',
            'tipo_create' => 'required|in:presencial,virtual',
        ]);

        $aula = new Aula();
        $aula->nombre = $request->nombre_create;
        $aula->capacidad = $request->capacidad_create;
        $aula->tipo = $request->tipo_create;
        $aula->save();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se creó un aula: ' . $aula->nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.aulas.index')
            ->with('mensaje', 'El aula se ha creado correctamente.')
            ->with('icono', 'success');
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'nombre' => 'required|max:255|unique:aulas,nombre,' . $id,
            'capacidad' => 'required|integer|min:1',
            'tipo' => 'required|in:presencial,virtual',
        ]);

        if ($validate->fails()) {
            return redirect()
                ->back()
                ->withErrors($validate)
                ->withInput()
                ->with('modal_id', $id);
        }

        $aula = Aula::find($id);
        $aula->nombre = $request->nombre;
        $aula->capacidad = $request->capacidad;
        $aula->tipo = $request->tipo;
        $aula->save();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se editó un aula: ' . $aula->nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.aulas.index')
            ->with('mensaje', 'El aula se ha actualizado correctamente')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $aula = Aula::find($id);
        $nombre = $aula->nombre;
        $aula->delete();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó un aula: ' . $nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.aulas.index')
            ->with('mensaje', 'El aula se ha eliminado correctamente')
            ->with('icono', 'success');
    }
}
