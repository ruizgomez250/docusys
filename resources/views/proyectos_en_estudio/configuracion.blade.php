@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1 class="m-0 custom-heading">Configuración de Proyectos en Estudio</h1>
        </div>
        <div class="col-6">
            <a href="{{ route('proyectos-en-estudio.index') }}" class="btn btn-secondary" style="float: right;">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('plugins.Sweetalert2', true)

@push('js')
    <script>
        $(document).ready(function() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
                color: '#716add',
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

            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
            });
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cog"></i> Configuración General
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Esta configuración se carga una sola vez y se aplica a todos los proyectos generados.
                        El membrete y la leyenda se insertarán automáticamente en el contenido del documento.
                    </p>

                    <form action="{{ route('proyectos-en-estudio.update-config') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="membrete">Membrete (imagen para el encabezado)</label>
                                    @if ($config->membrete)
                                        <div class="mb-2">
                                            <img src="{{ asset($config->membrete) }}" alt="Membrete actual"
                                                style="max-height: 120px; border: 1px solid #ddd; padding: 5px; border-radius: 4px;">
                                            <p class="text-muted small mt-1">Membrete actual. Si subes una nueva imagen, se reemplazará.</p>
                                        </div>
                                    @endif
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" name="membrete" class="custom-file-input" id="membrete" accept="image/*">
                                            <label class="custom-file-label" for="membrete">
                                                {{ $config->membrete ? 'Cambiar imagen' : 'Elegir imagen' }}
                                            </label>
                                        </div>
                                    </div>
                                    <small class="text-muted">Formatos: JPG, PNG, GIF. Tamaño máximo: 2MB.</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="leyenda">Leyenda</label>
                                    <textarea name="leyenda" id="leyenda" class="form-control" rows="4"
                                        style="font-family: 'Times New Roman', Times, serif; font-size: 12pt;">{{ old('leyenda', $config->leyenda) }}</textarea>
                                    <small class="text-muted">Texto que aparecerá en todos los documentos.</small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ultimo_nro_expediente">Último N° de Expediente</label>
                                    <input type="number" name="ultimo_nro_expediente" id="ultimo_nro_expediente"
                                        class="form-control" min="0"
                                        value="{{ old('ultimo_nro_expediente', $config->ultimo_nro_expediente ?? 0) }}">
                                    <small class="text-muted">El próximo proyecto se creará con el N° <strong>{{ ($config->ultimo_nro_expediente ?? 0) + 1 }}</strong>. Cambie este valor para definir desde qué número continuar.</small>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5><i class="fas fa-gavel"></i> Datos de la Sesión</h5>
                        <p class="text-muted">Estos datos se utilizan en el encabezado de los documentos PDF agrupados por fecha.</p>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo_sesion">Tipo de Sesión</label>
                                    <select name="tipo_sesion" id="tipo_sesion" class="form-control">
                                        <option value="EXTRAORDINARIA" {{ old('tipo_sesion', $config->tipo_sesion ?? 'EXTRAORDINARIA') == 'EXTRAORDINARIA' ? 'selected' : '' }}>EXTRAORDINARIA</option>
                                        <option value="ORDINARIA" {{ old('tipo_sesion', $config->tipo_sesion ?? '') == 'ORDINARIA' ? 'selected' : '' }}>ORDINARIA</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nro_sesion">N° de Sesión</label>
                                    <input type="number" name="nro_sesion" id="nro_sesion"
                                        class="form-control" min="1"
                                        value="{{ old('nro_sesion', $config->nro_sesion ?? 1) }}">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Configuración
                                </button>
                                <a href="{{ route('proyectos-en-estudio.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
