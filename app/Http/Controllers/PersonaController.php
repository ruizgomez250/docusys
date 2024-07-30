<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Legislador;
use Illuminate\Http\Request;
use App\Models\PartidoPolitico;
use App\Models\PeriodoLegislativo;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
    public function index()
    {
        $personas = Persona::all();
        return view('personas.index', compact('personas'));
    }

    public function create()
    {
        $partidos = PartidoPolitico::all();
        $periodos = PeriodoLegislativo::all();

        return view('personas.create', compact('partidos', 'periodos'));
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'ci' => 'nullable|string|max:255|unique:personas,ci',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:personas,email',
            'fecha_nac' => 'nullable|date',
            'cargo' => 'required|string|max:255',
            'partido_id' => 'required|exists:partidos_politicos,id',
            'periodos' => 'required|array',
            'periodos.*' => 'exists:periodos_legislativos,id',
        ]);
       
        //DB::beginTransaction();

        try {
            // Crear la persona
            $persona = Persona::create([
                'ci' => $request->ci,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
                'email' => $request->email,
                'fecha_nac' => $request->fecha_nac,
            ]);

            
            // Crear el legislador
            $legislador = Legislador::create([
                'persona_id' => $persona->id,                
                'partido_id' => $request->partido_id,                
            ]);
           
            //dd($legislador->id);
            // Asociar el legislador con los periodos legislativos
            $legislador->periodos()->attach($request->periodos);

        //    DB::commit();
     
            return redirect()->route('personas.index')->with('success', 'Persona y Legislador creados exitosamente.');
        } catch (\Exception $e) {
          //  DB::rollBack();
            return back()->with('error', 'Error al crear Persona y Legislador: ' . $e->getMessage());
        }
    }

    public function show(Persona $persona)
    {
        return view('personas.show', compact('persona'));
    }

    public function edit(Persona $persona)
    {
        return view('personas.edit', compact('persona'));
    }

    public function update(Request $request, Persona $persona)
    {
        $request->validate([
            'ci' => 'nullable|string|unique:personas,ci,' . $persona->id,
            'nombre' => 'required|string',
            'apellido' => 'required|string',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string',
            'email' => 'nullable|email|unique:personas,email,' . $persona->id,
            'fecha_nac' => 'nullable|date',
        ]);

        $persona->update($request->all());

        return redirect()->route('personas.index')->with('success', 'Persona actualizada exitosamente.');
    }

    public function destroy(Persona $persona)
    {
        $persona->delete();
        return redirect()->route('personas.index')->with('success', 'Persona eliminada exitosamente.');
    }
}
