<?php

namespace App\Http\Controllers;

use App\Models\Admitido;
use App\Models\Bitacora;
use App\Models\Carrera;
use App\Models\ConfigPorcentaje;
use App\Models\Materia;
use App\Models\Nota;
use App\Models\Postulante;
use App\Models\Reprobado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NotaController extends Controller
{
    public function index()
    {
        $notas = Nota::with(['postulante', 'materia', 'configExamen'])->get();
        $postulantes = Postulante::orderBy('apellidos')->orderBy('nombres')->get();
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

        // Verificar si se completaron las 12 notas y procesar automáticamente
        $this->verificarYProcesarNotas($request->postulante_id_create);

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

        // Verificar si se completaron las 12 notas y procesar automáticamente
        $this->verificarYProcesarNotas($request->postulante_id);

        return redirect()->route('admin.notas.index')
            ->with('mensaje', 'La nota se ha actualizado correctamente')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $nota = Nota::findOrFail($id);
        $nota->delete();

        $postulanteId = $nota->postulante_id;

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó una nota: ID ' . $id,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.notas.index')
            ->with('mensaje', 'La nota se ha eliminado correctamente')
            ->with('icono', 'success');
    }

    // Método helper para verificar si se completaron 12 notas y procesar automáticamente
    private function verificarYProcesarNotas($postulanteId)
    {
        $countNotas = Nota::where('postulante_id', $postulanteId)->count();

        // Si no tiene exactamente 12 notas, no procesar
        if ($countNotas != 12) {
            return;
        }

        // Verificar si ya está en admitidos o reprobados
        if (Admitido::where('postulante_id', $postulanteId)->exists()) {
            return;
        }

        if (Reprobado::where('postulante_id', $postulanteId)->exists()) {
            return;
        }

        // Obtener postulante
        $postulante = Postulante::findOrFail($postulanteId);

        // Calcular promedio final
        $promedios = Nota::selectRaw('notas.materia_id, SUM(notas.nota * config_porcentaje.ponderacion / 100) as promedio')
            ->join('config_porcentaje', 'notas.config_examen_id', '=', 'config_porcentaje.id')
            ->where('notas.postulante_id', $postulanteId)
            ->groupBy('notas.materia_id')
            ->get();

        if ($promedios->count() == 0) {
            return;
        }

        $promedioFinal = round($promedios->avg('promedio'), 2);

        // Si promedio < 60, registrar como reprobado
        if ($promedioFinal < 60) {
            Reprobado::create([
                'postulante_id' => $postulanteId,
                'promedio_final' => $promedioFinal,
                'motivo' => 'PROMEDIO INSUFICIENTE',
                'detalle' => "Se completaron las 12 notas con promedio {$promedioFinal}",
                'fecha_registro' => now()->format('Y-m-d'),
            ]);

            Bitacora::create([
                'user_id' => auth()->user()->id,
                'accion' => "Se registró automáticamente como reprobado al postulante {$postulanteId} con promedio {$promedioFinal}",
                'hora' => now('America/La_Paz'),
            ]);

            return;
        }

        // Si promedio >= 60, intentar asignar automáticamente entre las dos opciones del postulante
        $carrera1 = $postulante->carrera1_id ? Carrera::find($postulante->carrera1_id) : null;
        $carrera2 = $postulante->carrera2_id ? Carrera::find($postulante->carrera2_id) : null;

        // Si no tiene opciones, crear admitido sin carrera (fallback)
        if (!$carrera1 && !$carrera2) {
            Admitido::create([
                'postulante_id' => $postulanteId,
                'carrera_id' => null,
                'opcion_asignada' => 'AUTOMÁTICO',
                'promedio_final' => $promedioFinal,
                'fecha_asignacion' => now()->format('Y-m-d'),
            ]);

            Bitacora::create([
                'user_id' => auth()->user()->id,
                'accion' => "Se registró automáticamente como admitido al postulante {$postulanteId} con promedio {$promedioFinal} (sin opciones de carrera)",
                'hora' => now('America/La_Paz'),
            ]);

            return;
        }

        // Preparar lista de opciones disponibles (solo las dos opciones del postulante)
        $opciones = [];
        if ($carrera1) $opciones[] = ['model' => $carrera1, 'tipo' => '1RA OPCIÓN'];
        if ($carrera2) $opciones[] = ['model' => $carrera2, 'tipo' => '2DA OPCIÓN'];

        // Buscar candidatas que cumplan nota_minima
        $conCupo = [];
        $sinCupo = [];
        foreach ($opciones as $opt) {
            $c = $opt['model'];
            if ($promedioFinal >= $c->nota_minima) {
                if ($c->cupo_disponible > 0) {
                    $conCupo[] = $opt;
                } else {
                    $sinCupo[] = $opt;
                }
            }
        }

        DB::beginTransaction();
        try {
            // Priorizar primera opción si cumple con la nota mínima y tiene cupo
            if ($carrera1 && $promedioFinal >= $carrera1->nota_minima && $carrera1->cupo_disponible > 0) {
                $cModel = $carrera1;
                $opcion = '1RA OPCIÓN';
            } elseif ($carrera2 && $promedioFinal >= $carrera2->nota_minima && $carrera2->cupo_disponible > 0) {
                $cModel = $carrera2;
                $opcion = '2DA OPCIÓN';
            } elseif ($carrera1 && $promedioFinal >= $carrera1->nota_minima && $carrera1->cupo_disponible <= 0) {
                $cModel = $carrera1;
                $opcion = '1RA OPCIÓN (sin cupo)';
            } elseif ($carrera2 && $promedioFinal >= $carrera2->nota_minima && $carrera2->cupo_disponible <= 0) {
                $cModel = $carrera2;
                $opcion = '2DA OPCIÓN (sin cupo)';
            } else {
                // Si ninguna opción cumple la nota_minima, asignar a la carrera con menor nota_minima entre las opciones disponibles
                usort($opciones, function ($a, $b) {
                    return $a['model']->nota_minima <=> $b['model']->nota_minima;
                });
                $seleccion = $opciones[0];
                $cModel = $seleccion['model'];
                $opcion = $seleccion['tipo'] . ' (ASIGNADA_AUTOMÁTICA)';
            }

            $admitido = Admitido::create([
                'postulante_id' => $postulanteId,
                'carrera_id' => $cModel->id,
                'opcion_asignada' => $opcion,
                'promedio_final' => $promedioFinal,
                'fecha_asignacion' => now()->format('Y-m-d'),
            ]);

            if ($promedioFinal >= $cModel->nota_minima && $cModel->cupo_disponible > 0) {
                $cModel->cupo_disponible = max(0, $cModel->cupo_disponible - 1);
                $cModel->save();
            }

            DB::commit();

            Bitacora::create([
                'user_id' => auth()->user()->id,
                'accion' => "Se asignó automáticamente la carrera {$cModel->nombre} al postulante {$postulanteId}",
                'hora' => now('America/La_Paz'),
            ]);

            return;
        } catch (\Exception $e) {
            DB::rollBack();
            // Si ocurre un error, registrar admitido como pendiente (fallback)
            Admitido::create([
                'postulante_id' => $postulanteId,
                'carrera_id' => null,
                'opcion_asignada' => 'PENDIENTE',
                'promedio_final' => $promedioFinal,
                'fecha_asignacion' => now()->format('Y-m-d'),
            ]);

            DB::commit();

            Bitacora::create([
                'user_id' => auth()->user()->id,
                'accion' => "Se asignó automáticamente (sin cumplir nota_minima) la carrera {$cModel->nombre} al postulante {$postulanteId}",
                'hora' => now('America/La_Paz'),
            ]);

            return;
        } catch (\Exception $e) {
            DB::rollBack();
            // Si ocurre un error, registrar admitido como pendiente (fallback)
            Admitido::create([
                'postulante_id' => $postulanteId,
                'carrera_id' => null,
                'opcion_asignada' => 'PENDIENTE',
                'promedio_final' => $promedioFinal,
                'fecha_asignacion' => now()->format('Y-m-d'),
            ]);

            Bitacora::create([
                'user_id' => auth()->user()->id,
                'accion' => "Error al asignar automáticamente al postulante {$postulanteId}: " . $e->getMessage(),
                'hora' => now('America/La_Paz'),
            ]);
        }
    }
}
