@extends('adminlte::page')

@section('content_header')
    <div class="row">
        @error('error')
            <small class="text-danger">{{ $error }}</small>
        @enderror
        <div class="col-12">
            <h1 class="m-0 text-dark">Registrar Legislador</h1>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('legislador.index') }}" class="btn btn-secondary float-right">Regresar</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('legislador.store') }}" method="POST" autocomplete="off">
                                @csrf
                                <div class="row">
                                    <div class="col-12 col-md-2">
                                        <label for="ci">CI</label>
                                        <input type="text" class="form-control" id="ci" name="ci"
                                            value="{{ old('ci') }}">
                                        @error('ci')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <label for="nombre">Nombre(s)</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre"
                                            value="{{ old('nombre') }}" required>
                                        @error('nombre')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <label for="apellido">Apellido(s)</label>
                                        <input type="text" class="form-control" id="apellido" name="apellido"
                                            value="{{ old('apellido') }}" required>
                                        @error('apellido')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-12 col-md-5">
                                        <label for="circunscripcion">Circunscripción</label>
                                        <input type="text" class="form-control" id="circunscripcion" name="circunscripcion"
                                            value="{{ old('circunscripcion') }}">
                                        @error('circunscripcion')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-2">
                                        <label for="telefono">Teléfono</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono"
                                            value="{{ old('telefono') }}">
                                        @error('telefono')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email') }}">
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-2">
                                        <label for="fecha_nac">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" id="fecha_nac" name="fecha_nac"
                                            value="{{ old('fecha_nac') }}">
                                        @error('fecha_nac')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <label for="cargo">Cargo</label>
                                        <input type="text" class="form-control" id="cargo" name="cargo"
                                            value="{{ old('cargo') }}" required>
                                        @error('cargo')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <label for="partido_id">Partido Político</label>
                                        <select name="partido_id" id="partido_id" class="form-control" required>
                                            <option value="">Seleccione un partido</option>
                                            @foreach ($partidos as $partido)
                                                <option value="{{ $partido->id }}">{{ $partido->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @error('partido_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label for="periodos">Periodo Legislativo</label>
                                        <select name="periodos[]" id="periodos" class="form-control"  required>
                                            @foreach ($periodos as $periodo)
                                                <option value="{{ $periodo->id }}">{{ $periodo->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @error('periodos')
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
