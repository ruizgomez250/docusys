@extends('adminlte::page')
@section('css')
    <style>
        .label {
            padding: 0.2em 0.5em;
            font-weight: bold;
            border-radius: 0.25em;
            display: inline-block;
        }

        .label-success {
            background-color: green;
            color: white;
        }

        .label-danger {
            background-color: red;
            color: white;
        }
    </style>
@endsection
@section('content_header')
    <h1>Permisos</h1>
@stop

@section('content')
    <a href="{{ route('permisos.create') }}" class="btn btn-primary">Agregar Permiso</a>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Legislador</th>
                <th>Tipo de Permiso</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($permisos as $permiso)
                <tr>
                    <td>{{ $permiso->id }}</td>
                    <td>{{ $permiso->legislador->nombre }} {{ $permiso->legislador->apellido }}</td>
                    <td>{{ $permiso->tipo_permiso }}</td>
                    <td>{{ $permiso->fecha }}</td>

                    <td>
                        @if ($permiso->estado == 'Vigente')
                            <label class="label label-success">Vigente</label>
                        @else
                            <label class="label label-danger">Anulado</label>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('permisos.edit', $permiso) }}" class="btn btn-sm btn-outline-secondary">Editar</a>

                        <form action="{{ route('permisos.destroy', $permiso) }}" method="post" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@stop
