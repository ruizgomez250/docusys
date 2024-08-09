@extends('adminlte::page')

@section('content_header')
<h1 class="m-0 custom-heading">Editar Usuario Destino</h1>
@stop
@section('plugins.Sweetalert2', true)

@push('js')
    <script>
        $(document).ready(function() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                showConfirmButton: false,
                timer: 3000
            });

            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: '<label style="font-size: 1.6rem !important;">Operación Exitosa!</label>',
                    text: '{{ session('success') }}',
                });
            @endif

            @if (session('error'))
                Toast.fire({
                    icon: 'error',
                    title: '<label style="font-size: 1.6rem !important;">Error Inesperado!</label>',
                    text: '{{ session('error') }}',
                });
            @endif
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('usuariodestino.update', $usuariodestino->id) }}" method="post" autocomplete="off">
                        @csrf
                        @method('PUT')
                        <div class="row">
                        </div>

                        <div class="row">
                            <x-adminlte-select2 name="user_id" label="Usuario" fgroup-class="col-md-4" required>
                                
                                @foreach($users as $row)
                                    <option value="{{ $row->id }}" {{ $row->id == $usuariodestino->user_id  ? 'selected' : '' }}>{{ $row->name }}</option>
                                @endforeach
                            </x-adminlte-select2>

                            <x-adminlte-select2 name="destino_id" label="Destino" fgroup-class="col-md-4" required>
                               
                                @foreach($destinos as $destino)
                                    <option value="{{ $destino->id }}" {{ $usuariodestino->destino_id == $destino->id ? 'selected' : '' }}>{{ $destino->nombre }}</option>
                                @endforeach
                            </x-adminlte-select2>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <a class="btn btn-danger" style="float: right;" href="{{ route('usuariodestino.index') }}">Cancelar</a>
                                <x-adminlte-button class="btn-group" style="float: right;" type="submit" label="Actualizar" theme="primary" icon="fas fa-lg fa-save" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

