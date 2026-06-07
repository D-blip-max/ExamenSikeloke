<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Postulante;
use App\Models\Reprobado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReprobadoController extends Controller
{
    public function index()
    {
        $reprobados = Reprobado::with('postulante')->get();
        $postulantes = Postulante::orderBy('apellidos')->orderBy('nombres')->get();

        return view('admin.reprobados.index', compact('reprobados', 'postulantes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'postulante_id' => 'required|exists:postulantes,id',
            'promedio_final' => 'required|numeric|min:0|max:100',
            'motivo' => 'required|string|max:255',
            'detalle' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal_id', 'create');
        }

        $validated = $validator->validated();
        $postulanteId = $validated['postulante_id'];

        if (Reprobado::where('postulante_id', $postulanteId)->exists()) {
            return redirect()->back()->with('mensaje', 'Este postulante ya está registrado en reprobados')->with('icono', 'error')->with('modal_id', 'create');
        }

        Reprobado::create([
            'postulante_id' => $postulanteId,
            'promedio_final' => $validated['promedio_final'],
            'motivo' => $validated['motivo'],
            'detalle' => $validated['detalle'] ?? null,
            'fecha_registro' => now()->format('Y-m-d'),
        ]);

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se registró reprobado para el postulante ' . $postulanteId,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.reprobados.index')->with('mensaje', 'Registro de reprobado guardado')->with('icono', 'success');
    }

    public function destroy($id)
    {
        $reprobado = Reprobado::findOrFail($id);
        $reprobado->delete();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó reprobado ID ' . $id,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.reprobados.index')->with('mensaje', 'Registro eliminado')->with('icono', 'success');
    }
}
