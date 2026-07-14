@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-12">
            <h1 class="m-0 text-dark">Roles</h1>
            <div class="row">
                <div class="col-md-12">
                    @if (session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif

                    <div class="card">

                        <div class="card-header">
                            <a href="{{ route('roles.create') }}" class="btn btn-primary float-left">Agregar Roles</a>
                        </div>

                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                    @foreach ($roles as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>
                                                <a href="{{ route('roles.addpermissionrole', $item->id) }}"
                                                    class="btn btn-sm btn-outline-secondary"><i
                                                        class="fa fa-sm fa-fw fa fa-lock"></i></a>
                                                <a href="{{ route('roles.edit', $item->id) }}"
                                                    class="btn btn-sm btn-outline-secondary"><i
                                                        class="fa fa-sm fa-fw fa-pen"></i></a>

                                                <form action="{{ route('roles.destroy', $item->id) }}" method="post"
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
