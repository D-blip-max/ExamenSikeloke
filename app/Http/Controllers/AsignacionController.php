<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\Asignacion;
use App\Models\Bitacora;
use App\Models\Dia;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AsignacionController extends Controller
{
    public function index()
    {
        $asignaciones = Asignacion::with(['grupo', 'materia', 'docente', 'aula', 'dia', 'horario'])->get();
        $asignacionesByGrupo = $asignaciones
            ->groupBy('grupo_id')
            ->sortBy(function ($group) {
                return $group->first()->grupo->nombre ?? '';
            })
            ->map(function ($group) {
                return $group->sortBy(function ($a) {
                    return sprintf('%s %s', $a->horario->horaInicio ?? '', $a->horario->horaFin ?? '');
                })->values();
            });

        $grupos = Grupo::orderBy('nombre')->get();
        $materias = Materia::orderBy('nombre')->get();
        $docentes = Docente::orderBy('nombre')->get();
        $aulas = Aula::orderBy('nombre')->get();
        $dias = Dia::orderBy('id')->get();
        $horarios = Horario::orderBy('horaInicio')->get();

        return view('admin.asignaciones.index', compact('asignacionesByGrupo', 'grupos', 'materias', 'docentes', 'aulas', 'dias', 'horarios'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'grupo_id' => 'required|exists:grupos,id',
            'materia_id' => 'required|exists:materias,id',
            'docente_id' => 'required|exists:docentes,id',
            'aula_id' => 'required|exists:aulas,id',
            'dia_id' => 'required|exists:dias,id',
            'horario_id' => 'required|exists:horarios,id',
        ]);

        $validator->after(function ($validator) use ($request) {
            $this->validateAsignacion($validator, $request);
        });

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal_id', null);
        }

        $asignacion = Asignacion::create([
            'grupo_id' => $request->grupo_id,
            'materia_id' => $request->materia_id,
            'docente_id' => $request->docente_id,
            'aula_id' => $request->aula_id,
            'dia_id' => $request->dia_id,
            'horario_id' => $request->horario_id,
        ]);

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se creó asignación ID ' . $asignacion->id . ' (grupo ' . $request->grupo_id . ', materia ' . $request->materia_id . ', docente ' . $request->docente_id . ')',
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.asignaciones.index')
            ->with('mensaje', 'La asignación se creó correctamente.')
            ->with('icono', 'success');
    }

    public function update(Request $request, $id)
    {
        $asignacion = Asignacion::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'grupo_id' => 'required|exists:grupos,id',
            'materia_id' => 'required|exists:materias,id',
            'docente_id' => 'required|exists:docentes,id',
            'aula_id' => 'required|exists:aulas,id',
            'dia_id' => 'required|exists:dias,id',
            'horario_id' => 'required|exists:horarios,id',
        ]);

        $validator->after(function ($validator) use ($request, $id) {
            $this->validateAsignacion($validator, $request, $id);
        });

        if ($validator->fails()) {
            return redirect()->route('admin.asignaciones.index')
                ->withErrors($validator)
                ->withInput()
                ->with('modal_id', $id);
        }

        $asignacion->update([
            'grupo_id' => $request->grupo_id,
            'materia_id' => $request->materia_id,
            'docente_id' => $request->docente_id,
            'aula_id' => $request->aula_id,
            'dia_id' => $request->dia_id,
            'horario_id' => $request->horario_id,
        ]);

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se actualizó asignación ID ' . $asignacion->id . ' (grupo ' . $request->grupo_id . ', materia ' . $request->materia_id . ', docente ' . $request->docente_id . ')',
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.asignaciones.index')
            ->with('mensaje', 'La asignación se actualizó correctamente.')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $asignacion = Asignacion::findOrFail($id);
        $asignacion->delete();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó asignación ID ' . $id,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.asignaciones.index')
            ->with('mensaje', 'La asignación se eliminó correctamente.')
            ->with('icono', 'success');
    }

    protected function validateAsignacion($validator, Request $request, $excludeId = null)
    {
        $docente = Docente::find($request->docente_id);
        $materia = Materia::find($request->materia_id);

        if ($docente && $docente->estado !== 'ACTIVO') {
            $validator->errors()->add('docente_id', 'El docente no está contratado y no puede ser asignado');
        }

        if ($docente && $materia && $docente->especialidad !== $materia->nombre) {
            $validator->errors()->add('docente_id', 'El docente no corresponde a la especialidad de la materia');
        }

        if ($docente) {
            $gruposAsignados = Asignacion::where('docente_id', $request->docente_id)
                ->when($excludeId, fn ($query) => $query->where('id', '<>', $excludeId))
                ->distinct('grupo_id')
                ->count('grupo_id');

            if ($gruposAsignados >= 4) {
                $validator->errors()->add('docente_id', 'El docente ya tiene el máximo de 4 grupos asignados');
            }
        }

        $query = Asignacion::where('grupo_id', $request->grupo_id)
            ->where('materia_id', $request->materia_id)
            ->where('dia_id', $request->dia_id);

        if ($excludeId) {
            $query->where('id', '<>', $excludeId);
        }

        if ($query->exists()) {
            $validator->errors()->add('dia_id', 'Este grupo ya tiene asignada esa materia en ese día');
        }

        $query = Asignacion::where('docente_id', $request->docente_id)
            ->where('dia_id', $request->dia_id)
            ->where('horario_id', $request->horario_id);

        if ($excludeId) {
            $query->where('id', '<>', $excludeId);
        }

        if ($query->exists()) {
            $validator->errors()->add('horario_id', 'El docente ya tiene clase asignada en ese día y horario');
        }

        $query = Asignacion::where('aula_id', $request->aula_id)
            ->where('dia_id', $request->dia_id)
            ->where('horario_id', $request->horario_id);

        if ($excludeId) {
            $query->where('id', '<>', $excludeId);
        }

        if ($query->exists()) {
            $validator->errors()->add('aula_id', 'El aula ya está ocupada en ese día y horario');
        }
    }
}
