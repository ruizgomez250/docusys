@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-12">
            <h1 class="m-0 text-dark">Legisladores</h1>
            <div class="row">
                <div class="col-md-12">
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <div class="card">

                        <div class="card-header">
                            <a href="{{ route('personas.create') }}" class="btn btn-primary float-left">Add Personas</a>
                        </div>

                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <th>ID</th>
                                    <th>CI</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Acciones</th>
                                </thead>
                                <tbody>
                                    @foreach ($personas as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->ci }}</td>
                                            <td>{{ $item->nombre }}</td>
                                            <td>{{ $item->apellido }}</td>
                                            <td>
                                                
                                                <a href="{{ route('personas.edit', $item->id) }}"
                                                    class="btn btn-sm btn-outline-secondary"><i
                                                        class="fa fa-sm fa-fw fa-pen"></i></a>

                                                <form action="{{ route('personas.destroy', $item->id) }}" method="post"
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
