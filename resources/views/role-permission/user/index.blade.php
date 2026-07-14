@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-12">
            <h1 class="m-0 text-dark">Users</h1>
            <div class="row">
                <div class="col-md-12">
                    @if (session('status'))
                        <div  id="alert"  class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('users.create') }}" class="btn btn-primary float-left">Agregar Usuario</a>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th>Action</th>
                                </thead>
                                <tbody>
                                    @foreach ($users as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->email }}</td>
                                            <td>
                                                @if (!empty($item->getRoleNames()))
                                                    @foreach ($item->getRoleNames() as $rolname)
                                                        <label for=""
                                                            class="badge badge-primary mx-1">{{ $rolname }}</label>
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('users.edit', $item->id) }}"
                                                    class="btn btn-sm btn-outline-secondary"><i
                                                        class="fa fa-sm fa-fw fa-pen"></i></a>

                                                <form action="{{ route('users.destroy', $item->id) }}" method="post"
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

@push('js')
    <script>
        // Esperar 5 segundos y luego ocultar la alerta
        setTimeout(function() {
            document.getElementById('alert').style.display = 'none';
        }, 4000); // 5000 milisegundos = 5 segundos
    </script>
@endpush
