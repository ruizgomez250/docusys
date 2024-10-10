<?php

namespace App\Http\Controllers;

use App\Models\Firmante;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class FirmanteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $heads = [
            'ID',
            'Nombre',
            'Cedula',
            'Codigo',
            'Correo',
            'Teléfono',
            'Acción'
        ];
        $firmantes = Firmante::all();
        return view('firmante.index', ['firmantes' => $firmantes, 'heads' => $heads]);
    }
    public function create(): View
    {
        return view('firmante.create');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'cedula' => 'nullable|string|max:20',
                'telefono' => 'nullable|string|max:20',
                'correo' => 'nullable|string|email|max:255',
                'codigo' => 'nullable|string|max:255',
            ]);
            Firmante::create($request->all());
            return redirect()->route('firmante.create')->with('success', 'Operación exitosa');
        } catch (Exception $e) {
            // Puedes registrar el error si lo deseas
            //Log::error('Error al crear el firmante: ' . $e->getMessage());

            // Redirigir con un mensaje de error
            return redirect()->route('firmante.create')->with('error', 'Hubo un problema con la operación. Por favor, inténtelo de nuevo (correo registrado).');
        }
    }

    public function edit(Firmante $firmante): View
    {

        try {
            return view('firmante.edit', ['firmante' => $firmante]);
            
        } catch (Exception $e) {
            return view('firmante.index')->with('message', 'No se pudo completar la operación.');
        }
    }

    public function show(Firmante $firmante)
    {
        return response()->json($firmante);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Firmante $firmante)
    {

        try {
            $request->validate([
                'nombre' => 'string|max:255',
                'correo' => 'nullable|string|email|max:255|unique:firmantes,correo,' . $firmante->id,
                'telefono' => 'string|max:20',
                'cedula' => 'string|max:20',
                'codigo' => 'string|max:20',
            ]);

            $firmante->update($request->all());
            return redirect()->route('firmante.edit', $firmante)->with('success', 'Operación exitosa');
            //return view('firmante.edit', ['firmante' => $firmante, 'success' => 'Operación exitosa'])->with('success', 'Operación exitosa');
        } catch (Exception $e) {
            // Puedes registrar el error si lo deseas
            //Log::error('Error al crear el firmante: ' . $e->getMessage());

            // Redirigir con un mensaje de error
            return redirect()->route('firmante.index')->with('error', 'Hubo un problema con la operación. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Buscar el registro del firmante por ID
            $firmante = Firmante::findOrFail($id);

            // Eliminar el registro
            $firmante->delete();

            // Redirigir con un mensaje de éxito
            return redirect()->route('firmante.index')->with('success', 'Firmante eliminado correctamente.');
        } catch (Exception $e) {
            // Puedes registrar el error si lo deseas
            // Log::error('Error al eliminar el firmante: ' . $e->getMessage());

            // Redirigir con un mensaje de error
            return redirect()->route('firmante.index')->with('error', 'Hubo un problema al intentar eliminar el firmante. Por favor, inténtelo de nuevo.');
        }
    }
}
