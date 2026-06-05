<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MateriaController extends Controller
{
    public function index()
    {
        $materias = Materia::all();
        return view('admin.materias.index', compact('materias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_create' => 'required|max:255|unique:materias,nombre',
        ]);

        $materia = Materia::create([
            'nombre' => $request->nombre_create,
        ]);

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se creó una materia: ' . $materia->nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.materias.index')
            ->with('mensaje', 'La materia se ha creado correctamente.')
            ->with('icono', 'success');
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'nombre' => 'required|max:255|unique:materias,nombre,' . $id,
        ]);

        if ($validate->fails()) {
            return redirect()
                ->back()
                ->withErrors($validate)
                ->withInput()
                ->with('modal_id', $id);
        }

        $materia = Materia::find($id);
        $materia->nombre = $request->nombre;
        $materia->save();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se editó una materia: ' . $materia->nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.materias.index')
            ->with('mensaje', 'La materia se ha actualizado correctamente')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $materia = Materia::find($id);
        $nombre = $materia->nombre;
        $materia->delete();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó una materia: ' . $nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.materias.index')
            ->with('mensaje', 'La materia se ha eliminado correctamente')
            ->with('icono', 'success');
    }
}
