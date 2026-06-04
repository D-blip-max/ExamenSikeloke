<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $bitacoras = Bitacora::with('usuario')->orderBy('hora', 'desc')->get();
       return view('admin.bitacora.index', compact('bitacoras'));
    }

    
}
