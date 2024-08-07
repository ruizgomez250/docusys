@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-12">
            <h1 class="m-0 text-dark">Designar a <label for="">{{ $legislador->nombre . ' ' . $legislador->apellido }}</label></h1>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('legislador.index') }}" class="btn btn-secondary float-right">Regresar</a>
                        </div>
                        <div class="card-body">

                            <form action="{{ route('designaciones.store') }}" method="POST">
                                @csrf
                                <div class="form-group">                                                              
                                    <input type="hidden" id='legislador_id' name='legislador_id'
                                        value="{{ $legislador->id }}">
                                </div>
                                <div class="form-group">
                                    <label for="entidad_id">Entidad</label>
                                    <select class="form-control" id="entidad_id" name="entidad_id" required>
                                        <option value="">Seleccione una entidad</option>
                                        @foreach ($entidades as $entidad)
                                            <option value="{{ $entidad->id }}">{{ $entidad->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="cargo_id">Cargo</label>
                                    <select class="form-control" id="cargo_id" name="cargo_id">
                                        <option value="">Seleccione un cargo (opcional)</option>
                                        @foreach ($cargos as $cargo)
                                            <option value="{{ $cargo->id }}">{{ $cargo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="fecha_inicio">Fecha de Inicio</label>
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="fecha_fin">Fecha de Fin</label>
                                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin">
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
