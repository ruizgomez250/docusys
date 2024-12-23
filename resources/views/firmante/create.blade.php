@extends('adminlte::page')



@section('content_header')
<h1 class="m-0 custom-heading">Registrar Firmante</h1>
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
        });
    </script>
@endpush
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-body">
                    <form action="{{ route('firmante.store') }}" method="post" autocomplete="off">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <x-adminlte-input name="nombre" label="Nombre"
                                placeholder="Ingresar" fgroup-class="col-md-8"
                                value="{{ old('nombre') }}" required/>
                            <x-adminlte-input name="cedula" label="Cedula" placeholder="Ingresar "
                                fgroup-class="col-md-4" value="{{ old('ruc') }}" />
                        </div>

                        <div class="row">
                            <x-adminlte-input name="telefono" label="Telefono" placeholder="Ingresar"
                                fgroup-class="col-md-3" value="{{ old('telefono') }}"  />
                            <x-adminlte-input name="correo" type="email" label="Email"
                                placeholder="Ingresar " fgroup-class="col-md-3"
                                value="{{ old('correo') }}"/>
                        </div>


                        <div class="row">
                            <div class="form-group col-md-12">
                                <a class="btn btn-danger" style="float: right;" href="{{route('firmante.index')}}">Cancelar</a>
                                <x-adminlte-button class="btn-group" style="float: right;" type="submit" label="Registrar"
                                    theme="primary" icon="fas fa-lg fa-save" />
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
