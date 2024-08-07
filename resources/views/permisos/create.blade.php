@extends('adminlte::page')

@section('content_header')
    <h1>Agregar Permiso</h1>
@stop

@section('content')
    <form action="{{ route('permisos.store') }}" method="post">
        @csrf
        <div class="form-group">
            <label for="legislador_id">Legislador</label>
            <select class="form-control" id="legislador_id" name="legislador_id">
                @foreach ($legisladores as $legislador)
                    <option value="{{ $legislador->id }}">{{ $legislador->nombre }} {{ $legislador->apellido }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="tipo_permiso">Tipo de Permiso</label>
            <select class="form-control" id="tipo_permiso" name="tipo_permiso">
                <option value="Particular">Particular</option>
                <option value="Reposo Médico">Reposo Médico</option>
                <option value="Otro">Otro</option>
            </select>
        </div>
        <div class="form-group">
            <label for="fecha">Fecha</label>
            <input type="date" class="form-control" id="fecha" name="fecha">
        </div>

        <div class="form-group">
            <label for="observacion">Observación</label>
            <textarea class="form-control" id="observacion" name="observacion" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label for="estado">Estado</label>
            <select class="form-control" id="estado" name="estado">
                <option value="Vigente">Vigente</option>
                <option value="Anulado">Anulado</option>
            </select>
        </div>
        <button type="submit" style="float: right;" class="btn btn-primary">Guardar</button>
    </form>
@stop
