<?php

namespace App\Http\Controllers;

use App\Models\Permiso;
use App\Models\Legislador;
use Illuminate\Http\Request;

class PermisoController extends Controller
{
    public function index()
    {
        $permisos = Permiso::with('legislador')->get();
        return view('permisos.index', compact('permisos'));
    }

    public function create()
    {
        $legisladores = Legislador::where('activo', 1) // Filtra solo los activos
        ->orderBy('nombre', 'asc') // Ordena por nombre de forma ascendente
        ->get(); // Obtiene los resultados
        return view('permisos.create', compact('legisladores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'legislador_id' => 'required|exists:legisladores,id',
            'tipo_permiso' => 'required|in:Particular,Misión,Salud,Otros',
            'fecha' => 'required|date',
            'estado' => 'required|in:Vigente,Anulado',
        ]);

        Permiso::create($request->all());

        return redirect()->route('permisos.index')->with('status', 'Permiso creado con éxito!');
    }

    public function edit(Permiso $permiso)
    {
        $legisladores = Legislador::all();
        return view('permisos.edit', compact('permiso', 'legisladores'));
    }

    public function update(Request $request, Permiso $permiso)
    {
        $request->validate([
            'legislador_id' => 'required|exists:legisladores,id',
            'tipo_permiso' => 'required|in:Particular,Misión,Salud,Otros',
            'fecha' => 'required|date',
            'estado' => 'required|in:Vigente,Anulado',
        ]);

        $permiso->update($request->all());

        return redirect()->route('permisos.index')->with('status', 'Permiso actualizado con éxito!');
    }

    public function destroy(Permiso $permiso)
    {
        $permiso->delete();

        return redirect()->route('permisos.index')->with('status', 'Permiso eliminado con éxito!');
    }
}

