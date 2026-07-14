@extends('adminlte::page')

@section('content_header')
    <h1 class="m-0 custom-heading">Editar Destino</h1>
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
                    <form action="{{ route('destino.update', $destino->id) }}" method="post" autocomplete="off">
                        @csrf
                        @method('PUT') <!-- O cambia a @method('PATCH') según lo que esté configurado en tu sistema -->
                        <div class="row">
                            <x-adminlte-input name="nombre" label="Nombre" placeholder="Ingresar" fgroup-class="col-md-8"
                                value="{{ $destino->nombre }}" required />
                            <div class="form-group col-md-4">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="default" name="default"
                                        {{ $destino->default == '0' ? '' : 'checked' }} >
                                    <label class="custom-control-label" for="default">Destino Inicial</label>
                                </div>
                            </div>                           
                            
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <a class="btn btn-danger" style="float: right;" href="{{ route('destino.index') }}">Cancelar</a>
                                <x-adminlte-button class="btn-group" style="float: right;" type="submit" label="Guardar Cambios"
                                    theme="primary" icon="fas fa-lg fa-save" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
