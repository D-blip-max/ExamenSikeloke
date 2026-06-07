<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\PostGrupo;
use App\Models\Postulante;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostGrupoController extends Controller
{
    public function index()
    {
        Grupo::ensureAutoGroups();

        $postGrupos = PostGrupo::with(['postulante', 'grupo'])->get();
        $postulantes = Postulante::orderBy('apellidos')->orderBy('nombres')->get();
        $grupos = Grupo::orderBy('nombre')->get();

        return view('admin.post_grupos.index', compact('postGrupos', 'postulantes', 'grupos'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'postulante_id' => 'required|exists:postulantes,id|unique:post_grupos,postulante_id',
            'grupo_id' => 'required|exists:grupos,id',
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($request->filled('grupo_id')) {
                $grupo = Grupo::find($request->grupo_id);
                if ($grupo && $grupo->inscritos >= $grupo->cupo_maximo) {
                    $validator->errors()->add('grupo_id', 'El grupo seleccionado ya alcanzó su cupo máximo.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal_id', null);
        }

        $postGrupo = PostGrupo::create([
            'postulante_id' => $request->postulante_id,
            'grupo_id' => $request->grupo_id,
        ]);

        $grupo = Grupo::find($request->grupo_id);
        if ($grupo) {
            $grupo->inscritos = $grupo->postGrupos()->count();
            $grupo->save();
        }

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se asignó postulante ID ' . $postGrupo->postulante_id . ' al grupo ID ' . $postGrupo->grupo_id,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.post_grupos.index')
            ->with('mensaje', 'La asignación de grupo se creó correctamente.')
            ->with('icono', 'success');
    }

    public function update(Request $request, $id)
    {
        $postGrupo = PostGrupo::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'postulante_id' => 'required|exists:postulantes,id|unique:post_grupos,postulante_id,' . $id,
            'grupo_id' => 'required|exists:grupos,id',
        ]);

        $validator->after(function ($validator) use ($request, $postGrupo) {
            if ($request->filled('grupo_id')) {
                $grupo = Grupo::find($request->grupo_id);
                if ($grupo && $grupo->id !== $postGrupo->grupo_id && $grupo->inscritos >= $grupo->cupo_maximo) {
                    $validator->errors()->add('grupo_id', 'El grupo seleccionado ya alcanzó su cupo máximo.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal_id', $id);
        }

        $oldGrupoId = $postGrupo->grupo_id;

        $postGrupo->update([
            'postulante_id' => $request->postulante_id,
            'grupo_id' => $request->grupo_id,
        ]);

        if ($oldGrupoId !== $postGrupo->grupo_id) {
            $oldGrupo = Grupo::find($oldGrupoId);
            if ($oldGrupo) {
                $oldGrupo->inscritos = $oldGrupo->postGrupos()->count();
                $oldGrupo->save();
            }

            $newGrupo = Grupo::find($postGrupo->grupo_id);
            if ($newGrupo) {
                $newGrupo->inscritos = $newGrupo->postGrupos()->count();
                $newGrupo->save();
            }
        }

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se actualizó asignación ID ' . $postGrupo->id . ' a postulante ID ' . $postGrupo->postulante_id . ' y grupo ID ' . $postGrupo->grupo_id,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.post_grupos.index')
            ->with('mensaje', 'La asignación se actualizó correctamente.')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $postGrupo = PostGrupo::findOrFail($id);
        $postulanteId = $postGrupo->postulante_id;
        $grupoId = $postGrupo->grupo_id;

        $postGrupo->delete();

        $grupo = Grupo::find($grupoId);
        if ($grupo) {
            $grupo->inscritos = $grupo->postGrupos()->count();
            $grupo->save();
        }

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó asignación de postulante ID ' . $postulanteId . ' en grupo ID ' . $grupoId,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.post_grupos.index')
            ->with('mensaje', 'La asignación de grupo se eliminó correctamente.')
            ->with('icono', 'success');
    }
}
