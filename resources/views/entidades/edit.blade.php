@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-12">
            <h1 class="m-0 text-dark">Editar</h1>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('entidades.index') }}" class="btn btn-secondary float-right">Regresar</a>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('entidades.update', $entidad) }}" method="POST" autocomplete="off">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <label for="nombre">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre"
                                            value="{{ old('nombre', $entidad->nombre) }}">
                                        @error('nombre')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <label for="tipo">Tipo</label>
                                        <select class="form-control" id="tipo" name="tipo">
                                            <option value="Comisión Asesora Permanente" {{ $entidad->tipo == 'Comisión Asesora Permanente' ? 'selected' : '' }}>
                                                Comisión Asesora Permanente
                                            </option>
                                            <option value="Comisión Bicameral" {{ $entidad->tipo == 'Comisión Bicameral' ? 'selected' : '' }}>
                                                Comisión Bicameral
                                            </option>
                                            <option value="Comisión Especial" {{ $entidad->tipo == 'Comisión Especial' ? 'selected' : '' }}>
                                                Comisión Especial
                                            </option>
                                            <option value="Órgano de Cooperación | Legislativa | Judícial" {{ $entidad->tipo == 'Órgano de Cooperación | Legislativa | Judícial' ? 'selected' : '' }}>
                                                Órgano de Cooperación | Legislativa | Judícial
                                            </option>
                                        </select>
                                    </div>
                                    

                                    <div class="col-12 col-md-6">
                                        <label for="descripcion">Descripción</label>
                                        <input type="text" class="form-control" id="descripcion" name="descripcion"
                                            value="{{ old('descripcion', $entidad->descripcion) }}" >
                                        @error('descripcion')
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
