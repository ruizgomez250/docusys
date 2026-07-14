<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Firmante;
use App\Models\MesaEntrada;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\ProyectosEnEstudio;
use Illuminate\Http\Request;

class AutocompleteController extends Controller
{
    //
    public function autocomplete(Request $request)
    {
        $search = $request->get('term');       
        $results = Cliente::where(function($query) use ($search) {
            $query->where('razonsocial', 'LIKE', '%' . $search . '%')
                  ->orWhere('ruc', 'LIKE', '%' . $search . '%');
        })->where('id_estado', 1) // estado 1 = activo
        ->get();          
        return response()->json($results);
    }


    public function proveedor(Request $request)
    {
        $search = $request->get('term');       
        $results = Proveedor::where(function($query) use ($search) {
            $query->where('razonsocial', 'LIKE', '%' . $search . '%')
                  ->orWhere('ruc', 'LIKE', '%' . $search . '%');
        })->where('id_estado', 4) // estado 4 = activo
        ->get();          
        return response()->json($results);
    }

    public function getfirmante(Request $request)
    {
        $search = $request->get('term');       
        $results = Firmante::where(function($query) use ($search) {
            $query->where('nombre', 'LIKE', '%' . $search . '%')
                  ->orWhere('cedula', 'LIKE', '%' . $search . '%');
        })//->where('id_estado', 18) // estado 4 = activo
        ->get();          
        return response()->json($results);

    }

    public function getObservacion(Request $request)
    {
        $search = $request->get('term');
        $results = MesaEntrada::where('observacion', 'LIKE', '%' . $search . '%')
            ->whereNotNull('observacion')
            ->where('observacion', '!=', '')
            ->distinct()
            ->limit(10)
            ->pluck('observacion');
        return response()->json($results);
    }

    public function getAcapite(Request $request)
    {
        $search = $request->get('term');
        $results = ProyectosEnEstudio::where('acapite', 'LIKE', '%' . $search . '%')
            ->whereNotNull('acapite')
            ->where('acapite', '!=', '')
            ->distinct()
            ->limit(10)
            ->pluck('acapite');
        return response()->json($results);
    }

    public function getDestino(Request $request)
    {
        $search = $request->get('term');
        $results = ProyectosEnEstudio::where('destino', 'LIKE', '%' . $search . '%')
            ->whereNotNull('destino')
            ->where('destino', '!=', '')
            ->distinct()
            ->limit(10)
            ->pluck('destino');
        return response()->json($results);
    }

}
