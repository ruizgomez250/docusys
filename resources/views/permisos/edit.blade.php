@extends('adminlte::page')

@section('content_header')
    <h1>Editar Permiso</h1>
@stop

@section('content')
    <form action="{{ route('permisos.update', $permiso) }}" method="post">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="legislador_id">Legislador</label>
            <select class="form-control" id="legislador_id" name="legislador_id">
                @foreach ($legisladores as $legislador)
                    <option value="{{ $legislador->id }}" {{ $legislador->id == $permiso->legislador_id ? 'selected' : '' }}>
                        {{ $legislador->nombre }} {{ $legislador->apellido }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="tipo_permiso">Tipo de Permiso</label>
            <select class="form-control" id="tipo_permiso" name="tipo_permiso">
                <option value="Misión" {{ $permiso->tipo_permiso == 'Misión' ? 'selected' : '' }}>Misión</option>
                <option value="Particular" {{ $permiso->tipo_permiso == 'Particular' ? 'selected' : '' }}>Particular</option>
                <option value="Salud" {{ $permiso->tipo_permiso == 'Salud' ? 'selected' : '' }}>Salud</option>
                <option value="Otros" {{ $permiso->tipo_permiso == 'Otros' ? 'selected' : '' }}>Otros</option>
            </select>
        </div>
        <div class="form-group">
            <label for="fecha">Fecha</label>
            <input type="date" class="form-control" id="fecha" name="fecha" value="{{ $permiso->fecha }}">
        </div>

        <div class="form-group">
            <label for="observacion">Observación</label>
            <textarea class="form-control" id="observacion" name="observacion" rows="3"> {{$permiso->observacion}}</textarea>
        </div>

        <div class="form-group">
            <label for="estado">Estado</label>
            <select class="form-control" id="estado" name="estado">
                <option value="Vigente" {{ $permiso->estado == 'Vigente' ? 'selected' : '' }}>Vigente</option>
                <option value="Anulado" {{ $permiso->estado == 'Anulado' ? 'selected' : '' }}>Anulado</option>
            </select>
        </div>
        <button type="submit" style="float: right;" class="btn btn-primary">Actualizar</button>
    </form>
@stop
