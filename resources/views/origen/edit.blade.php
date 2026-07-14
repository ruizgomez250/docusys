@extends('adminlte::page')

@section('content_header')
    <h1 class="m-0 custom-heading">Editar Origen</h1>
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
                    <form action="{{ route('origen.update', $origen->id) }}" method="post" autocomplete="off">
                        @csrf
                        @method('PUT') <!-- O cambia a @method('PATCH') según lo que esté configurado en tu sistema -->
                        <div class="row">
                            <x-adminlte-input name="indice" label="Indice"
                                placeholder="Indice" fgroup-class="col-md-6"
                                value="{{ $origen->indice }}" required/>
                            <x-adminlte-input name="subindice" label="Sub Indice"
                                placeholder="sub Indice" fgroup-class="col-md-6"
                                value="{{ $origen->subindice }}" required/> 
                        </div> 
                        <div class="row">
                            <x-adminlte-input name="nombre" label="Nombre" placeholder="Ingresar" fgroup-class="col-md-8"
                                value="{{ $origen->nombre }}" required />
                                                     
                            
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <a class="btn btn-danger" style="float: right;" href="{{ route('origen.index') }}">Cancelar</a>
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
