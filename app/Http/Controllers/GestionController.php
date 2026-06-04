<?php

namespace App\Http\Controllers;

use App\Models\Gestion;
use Illuminate\Http\Request;
use App\Models\Bitacora;

class GestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $gestiones = Gestion::all();
        return view('admin.gestiones.index', compact('gestiones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //

        return view('admin.gestiones.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //   $datos= request()->all();
        // return response()->json($datos);
        // 1. Validamos los datos que vienen del formulario
        $request->validate([
            'nombre' => 'required|max:255|unique:gestions',
        ]);

        $gestion = new Gestion();
        $gestion->nombre = $request->nombre;
        $gestion->save();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Creó una Gestion: ' . $gestion->nombre,
            'hora' => now('America/La_Paz'),
        ]);
        return redirect()->route('admin.gestiones.index')
            ->with('mensaje', 'La gestión se ha creado correctamente.')
            ->with('icono', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Gestion $gestion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $gestion = Gestion::find($id);
        return view('admin.gestiones.edit', compact('gestion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'nombre' => 'required|max:255|unique:gestions,nombre,' . $id,
        ]);

        $gestion = Gestion::find($id);
        $gestion->nombre = $request->nombre;
        $gestion->save();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Actualizó la Gestion: ' . $gestion->nombre,
            'hora' => now('America/La_Paz'),
        ]);
        return redirect()->route('admin.gestiones.index')
            ->with('mensaje', 'La gestión se ha actualizado correctamente.')
            ->with('icono', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $gestion = Gestion::find($id);
        $gestion->delete();

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Eliminó la Gestion: ' . $gestion->nombre,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.gestiones.index')
            ->with('mensaje', 'La gestión se ha eliminado correctamente.')
            ->with('icono', 'success');
    }
}
