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
    <div class="row">
        <div class="col-12">
            <h1 class="m-0 text-dark">Periodos</h1>
            <div class="row">
                <div class="col-md-12">
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <div class="card">

                        <div class="card-header">
                            <a href="{{ route('periodos.create') }}" class="btn btn-primary float-left">Agregar Periodo</a>
                        </div>

                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <th>ID</th>
                                    <th>Período</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </thead>
                                <tbody>
                                    @foreach ($periodos as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->nombre }}</td>
                                            <td>{{ formatoFecha($item->inicio) }}</td>
                                            <td>{{ formatoFecha($item->fin) }}</td>
                                            <td>
                                                @if ($item->activo)
                                                    <label class="label label-success">Activo</label>
                                                @else
                                                    <label class="label label-danger">Inactivo</label>
                                                @endif
                                            </td>
                                            <td>

                                                <a href="{{ route('periodos.edit', $item->id) }}"
                                                    class="btn btn-sm btn-outline-secondary"><i
                                                        class="fa fa-sm fa-fw fa-pen"></i></a>

                                                <form action="{{ route('periodos.destroy', $item->id) }}" method="post"
                                                    class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-sm btn-outline-secondary delete-button">
                                                        <ion-icon name="trash-outline"><i
                                                                class="fa fa-sm fa-fw fa-trash"></i></ion-icon>
                                                    </button>
                                                </form>

                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
