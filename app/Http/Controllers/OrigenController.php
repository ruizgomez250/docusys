<?php

namespace App\Http\Controllers;

use App\Models\Origen;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrigenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $heads = [
            'ID', 'Indice', 'Subindice', 'Nombre', 'Acción'
        ];
        $origenes = Origen::all();
        return view('origen.index', ['origenes' => $origenes, 'heads' => $heads]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('origen.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            // Validar los datos del formulario
            $request->validate([
                'indice' => 'required|string|max:255',
                'subindice' => 'required|string|max:255',
                'nombre' => 'required|string|max:255',
            ]);

            
            Origen::create($request->all());
            return redirect()->route('origen.create')->with('success', 'Operación exitosa');
        } catch (Exception $e) {
            // Puedes registrar el error si lo deseas
            //Log::error('Error al crear el firmante: ' . $e->getMessage());

            // Redirigir con un mensaje de error
            return redirect()->route('origen.create')->with('error', 'Hubo un problema con la operación. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Origen $origen): View
    {
        try {
            return view('origen.edit', ['origen' => $origen]);
        } catch (Exception $e) {
            return view('origen.index')->with('message', 'No se pudo completar la operación.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Origen $origen)
    {
        try {
            $request->validate([
                'indice' => 'required|string|max:255',
                'subindice' => 'required|string|max:255',
                'nombre' => 'required|string|max:255',
            ]);
            
            
            $origen->update($request->all());
            return redirect()->route('origen.edit', $origen)->with('success', 'Operación exitosa');
            //return view('firmante.edit', ['firmante' => $firmante, 'success' => 'Operación exitosa'])->with('success', 'Operación exitosa');
        } catch (Exception $e) {
            // Puedes registrar el error si lo deseas
            //Log::error('Error al crear el firmante: ' . $e->getMessage());

            // Redirigir con un mensaje de error
            return redirect()->route('origen.index')->with('error', 'Hubo un problema con la operación. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
