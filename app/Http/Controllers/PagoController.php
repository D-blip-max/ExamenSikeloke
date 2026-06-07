<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Postulante;
use App\Models\Bitacora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with('postulante')->get();
        $postulantes = Postulante::orderBy('apellidos')->orderBy('nombres')->get();

        return view('admin.pagos.index', compact('pagos', 'postulantes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'postulante_id' => 'required|exists:postulantes,id',
            'comprobante' => 'required|string|max:255|unique:pagos,comprobante',
            'monto' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'estado' => 'required|in:PENDIENTE,CONFIRMADO',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal_id', null);
        }

        $pago = Pago::create([
            'postulante_id' => $request->postulante_id,
            'comprobante' => $request->comprobante,
            'monto' => $request->monto,
            'fecha' => $request->fecha,
            'estado' => $request->estado,
        ]);

        $this->syncPostulantePayment($pago);

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se creó un pago: ' . $pago->comprobante . ' para el postulante ID ' . $pago->postulante_id,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.pagos.index')
            ->with('mensaje', 'El pago se ha registrado correctamente.')
            ->with('icono', 'success');
    }

    public function update(Request $request, $id)
    {
        $pago = Pago::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'postulante_id' => 'required|exists:postulantes,id',
            'comprobante' => 'required|string|max:255|unique:pagos,comprobante,' . $id,
            'monto' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'estado' => 'required|in:PENDIENTE,CONFIRMADO',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal_id', $id);
        }

        $pago->update([
            'postulante_id' => $request->postulante_id,
            'comprobante' => $request->comprobante,
            'monto' => $request->monto,
            'fecha' => $request->fecha,
            'estado' => $request->estado,
        ]);

        $this->syncPostulantePayment($pago);

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se actualizó el pago: ' . $pago->comprobante . ' para el postulante ID ' . $pago->postulante_id,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.pagos.index')
            ->with('mensaje', 'El pago se ha actualizado correctamente.')
            ->with('icono', 'success');
    }

    public function destroy($id)
    {
        $pago = Pago::findOrFail($id);
        $postulante = $pago->postulante;
        $comprobante = $pago->comprobante;
        $postulanteId = $pago->postulante_id;

        $pago->delete();

        if ($postulante) {
            $this->syncPostulantePaymentForPostulante($postulante);
        }

        Bitacora::create([
            'user_id' => auth()->user()->id,
            'accion' => 'Se eliminó el pago: ' . $comprobante . ' para el postulante ID ' . $postulanteId,
            'hora' => now('America/La_Paz'),
        ]);

        return redirect()->route('admin.pagos.index')
            ->with('mensaje', 'El pago se ha eliminado correctamente.')
            ->with('icono', 'success');
    }

    protected function syncPostulantePayment(Pago $pago)
    {
        $postulante = $pago->postulante;

        if (! $postulante) {
            return;
        }

        if ($pago->estado === 'CONFIRMADO') {
            $postulante->update([
                'pago_confirmado' => 'VERDADERO',
                'estado_inscripcion' => 'INSCRITO',
            ]);
            return;
        }

        $this->syncPostulantePaymentForPostulante($postulante);
    }

    protected function syncPostulantePaymentForPostulante(Postulante $postulante)
    {
        $tieneConfirmado = $postulante->pagos()->where('estado', 'CONFIRMADO')->exists();

        if ($tieneConfirmado) {
            $postulante->update([
                'pago_confirmado' => 'VERDADERO',
                'estado_inscripcion' => 'INSCRITO',
            ]);
        } else {
            $postulante->update([
                'pago_confirmado' => 'FALSO',
                'estado_inscripcion' => 'PENDIENTE_PAGO',
            ]);
        }
    }
}
