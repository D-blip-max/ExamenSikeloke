<?php

namespace App\Http\Controllers;

use App\Models\Admitido;
use App\Models\Bitacora;
use App\Models\Carrera;
use App\Models\Nota;
use App\Models\Postulante;
use App\Models\Reprobado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdmitidoController extends Controller
{
    public function index()
    {
        $admitidos = Admitido::with(['postulante', 'carrera'])->get();
        $postulantes = Postulante::all();
        return view('admin.admitidos.index', compact('admitidos', 'postulantes'));
    }

    public function asignarCarrera(Request $request)
    {
        // Obtener postulante_id desde request
        $postulante_id = $request->input('postulante_id');
        // Validación 1: postulante INSCRITO
        $postulante = Postulante::findOrFail($postulante_id);
        if ($postulante->estado_inscripcion !== 'INSCRITO') {
            return redirect()->back()->with('mensaje', 'El postulante no está inscrito')->with('icono', 'error');
        }

        // Validación 2: debe tener 12 notas
        $countNotas = Nota::where('postulante_id', $postulante_id)->count();
        if ($countNotas != 12) {
            return redirect()->back()->with('mensaje', 'El postulante no tiene todas las notas registradas.\nDebe tener 3 exámenes en cada una de las 4 materias')->with('icono', 'error');
        }

        // Validación 3: comprobar si ya existe un registro en admitidos
        // Si existe y ya tiene una carrera asignada, bloquear; si existe pero está pendiente, permitimos actualizarlo.
        $existe = Admitido::where('postulante_id', $postulante_id)->first();
        if ($existe && $existe->carrera_id !== null) {
            return redirect()->back()->with('mensaje', 'Este postulante ya tiene carrera asignada')->with('icono', 'error');
        }

        // Paso 1: calcular promedio_final
        $promedios = Nota::selectRaw('notas.materia_id, SUM(notas.nota * config_porcentaje.ponderacion / 100) as promedio')
            ->join('config_porcentaje', 'notas.config_examen_id', '=', 'config_porcentaje.id')
            ->where('notas.postulante_id', $postulante_id)
            ->groupBy('notas.materia_id')
            ->get();

        if ($promedios->count() == 0) {
            return redirect()->back()->with('mensaje', 'No se pudieron calcular los promedios del postulante')->with('icono', 'error');
        }

        $promedioFinal = round($promedios->avg('promedio'), 2);

        // Paso 2: si promedio_final < 60 -> reprobado
        if ($promedioFinal < 60) {
            if (!Reprobado::where('postulante_id', $postulante_id)->exists()) {
                Reprobado::create([
                    'postulante_id' => $postulante_id,
                    'promedio_final' => $promedioFinal,
                    'motivo' => 'PROMEDIO INSUFICIENTE',
                    'detalle' => "Promedio final {$promedioFinal} menor a 60",
                    'fecha_registro' => now()->format('Y-m-d'),
                ]);

                Bitacora::create([
                    'user_id' => auth()->user()->id,
                    'accion' => 'Se registró reprobado por promedio insuficiente para el postulante ' . $postulante_id,
                    'hora' => now('America/La_Paz'),
                ]);
            }

            return redirect()->route('admin.reprobados.index')->with('mensaje', "El postulante reprobó con promedio {$promedioFinal}. Registro guardado en Reprobados.")->with('icono', 'error');
        }

        // Comenzar transacción para inserciones y decremento de cupos
        DB::beginTransaction();
        try {
            $carrera1 = Carrera::find($postulante->carrera1_id);
            $carrera2 = Carrera::find($postulante->carrera2_id);

            // Paso 3: evaluar carrera1
            if ($carrera1 && $promedioFinal >= $carrera1->nota_minima && $carrera1->cupo_disponible > 0) {
                if ($existe && $existe->carrera_id === null) {
                    $existe->update([
                        'carrera_id' => $carrera1->id,
                        'opcion_asignada' => '1RA OPCIÓN',
                        'promedio_final' => $promedioFinal,
                        'fecha_asignacion' => now()->format('Y-m-d'),
                    ]);
                    $admitido = $existe;
                } else {
                    $admitido = Admitido::create([
                        'postulante_id' => $postulante_id,
                        'carrera_id' => $carrera1->id,
                        'opcion_asignada' => '1RA OPCIÓN',
                        'promedio_final' => $promedioFinal,
                        'fecha_asignacion' => now()->format('Y-m-d'),
                    ]);
                }

                $carrera1->cupo_disponible = max(0, $carrera1->cupo_disponible - 1);
                $carrera1->save();

                DB::commit();

                Bitacora::create([
                    'user_id' => auth()->user()->id,
                    'accion' => 'Se asignó carrera 1RA OPCIÓN al postulante ' . $postulante->id,
                    'hora' => now('America/La_Paz'),
                ]);

                return redirect()->route('admin.admitidos.index')->with('mensaje', 'Postulante admitido en 1RA OPCIÓN')->with('icono', 'success');
            }

            // Paso 4: evaluar carrera2
            if ($carrera2 && $promedioFinal >= $carrera2->nota_minima && $carrera2->cupo_disponible > 0) {
                if ($existe && $existe->carrera_id === null) {
                    $existe->update([
                        'carrera_id' => $carrera2->id,
                        'opcion_asignada' => '2DA OPCIÓN',
                        'promedio_final' => $promedioFinal,
                        'fecha_asignacion' => now()->format('Y-m-d'),
                    ]);
                    $admitido = $existe;
                } else {
                    $admitido = Admitido::create([
                        'postulante_id' => $postulante_id,
                        'carrera_id' => $carrera2->id,
                        'opcion_asignada' => '2DA OPCIÓN',
                        'promedio_final' => $promedioFinal,
                        'fecha_asignacion' => now()->format('Y-m-d'),
                    ]);
                }

                $carrera2->cupo_disponible = max(0, $carrera2->cupo_disponible - 1);
                $carrera2->save();

                DB::commit();

                Bitacora::create([
                    'user_id' => auth()->user()->id,
                    'accion' => 'Se asignó carrera 2DA OPCIÓN al postulante ' . $postulante->id,
                    'hora' => now('America/La_Paz'),
                ]);

                return redirect()->route('admin.admitidos.index')->with('mensaje', 'Postulante admitido en 2DA OPCIÓN')->with('icono', 'success');
            }

            // Paso 5: buscar alternativa
            $alternativa = Carrera::where('cupo_disponible', '>', 0)
                ->where('nota_minima', '<=', $promedioFinal)
                ->where('id', '!=', $postulante->carrera1_id)
                ->where('id', '!=', $postulante->carrera2_id)
                ->orderBy('nota_minima', 'asc')
                ->first();

            if ($alternativa) {
                if ($existe && $existe->carrera_id === null) {
                    $existe->update([
                        'carrera_id' => $alternativa->id,
                        'opcion_asignada' => 'ALTERNATIVA',
                        'promedio_final' => $promedioFinal,
                        'fecha_asignacion' => now()->format('Y-m-d'),
                    ]);
                    $admitido = $existe;
                } else {
                    $admitido = Admitido::create([
                        'postulante_id' => $postulante_id,
                        'carrera_id' => $alternativa->id,
                        'opcion_asignada' => 'ALTERNATIVA',
                        'promedio_final' => $promedioFinal,
                        'fecha_asignacion' => now()->format('Y-m-d'),
                    ]);
                }

                $alternativa->cupo_disponible = max(0, $alternativa->cupo_disponible - 1);
                $alternativa->save();

                DB::commit();

                Bitacora::create([
                    'user_id' => auth()->user()->id,
                    'accion' => 'Se asignó carrera ALTERNATIVA al postulante ' . $postulante->id,
                    'hora' => now('America/La_Paz'),
                ]);

                return redirect()->route('admin.admitidos.index')->with('mensaje', 'Postulante admitido en ALTERNATIVA')->with('icono', 'success');
            }

            // Paso 6: no hay alternativa -> asignar carrera2 aunque sin cupo
            if ($carrera2) {
                if ($existe && $existe->carrera_id === null) {
                    $existe->update([
                        'carrera_id' => $carrera2->id,
                        'opcion_asignada' => '2DA OPCIÓN',
                        'promedio_final' => $promedioFinal,
                        'fecha_asignacion' => now()->format('Y-m-d'),
                    ]);
                    $admitido = $existe;
                } else {
                    $admitido = Admitido::create([
                        'postulante_id' => $postulante_id,
                        'carrera_id' => $carrera2->id,
                        'opcion_asignada' => '2DA OPCIÓN',
                        'promedio_final' => $promedioFinal,
                        'fecha_asignacion' => now()->format('Y-m-d'),
                    ]);
                }

                DB::commit();

                Bitacora::create([
                    'user_id' => auth()->user()->id,
                    'accion' => 'Se asignó carrera 2DA OPCIÓN (sin cupo) al postulante ' . $postulante->id,
                    'hora' => now('America/La_Paz'),
                ]);

                return redirect()->route('admin.admitidos.index')->with('mensaje', 'Postulante admitido en 2DA OPCIÓN (sin cupo)')->with('icono', 'success');
            }

            DB::rollBack();
            return redirect()->back()->with('mensaje', 'No se pudo asignar una carrera al postulante')->with('icono', 'error');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('mensaje', 'Error al procesar la asignación: ' . $e->getMessage())->with('icono', 'error');
        }
    }

    public function destroy($id)
    {
        $admitido = Admitido::findOrFail($id);
        $admitido->delete();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó admitido ID ' . $id,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.admitidos.index')->with('mensaje', 'Registro eliminado')->with('icono', 'success');
    }
}
