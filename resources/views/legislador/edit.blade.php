@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-12">
            <h1 class="m-0 text-dark">Editar Legislador</h1>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('legislador.index') }}" class="btn btn-secondary float-right">Regresar</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('legislador.update', $legislador) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-12 col-md-2">
                                        <label for="ci">CI</label>
                                        <input type="text" class="form-control" id="ci" name="ci"
                                            value="{{ old('ci', $legislador->ci) }}">
                                        @error('ci')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <label for="nombre">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre"
                                            value="{{ old('nombre', $legislador->nombre) }}" required>
                                        @error('nombre')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <label for="apellido">Apellido</label>
                                        <input type="text" class="form-control" id="apellido" name="apellido"
                                            value="{{ old('apellido', $legislador->apellido) }}" required>
                                        @error('apellido')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <label for="circunscripcion">Circunscripcion</label>
                                        <input type="text" class="form-control" id="circunscripcion" name="circunscripcion"
                                            value="{{ old('circunscripcion', $legislador->circunscripcion) }}">
                                        @error('circunscripcion')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label for="telefono">Teléfono</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono"
                                            value="{{ old('telefono', $legislador->telefono) }}">
                                        @error('telefono')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email', $legislador->email) }}">
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label for="fecha_nac">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" id="fecha_nac" name="fecha_nac"
                                            value="{{ old('fecha_nac', $legislador->fecha_nac) }}">
                                        @error('fecha_nac')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 col-md-5">
                                        <label for="periodos_disponibles">Periodos Disponibles</label>
                                        <select multiple class="form-control" id="periodos_disponibles">
                                            @foreach ($periodo as $periodo)
                                                <option value="{{ $periodo->id }}">{{ $periodo->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-md-2 d-flex align-items-center justify-content-center">
                                        <div>
                                            <button type="button" class="btn btn-primary" id="addPeriodo">Agregar
                                                &gt;&gt;</button>
                                            <br><br>
                                            <button type="button" class="btn btn-secondary" id="removePeriodo">&lt;&lt;
                                                Quitar</button>
                                        </div>
                                    </div>
                                 
                                    <div class="col-12 col-md-5">
                                        <label for="periodos">Periodos Legislativos</label>
                                        <select multiple class="form-control" id="periodos" name="periodos[]">
                                            @foreach($periodotwo as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ $legislador->periodos->contains($item->id) ? 'selected' : '' }}>
                                                    {{ $item->nombre }}
                                                </option>
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

@section('js')
    <script>
        document.getElementById('addPeriodo').addEventListener('click', function() {
            let selected = document.getElementById('periodos_disponibles').selectedOptions;
            let periodosSelect = document.getElementById('periodos');
            for (let i = 0; i < selected.length; i++) {
                let option = selected[i];
                let newOption = new Option(option.text, option.value, true, true);
                periodosSelect.add(newOption);
                option.remove();
            }
        });

        document.getElementById('removePeriodo').addEventListener('click', function() {
            const select = document.getElementById('periodos');
            const selectedOptions = Array.from(select.selectedOptions);
            selectedOptions.forEach(option => option.remove());
        });
    </script>
@stop
