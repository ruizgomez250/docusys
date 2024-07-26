<?php

namespace App\Http\Controllers;

use App\Models\Destino;
use App\Models\User;
use App\Models\UserDestino;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserDestinoController extends Controller
{
    public function index()
    {
        $heads = [
            'ID', 'Usuario', 'Oficina', 'Acción'
        ];
        $usuariodestino = UserDestino::with(['user', 'destino'])->get();

        return view('usuariodestino.index', ['usuariodestino' => $usuariodestino, 'heads' => $heads]);
    }

    public function create()
    {
        $users = User::all();
        $destinos = Destino::all();
        return view('usuariodestino.create', ['users' => $users, 'destinos' => $destinos]);
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'destino_id' => 'required|exists:destinos,id',
        ]);
        //dd($request->input());
        // Iniciar una transacción de base de datos
        DB::transaction(function () use ($request) {
            // Buscar un registro existente por user_id
            $userDestino = UserDestino::where('user_id', $request->user_id)->first();

            if ($userDestino) {
                // Si se encuentra el registro, actualizarlo
                $userDestino->update($request->all());
            } else {
                // Si no se encuentra, crear un nuevo registro
                UserDestino::create($request->all());
            }
        });

        // Redirigir con un mensaje de éxito
        return redirect()->route('usuariodestino.create')->with('success', 'Operacion Exitosa!!');
    }

    public function show($id)
    {
        $userDestino = UserDestino::with(['user', 'destino'])->findOrFail($id);
        return view('user_destino.show', compact('userDestino'));
    }

    public function edit($id)
    {
        try {
            $usuarioDestino = UserDestino::findOrFail($id);
            $destinos = Destino::all();
            $users = User::all();
            return view('usuariodestino.edit', ['usuariodestino' => $usuarioDestino,'users' => $users, 'destinos' => $destinos]);
        } catch (Exception $e) {
            return redirect()->route('usuariodestino.index')->with('error', 'No se pudo completar la operación.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'destino_id' => 'required|exists:destinos,id',
        ]);

        $userDestino = UserDestino::findOrFail($id);
        $userDestino->update($request->all());

        return redirect()->route('usuariodestino.index')->with('success', 'Operacion Exitosa!!');
    }

    public function destroy($id)
    {
        $userDestino = UserDestino::findOrFail($id);
        $userDestino->delete();

        return redirect()->route('usuariodestino.index')->with('success', 'Operacion Exitosa.');
    }
}
