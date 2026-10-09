@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1 class="m-0 custom-heading">
                Editar Sesión {{ $sesion->tipo_sesion }} N° {{ $sesion->nro_sesion }}
            </h1>
        </div>
        <div class="col-6">
            <a href="{{ route('proyectos-en-estudio.asuntos-entrados') }}" class="btn btn-secondary" style="float: right;">
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

            function actualizarSeleccionados() {
                $('#cantidadSeleccionados').text($('.nuevo-documento-check:checked').length);
            }

            $('#buscarDocumento').on('input', function() {
                var termino = $(this).val().toLocaleLowerCase().trim();
                $('.documento-disponible-row').each(function() {
                    $(this).toggle($(this).text().toLocaleLowerCase().includes(termino));
                });
            });

            $('#seleccionarTodosDisponibles').on('change', function() {
                $('.documento-disponible-row:visible .nuevo-documento-check').prop('checked', this.checked);
                actualizarSeleccionados();
            });

            $('.nuevo-documento-check').on('change', actualizarSeleccionados);
            actualizarSeleccionados();
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Datos de la Sesión
                    </h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('proyectos-en-estudio.update-sesion', $sesion->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fecha_sesion"><strong>Fecha de Sesión</strong></label>
                                    <input type="date" name="fecha_sesion" id="fecha_sesion"
                                        class="form-control" value="{{ $sesion->fecha_sesion->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tipo_sesion"><strong>Tipo de Sesión</strong></label>
                                    <select name="tipo_sesion" id="tipo_sesion" class="form-control" required>
                                        <option value="EXTRAORDINARIA" {{ $sesion->tipo_sesion == 'EXTRAORDINARIA' ? 'selected' : '' }}>EXTRAORDINARIA</option>
                                        <option value="ORDINARIA" {{ $sesion->tipo_sesion == 'ORDINARIA' ? 'selected' : '' }}>ORDINARIA</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nro_sesion"><strong>N° de Sesión</strong></label>
                                    <input type="number" name="nro_sesion" id="nro_sesion"
                                        class="form-control" min="1" value="{{ $sesion->nro_sesion }}" required>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h5><i class="fas fa-file-alt"></i> Documentos incluidos ({{ $sesion->proyectos->count() }})</h5>
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>N° Exp.</th>
                                    <th>Expediente</th>
                                    <th>Acapite</th>
                                    <th>Fecha Recepción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sesion->proyectos->sortBy('nro_expediente') as $p)
                                    @php
                                        $letra = '';
                                        if ($p->camara == 'Senado') $letra = 'S';
                                        elseif ($p->camara == 'Diputados') $letra = 'D';
                                        elseif ($p->camara == 'Congreso') $letra = 'C';
                                        $aa = substr($p->anho, -2);
                                        $expediente = $letra . '-' . $aa . $p->nro_expediente;

                                        $acapite = strip_tags($p->acapite ?? '');
                                        $acapite = html_entity_decode($acapite, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                        $acapite = preg_replace('/\s+/', ' ', trim($acapite));
                                        if (mb_strlen($acapite) > 80) {
                                            $acapite = mb_substr($acapite, 0, 80) . '...';
                                        }

                                        $fechaRec = $p->mesaEntrada ? $p->mesaEntrada->fecha_recepcion : '-';
                                    @endphp
                                    <tr>
                                        <td class="text-center"><strong>{{ $p->nro_expediente }}</strong></td>
                                        <td>{{ $expediente }}</td>
                                        <td>{{ $acapite }}</td>
                                        <td>{{ $fechaRec }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                            <h5 class="mb-2">
                                <i class="fas fa-plus-circle text-success"></i>
                                Agregar documentos disponibles
                            </h5>
                            <span class="badge badge-primary p-2">
                                Seleccionados: <span id="cantidadSeleccionados">0</span>
                            </span>
                        </div>

                        @if ($proyectosDisponibles->isEmpty())
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No hay documentos disponibles para agregar en este momento.
                            </div>
                        @else
                            <div class="row mb-2">
                                <div class="col-md-7">
                                    <input type="text" id="buscarDocumento" class="form-control"
                                        placeholder="Buscar por expediente, acápite, destino o fecha...">
                                </div>
                                <div class="col-md-5 d-flex align-items-center justify-content-end">
                                    <label class="mb-0">
                                        <input type="checkbox" id="seleccionarTodosDisponibles">
                                        <strong>Seleccionar todos los visibles</strong>
                                    </label>
                                </div>
                            </div>

                            <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                                <table class="table table-sm table-bordered table-hover">
                                    <thead class="thead-light" style="position: sticky; top: 0; z-index: 1;">
                                        <tr>
                                            <th style="width: 35px;"></th>
                                            <th>Expediente</th>
                                            <th>Acápite</th>
                                            <th>Destino</th>
                                            <th>Fecha de recepción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($proyectosDisponibles as $proyectoDisponible)
                                            @php
                                                $letraDisponible = '';
                                                if ($proyectoDisponible->camara == 'Senado') $letraDisponible = 'S';
                                                elseif ($proyectoDisponible->camara == 'Diputados') $letraDisponible = 'D';
                                                elseif ($proyectoDisponible->camara == 'Congreso') $letraDisponible = 'C';
                                                $expedienteDisponible = $letraDisponible . '-' . substr($proyectoDisponible->anho, -2) . $proyectoDisponible->nro_expediente;
                                                $acapiteDisponible = html_entity_decode(strip_tags($proyectoDisponible->acapite ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                                $acapiteDisponible = preg_replace('/\s+/', ' ', trim($acapiteDisponible));
                                                if (mb_strlen($acapiteDisponible) > 100) {
                                                    $acapiteDisponible = mb_substr($acapiteDisponible, 0, 100) . '...';
                                                }
                                                $fechaDisponible = optional($proyectoDisponible->mesaEntrada)->fecha_recepcion ?? '-';
                                            @endphp
                                            <tr class="documento-disponible-row">
                                                <td class="text-center">
                                                    <input type="checkbox" class="nuevo-documento-check"
                                                        name="nuevos_documentos[]" value="{{ $proyectoDisponible->id }}"
                                                        {{ in_array($proyectoDisponible->id, old('nuevos_documentos', [])) ? 'checked' : '' }}>
                                                </td>
                                                <td><strong>{{ $expedienteDisponible }}</strong></td>
                                                <td>{{ $acapiteDisponible }}</td>
                                                <td>{{ $proyectoDisponible->destino ?: '-' }}</td>
                                                <td>{{ $fechaDisponible }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <hr>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="{{ route('proyectos-en-estudio.asuntos-entrados') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
