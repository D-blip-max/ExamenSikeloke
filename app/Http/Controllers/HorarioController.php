<?php

namespace App\Http\Controllers;

use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Bitacora;

class HorarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $horarios = Horario::all();
        return view('admin.horarios.index', compact('horarios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'horaInicio_create' => 'required|date_format:H:i',
            'horaFin_create' => 'required|date_format:H:i',
        ]);

        $validator->after(function ($validator) use ($request) {
            $horaInicio = $request->input('horaInicio_create');
            $horaFin = $request->input('horaFin_create');

            if ($horaInicio >= $horaFin) {
                $validator->errors()->add('horaInicio_create', 'La hora de inicio debe ser anterior a la hora de fin.');
            }

            if (Horario::whereTime('horaInicio', '<', $horaFin)
                ->whereTime('horaFin', '>', $horaInicio)
                ->exists()
            ) {
                $validator->errors()->add('horaInicio_create', 'Este horario se solapa con otro horario existente.');
            }
        });

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $horario = new Horario();
        $horario->horaInicio = $request->horaInicio_create;
        $horario->horaFin = $request->horaFin_create;
        $horario->save();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se creó un horario: ' . $horario->horaInicio . ' - ' . $horario->horaFin,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.horarios.index')
            ->with('mensaje', 'El horario se ha creado correctamente.')
            ->with('icono', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Horario $horario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Horario $horario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'horaInicio' => 'required|date_format:H:i',
            'horaFin' => 'required|date_format:H:i',
        ]);

        $validator->after(function ($validator) use ($request, $id) {
            $horaInicio = $request->input('horaInicio');
            $horaFin = $request->input('horaFin');

            if ($horaInicio >= $horaFin) {
                $validator->errors()->add('horaInicio', 'La hora de inicio debe ser anterior a la hora de fin.');
            }

            if (Horario::where('id', '<>', $id)
                ->whereTime('horaInicio', '<', $horaFin)
                ->whereTime('horaFin', '>', $horaInicio)
                ->exists()
            ) {
                $validator->errors()->add('horaInicio', 'Este horario se solapa con otro horario existente.');
            }
        });

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal_id', $id);
        }

        $horario = Horario::find($id);
        $horario->horaInicio = $request->horaInicio;
        $horario->horaFin = $request->horaFin;
        $horario->save();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se editó un horario: ' . $horario->horaInicio . ' - ' . $horario->horaFin,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.horarios.index')
            ->with('mensaje', 'El horario se ha actualizado correctamente')
            ->with('icono', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $horario = Horario::find($id);
        $descripcion = $horario->horaInicio . ' - ' . $horario->horaFin;
        $horario->delete();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó un horario: ' . $descripcion,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.horarios.index')
            ->with('mensaje', 'El horario se ha eliminado correctamente')
            ->with('icono', 'success');
    }
}
