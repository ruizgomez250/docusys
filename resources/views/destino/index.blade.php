@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1 class="m-0 custom-heading">Lista de Destinos</h1>
        </div>
        <div class="col-6">
            <a href="{{ route('destino.create') }}" class="btn btn-primary" style="float: right;">Registrar Nuevo Destino</a>
        </div>
    </div>
@stop
@section('plugins.Sweetalert2', true)

@push('js')
    <script>
        $(document).ready(function() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                color: '#716add',
                
                showConfirmButton: false,
                timer: 3000               
            });

            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: '<label style="font-size: 1.6rem !important;">Operación Exitosa!</label>',
                    text:  '{{ session('success') }}',
                });
            @endif

            @if (session('error'))
                Toast.fire({
                    icon: 'error',
                    title: '<label style="font-size: 1.6rem !important;">Error Inesperado!</label>',
                    text: '{{ session('error') }}',
                });
            @endif

            // Agregar confirmación de eliminación
            $('.delete-button').on('click', function() {
                var form = $(this).closest('.delete-form');
                Swal.fire({
                    title: 'Confirmar eliminación',
                    text: '¿Estás seguro de que deseas eliminar este cliente?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <x-adminlte-datatable id="table1" :heads="$heads" head-theme="dark" theme="light" striped
                        hoverable with-buttons>
                        @foreach ($destinos as $row)
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td>{{ $row->nombre }}</td>
                                <td class="{{ $row->default == '1' ? 'text-success' : 'text-danger' }}">{{ $row->default == '1' ? 'Si' : 'No' }}</td>                             
                                <td style="float:right;">
                                    <a href="{{ route('destino.edit', $row->id) }}" class="btn btn-outline-secondary"><i
                                            class="fa fa-sm fa-fw fa-pen"></i></a>
                                    <form action="{{ route('destino.destroy', $row->id) }}" method="post"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-outline-secondary delete-button">
                                            <ion-icon name="trash-outline"><i class="fa fa-sm fa-fw fa-trash"></i></ion-icon>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </div>
            </div>
        </div>
    </div>
@stop
