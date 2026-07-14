@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-12">
            <h1 class="m-0 text-dark">Registrar Entidades</h1>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('entidades.index') }}" class="btn btn-secondary float-right">Regresar</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('entidades.store') }}" method="POST" autocomplete="off">
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <label for="nombre">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre"
                                            value="{{ old('nombre') }}">
                                        @error('nombre')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>





                                    <div class="col-12 col-md-6">
                                        <label for="descripcion">Descripción</label>
                                        <input type="text" class="form-control" id="descripcion" name="descripcion"
                                            value="{{ old('descripcion') }}">
                                        @error('descripcion')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>



                                <div class="row mt-5">
                                    <div class="form-group col-md-12">
                                        <button type="submit" style="float: right;"
                                            class="btn btn-primary">Guardar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
