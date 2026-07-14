@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-12">
            <h1 class="m-0 text-dark">Editar Período</h1>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('periodos.index') }}" class="btn btn-secondary float-right">Regresar</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('periodos.update', $periodo) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <label for="nombre">Período</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre"
                                            value="{{ old('nombre',$periodo->nombre) }}">
                                        @error('nombre')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-2">
                                        <label for="inicio">Inicio</label>
                                        <input type="date" class="form-control" id="inicio" name="inicio"
                                            value="{{ old('inicio',$periodo->inicio) }}" required>
                                        @error('inicio')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-2">
                                        <label for="fin">Fin</label>
                                        <input type="date" class="form-control" id="fin" name="fin"
                                            value="{{ old('fin',$periodo->fin) }}" required>
                                        @error('fin')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-2">
                                        <label for="activo">Estado</label>
                                        <select class="form-control" id="activo" name="activo" required>
                                            <option value="" disabled {{ old('activo') === null ? 'selected' : '' }}>
                                                Seleccione un estado</option>
                                            <option value="1" {{ old('activo',$periodo->activo) == '1' ? 'selected' : '' }}>Activo
                                            </option>
                                            <option value="0" {{ old('activo',$periodo->activo) == '0' ? 'selected' : '' }}>Inactivo
                                            </option>
                                        </select>
                                        @error('activo')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>


                                <div class="row mt-5">
                                    <div class="form-group col-md-12">
                                        <button type="submit" style="float: right;"
                                            class="btn btn-primary">Actualizar</button>
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
