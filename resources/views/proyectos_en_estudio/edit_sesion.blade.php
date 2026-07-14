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
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Datos de la Sesión
                    </h3>
                </div>
                <div class="card-body">
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
