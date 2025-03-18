<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $portiposdocs = DB::select("
    SELECT 
        DATE_FORMAT(me.fecha_recepcion, '%Y-%m') AS mes,
        LOWER(td.nombre) AS tipo_doc, -- Convertir a minúsculas
        COUNT(*) AS cantidad
    FROM mesa_entrada me
    JOIN tipo_docs td ON me.id_tipo_doc = td.id
    WHERE me.fecha_recepcion >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY mes, td.nombre
    HAVING cantidad > 0 -- Filtrar solo los que tienen más de 0 documentos
    ORDER BY mes ASC, td.nombre ASC;
");


    $portiposdocs = collect($portiposdocs);

    $documentosporfechas = DB::select("
        SELECT 
            DATE_FORMAT(me.fecha_recepcion, '%Y-%m') AS mes,
            COUNT(*) AS cantidad
        FROM mesa_entrada me
        WHERE me.fecha_recepcion >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY mes
        ORDER BY mes ASC;
    ");

        $documentosporfechas = collect($documentosporfechas);
        return view('reportes.index', ['documentosporfechas' => $documentosporfechas,'portiposdocs' => $portiposdocs]);
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
