<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarreraController extends Controller
{
    public function index()
    {
        $carreras = Carrera::all();
        return view('admin.carreras.index', compact('carreras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_create' => 'required|max:255|unique:carreras,nombre',
            'nota_minima_create' => 'required|numeric|min:0',
            'cupo_maximo_create' => 'required|integer|min:1',
            'cupo_disponible_create' => 'required|integer|min:0|max:' . $request->input('cupo_maximo_create'),
        ]);

        $carrera = new Carrera();
        $carrera->nombre = $request->nombre_create;
        $carrera->nota_minima = $request->nota_minima_create;
        $carrera->cupo_maximo = $request->cupo_maximo_create;
        $carrera->cupo_disponible = $request->cupo_disponible_create;
        $carrera->save();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se creó una carrera: ' . $carrera->nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.carreras.index')
            ->with('mensaje', 'La carrera se ha creado correctamente.')
            ->with('icono', 'success');
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'nombre' => 'required|max:255|unique:carreras,nombre,' . $id,
            'nota_minima' => 'required|numeric|min:0',
            'cupo_maximo' => 'required|integer|min:1',
            'cupo_disponible' => 'required|integer|min:0|max:' . $request->input('cupo_maximo'),
        ]);

        if ($validate->fails()) {
            return redirect()
                ->back()
                ->withErrors($validate)
                ->withInput()
                ->with('modal_id', $id);
        }

        $carrera = Carrera::find($id);
        $carrera->nombre = $request->nombre;
        $carrera->nota_minima = $request->nota_minima;
        $carrera->cupo_maximo = $request->cupo_maximo;
        $carrera->cupo_disponible = $request->cupo_disponible;
        $carrera->save();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se editó una carrera: ' . $carrera->nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.carreras.index')
            ->with('mensaje', 'La carrera se ha actualizado correctamente')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $carrera = Carrera::find($id);
        $nombre = $carrera->nombre;
        $carrera->delete();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó una carrera: ' . $nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.carreras.index')
            ->with('mensaje', 'La carrera se ha eliminado correctamente')
            ->with('icono', 'success');
    }
}
