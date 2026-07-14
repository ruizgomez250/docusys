<?php

namespace App\Http\Controllers;

use App\Models\TipoDoc;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TipoDocController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $heads = [
            'ID',
            'Nombre',
            'Acción'
        ];
        $tipodocs = TipoDoc::all();
        return view('tipodoc.index', ['tipodocs' => $tipodocs, 'heads' => $heads]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tipodoc.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
            ]);

            TipoDoc::create($request->all());
            return redirect()->route('tipodoc.create')->with('success', 'Operación exitosa');
        } catch (Exception $e) {
            // Puedes registrar el error si lo deseas
            //Log::error('Error al crear el firmante: ' . $e->getMessage());

            // Redirigir con un mensaje de error
            return redirect()->route('tipodoc.create')->with('error', 'Hubo un problema con la operación. Por favor, inténtelo de nuevo.');
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
    public function edit(TipoDoc $tipodoc): View
    {
        try {
            return view('tipodoc.edit', ['tipodoc' => $tipodoc]);
        } catch (Exception $e) {
            return view('tipodoc.index')->with('message', 'No se pudo completar la operación.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TipoDoc $tipodoc)
    {

        try {
            $request->validate([
                'nombre' => 'string|max:255',
            ]);

            $tipodoc->update($request->all());
            return redirect()->route('tipodoc.edit', $tipodoc)->with('success', 'Operación exitosa');
            //return view('firmante.edit', ['firmante' => $firmante, 'success' => 'Operación exitosa'])->with('success', 'Operación exitosa');
        } catch (Exception $e) {
            // Puedes registrar el error si lo deseas
            //Log::error('Error al crear el firmante: ' . $e->getMessage());

            // Redirigir con un mensaje de error
            return redirect()->route('tipodoc.index')->with('error', 'Hubo un problema con la operación. Por favor, inténtelo de nuevo.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Buscar el registro por ID
            $tipoDoc = TipoDoc::findOrFail($id);

            // Eliminar el registro
            $tipoDoc->delete();

            // Redirigir con un mensaje de éxito
            return redirect()->route('tipodoc.index')->with('success', 'El registro fue eliminado correctamente.');
        } catch (Exception $e) {
            // Registrar el error si lo deseas
            // Log::error('Error al eliminar el TipoDoc: ' . $e->getMessage());

            // Redirigir con un mensaje de error
            return redirect()->route('tipodoc.index')->with('error', 'Hubo un problema al intentar eliminar el registro. Por favor, inténtelo de nuevo.');
        }
    }
}
