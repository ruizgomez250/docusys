<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Firmante;
use App\Models\Producto;
use App\Models\Proveedor;
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

}
