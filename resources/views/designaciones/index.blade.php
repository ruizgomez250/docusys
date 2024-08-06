@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-12">
            <h1 class="m-0 text-dark">Designación</h1>
            <div class="row">
                <div class="col-md-12">
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <div class="card">

                        <div class="card-header">
                            <a href="{{ route('entidades.create') }}" class="btn btn-primary  float-right">Add Entidad</a>
                        </div>

                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Entidad</th>
                                        <th>Cargo</th>
                                        <th>Fecha de Inicio</th>
                                        <th>Fecha de Fin</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($designaciones as $designacion)
                                        <tr>
                                            <td>{{ $designacion->id }}</td>
                                            <td>{{ $designacion->entidad->nombre ?? 'N/A' }}</td>
                                            <td>{{ $designacion->cargo->nombre ?? 'N/A' }}</td>
                                            <td>{{ $designacion->fecha_inicio->format('d/m/Y') }}</td>
                                            <td>{{ $designacion->fecha_fin ? $designacion->fecha_fin->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('designacion.edit', $designacion) }}" class="btn btn-warning btn-sm">Editar</a>
                                                <form action="{{ route('designacion.destroy', $designacion) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar esta designación?')">Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No hay designaciones para este legislador.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
