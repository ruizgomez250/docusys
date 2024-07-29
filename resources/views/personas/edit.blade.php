@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-12">
            <h1 class="m-0 text-dark">Editar</h1>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('personas.index') }}" class="btn btn-secondary float-right">Regresar</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('personas.update', $persona) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-12 col-md-2">
                                        <label for="ci">CI</label>
                                        <input type="text" class="form-control" id="ci" name="ci"
                                            value="{{ old('ci', $persona->ci) }}">
                                        @error('ci')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <label for="nombre">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre"
                                            value="{{ old('nombre', $persona->nombre) }}" required>
                                        @error('nombre')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <label for="apellido">Apellido</label>
                                        <input type="text" class="form-control" id="apellido" name="apellido"
                                            value="{{ old('apellido', $persona->apellido) }}" required>
                                        @error('apellido')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <label for="direccion">Dirección</label>
                                        <input type="text" class="form-control" id="direccion" name="direccion"
                                            value="{{ old('direccion', $persona->direccion) }}">
                                        @error('direccion')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label for="telefono">Teléfono</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono"
                                            value="{{ old('telefono', $persona->telefono) }}">
                                        @error('telefono')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email', $persona->email) }}">
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label for="fecha_nac">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" id="fecha_nac" name="fecha_nac"
                                            value="{{ old('fecha_nac', $persona->fecha_nac) }}">
                                        @error('fecha_nac')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mt-5">
                                    <div class="form-group col-md-12">
                                        <button type="submit"  style="float: right;"  class="btn btn-primary">Actualizar</button>
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
