<?php

namespace App\Http\Controllers;

use App\Models\Dia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Bitacora;

class DiaController extends Controller
{
    public function index()
    {
        $dias = Dia::all();
        return view('admin.dias.index', compact('dias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_create' => 'required|max:255|unique:dias,nombre',
        ]);

        $dia = new Dia();
        $dia->nombre = $request->nombre_create;
        $dia->save();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se creó un día: ' . $dia->nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.dias.index')
            ->with('mensaje', 'El día se ha creado correctamente.')
            ->with('icono', 'success');
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'nombre' => 'required|max:255|unique:dias,nombre,' . $id,
        ]);

        if ($validate->fails()) {
            return redirect()
                ->back()
                ->withErrors($validate)
                ->withInput()
                ->with('modal_id', $id);
        }

        $dia = Dia::find($id);
        $dia->nombre = $request->nombre;
        $dia->save();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se editó un día: ' . $dia->nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.dias.index')
            ->with('mensaje', 'El día se ha actualizado correctamente')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $dia = Dia::find($id);
        $nombre = $dia->nombre;
        $dia->delete();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó un día: ' . $nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.dias.index')
            ->with('mensaje', 'El día se ha eliminado correctamente')
            ->with('icono', 'success');
    }
}
