<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DestinoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $heads = [
            'ID', 'Nombre', 'Destino Inicial', 'Acción'
        ];
        $destinos = Destino::all();
        return view('destino.index', ['destinos' => $destinos, 'heads' => $heads]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('destino.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            // Validar los datos del formulario
            $request->validate([
                'nombre' => 'required|string|max:255',
            ]);

            // Asignar el valor del checkbox
            $data = $request->all();
            $data['default'] = $request->has('default') ? 1 : 0;
            if ($data['default'] == 1) {
                Destino::where('default', 1)->update(['default' => 0]);
            }
            // Crear el nuevo destino
            Destino::create($data);
            return redirect()->route('destino.create')->with('success', 'Operación exitosa');
        } catch (Exception $e) {
            //dd($e);
            // Puedes registrar el error si lo deseas
            //Log::error('Error al crear el firmante: ' . $e->getMessage());

            // Redirigir con un mensaje de error
            return redirect()->route('destino.create')->with('error', 'Hubo un problema con la operación. Por favor, inténtelo de nuevo.');
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
    public function edit(Destino $destino): View
    {
        try {
            return view('destino.edit', ['destino' => $destino]);
        } catch (Exception $e) {
            return view('destino.index')->with('message', 'No se pudo completar la operación.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Destino $destino)
    {
        try {
            $request->validate([
                'nombre' => 'string|max:255',
            ]);
            
            $defaultValue = 0;
            if ($request->filled('default')) {
                $defaultValue = $request->input('default') == 'on' ? 1 : 0;
            }
            $data = $request->all();
            $data['default'] = $request->has('default') ? 1 : 0;
            if ($data['default'] == 1) {
                Destino::where('default', 1)->update(['default' => 0]);
            }
            $request->merge(['default' => $defaultValue]);
            $destino->update($request->all());
            return redirect()->route('destino.edit', $destino)->with('success', 'Operación exitosa');
            //return view('firmante.edit', ['firmante' => $firmante, 'success' => 'Operación exitosa'])->with('success', 'Operación exitosa');
        } catch (Exception $e) {
            // Puedes registrar el error si lo deseas
            //Log::error('Error al crear el firmante: ' . $e->getMessage());

            // Redirigir con un mensaje de error
            return redirect()->route('destino.index')->with('error', 'Hubo un problema con la operación. Por favor, inténtelo de nuevo.');
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
