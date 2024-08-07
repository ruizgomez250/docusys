@extends('adminlte::page')

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
                            <span style="color: green;">Vigente</span>
                        @else
                            <span style="color: red;">Anulado</span>
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
