<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\ConfigPorcentaje;
use App\Models\Materia;
use App\Models\Nota;
use App\Models\Postulante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotaController extends Controller
{
    public function index()
    {
        $notas = Nota::with(['postulante', 'materia', 'configExamen'])->get();
        $postulantes = Postulante::all();
        $materias = Materia::all();
        $configPorcentajes = ConfigPorcentaje::all();

        $promediosPorMateria = Nota::selectRaw('notas.postulante_id, notas.materia_id, SUM(notas.nota * config_porcentaje.ponderacion / 100) as promedio')
            ->join('config_porcentaje', 'notas.config_examen_id', '=', 'config_porcentaje.id')
            ->groupBy('notas.postulante_id', 'notas.materia_id')
            ->get();

        $resumenPostulantes = [];

        foreach ($promediosPorMateria as $fila) {
            $resumenPostulantes[$fila->postulante_id]['materias'][$fila->materia_id] = round($fila->promedio, 2);
        }

        foreach ($resumenPostulantes as $postulanteId => $data) {
            $materiasCount = count($data['materias']);
            $promedioFinal = $materiasCount > 0 ? round(array_sum($data['materias']) / $materiasCount, 2) : 0;
            $resumenPostulantes[$postulanteId]['postulante'] = Postulante::find($postulanteId);
            $resumenPostulantes[$postulanteId]['promedio_final'] = $promedioFinal;
            $resumenPostulantes[$postulanteId]['estado'] = $promedioFinal >= 60 ? 'APROBADO' : 'REPROBADO';
        }

        return view('admin.notas.index', compact('notas', 'postulantes', 'materias', 'configPorcentajes', 'resumenPostulantes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'postulante_id_create' => 'required|exists:postulantes,id',
            'materia_id_create' => 'required|exists:materias,id',
            'config_examen_id_create' => 'required|exists:config_porcentaje,id',
            'nota_create' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal_id', null);
        }

        if ($request->nota_create < 0 || $request->nota_create > 100) {
            return redirect()
                ->back()
                ->withInput()
                ->with('modal_id', null)
                ->with('mensaje', 'La nota debe estar entre 0 y 100')
                ->with('icono', 'error');
        }

        $postulante = Postulante::findOrFail($request->postulante_id_create);

        if ($postulante->estado_inscripcion !== 'INSCRITO') {
            return redirect()
                ->back()
                ->withInput()
                ->with('modal_id', null)
                ->with('mensaje', 'El postulante no está inscrito, no se pueden registrar notas')
                ->with('icono', 'error');
        }

        $countNotasMateria = Nota::where('postulante_id', $request->postulante_id_create)
            ->where('materia_id', $request->materia_id_create)
            ->count();

        if ($countNotasMateria >= 3) {
            return redirect()
                ->back()
                ->withInput()
                ->with('modal_id', null)
                ->with('mensaje', 'El postulante ya tiene 3 exámenes registrados en esta materia')
                ->with('icono', 'error');
        }

        $configDuplicada = Nota::where('postulante_id', $request->postulante_id_create)
            ->where('materia_id', $request->materia_id_create)
            ->where('config_examen_id', $request->config_examen_id_create)
            ->exists();

        if ($configDuplicada) {
            return redirect()
                ->back()
                ->withInput()
                ->with('modal_id', null)
                ->with('mensaje', 'Ya existe una nota para ese examen en esa materia')
                ->with('icono', 'error');
        }

        $nota = Nota::create([
            'postulante_id' => $request->postulante_id_create,
            'materia_id' => $request->materia_id_create,
            'config_examen_id' => $request->config_examen_id_create,
            'nota' => $request->nota_create,
        ]);

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se creó una nota: Postulante ' . $nota->postulante_id . ', Materia ' . $nota->materia_id . ', Examen ' . $nota->config_examen_id,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.notas.index')
            ->with('mensaje', 'La nota se ha registrado correctamente.')
            ->with('icono', 'success');
    }

    public function update(Request $request, $id)
    {
        $nota = Nota::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'postulante_id' => 'required|exists:postulantes,id',
            'materia_id' => 'required|exists:materias,id',
            'config_examen_id' => 'required|exists:config_porcentaje,id',
            'nota' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal_id', $id);
        }

        if ($request->nota < 0 || $request->nota > 100) {
            return redirect()
                ->back()
                ->withInput()
                ->with('modal_id', $id)
                ->with('mensaje', 'La nota debe estar entre 0 y 100')
                ->with('icono', 'error');
        }

        $postulante = Postulante::findOrFail($request->postulante_id);

        if ($postulante->estado_inscripcion !== 'INSCRITO') {
            return redirect()
                ->back()
                ->withInput()
                ->with('modal_id', $id)
                ->with('mensaje', 'El postulante no está inscrito, no se pueden registrar notas')
                ->with('icono', 'error');
        }

        $countNotasMateria = Nota::where('postulante_id', $request->postulante_id)
            ->where('materia_id', $request->materia_id)
            ->where('id', '!=', $id)
            ->count();

        if ($countNotasMateria >= 3) {
            return redirect()
                ->back()
                ->withInput()
                ->with('modal_id', $id)
                ->with('mensaje', 'El postulante ya tiene 3 exámenes registrados en esta materia')
                ->with('icono', 'error');
        }

        $configDuplicada = Nota::where('postulante_id', $request->postulante_id)
            ->where('materia_id', $request->materia_id)
            ->where('config_examen_id', $request->config_examen_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($configDuplicada) {
            return redirect()
                ->back()
                ->withInput()
                ->with('modal_id', $id)
                ->with('mensaje', 'Ya existe una nota para ese examen en esa materia')
                ->with('icono', 'error');
        }

        $nota->postulante_id = $request->postulante_id;
        $nota->materia_id = $request->materia_id;
        $nota->config_examen_id = $request->config_examen_id;
        $nota->nota = $request->nota;
        $nota->save();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se editó una nota: ID ' . $nota->id,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.notas.index')
            ->with('mensaje', 'La nota se ha actualizado correctamente')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $nota = Nota::findOrFail($id);
        $nota->delete();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó una nota: ID ' . $id,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.notas.index')
            ->with('mensaje', 'La nota se ha eliminado correctamente')
            ->with('icono', 'success');
    }
}
