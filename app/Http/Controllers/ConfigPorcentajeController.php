<?php

namespace App\Http\Controllers;

use App\Models\ConfigPorcentaje;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConfigPorcentajeController extends Controller
{
    public function index()
    {
        $configPorcentajes = ConfigPorcentaje::all();
        return view('admin.config_porcentajes.index', compact('configPorcentajes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_examen_create' => 'required|max:255|unique:config_porcentaje,numero_examen',
            'ponderacion_create' => 'required|numeric|min:0|max:100',
        ]);

        $configPorcentaje = ConfigPorcentaje::create([
            'numero_examen' => $request->numero_examen_create,
            'ponderacion' => $request->ponderacion_create,
        ]);

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se creó un porcentaje de evaluación: ' . $configPorcentaje->numero_examen,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.config_porcentajes.index')
            ->with('mensaje', 'El porcentaje de evaluación se ha creado correctamente.')
            ->with('icono', 'success');
    }

    public function update(Request $request, $id)
    {
        $validate = Validator::make($request->all(), [
            'numero_examen' => 'required|max:255|unique:config_porcentaje,numero_examen,' . $id,
            'ponderacion' => 'required|numeric|min:0|max:100',
        ]);

        if ($validate->fails()) {
            return redirect()
                ->back()
                ->withErrors($validate)
                ->withInput()
                ->with('modal_id', $id);
        }

        $configPorcentaje = ConfigPorcentaje::find($id);
        $configPorcentaje->numero_examen = $request->numero_examen;
        $configPorcentaje->ponderacion = $request->ponderacion;
        $configPorcentaje->save();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se editó un porcentaje de evaluación: ' . $configPorcentaje->numero_examen,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.config_porcentajes.index')
            ->with('mensaje', 'El porcentaje de evaluación se ha actualizado correctamente')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $configPorcentaje = ConfigPorcentaje::find($id);
        $numeroExamen = $configPorcentaje->numero_examen;
        $configPorcentaje->delete();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó un porcentaje de evaluación: ' . $numeroExamen,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.config_porcentajes.index')
            ->with('mensaje', 'El porcentaje de evaluación se ha eliminado correctamente')
            ->with('icono', 'success');
    }
}
