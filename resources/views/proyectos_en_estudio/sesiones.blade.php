@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1 class="m-0 custom-heading">Generar PDF de Sesión</h1>
        </div>
        <div class="col-6">
            <a href="{{ route('proyectos-en-estudio.index') }}" class="btn btn-secondary" style="float: right;">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
@stop

@section('plugins.Sweetalert2', true)

@push('css')
    <style>
        .fecha-group { background: #f8f9fa; }
        .fecha-group td { border-top: 2px solid #dee2e6 !important; }
        .check-all { cursor: pointer; }
    </style>
@endpush

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

            $('#filterFecha').on('change', function() {
                var filtro = $(this).val();
                if (filtro === '') {
                    $('tr.doc-row').show();
                } else {
                    $('tr.doc-row').each(function() {
                        if ($(this).data('fecha') === filtro) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
                updateCount();
            });

            $('#checkAll').on('change', function() {
                var visible = $('tr.doc-row:visible');
                visible.find('.doc-check').prop('checked', this.checked);
                updateCount();
            });

            $(document).on('change', '.doc-check', function() {
                updateCount();
            });

            function updateCount() {
                var total = $('tr.doc-row:visible .doc-check:checked').length;
                $('#selectedCount').text(total);
                $('#btnGenerar').prop('disabled', total === 0);
            }

            $('#btnGenerar').on('click', function() {
                var selected = [];
                $('tr.doc-row .doc-check:checked').each(function() {
                    selected.push($(this).val());
                });
                if (selected.length === 0) {
                    Swal.fire('Atención', 'Debe seleccionar al menos un documento.', 'warning');
                    return;
                }
                $('#docIds').val(selected.join(','));

                var primeraFecha = $('tr.doc-row .doc-check:checked').first().closest('tr').data('fecha');
                if (primeraFecha) {
                    $('#modal_fecha_sesion').val(primeraFecha);
                }

                $('#modalSesion').modal('show');
            });

            $('#formSesion').on('submit', function(e) {
                e.preventDefault();
                var tipo = $('#modal_tipo_sesion').val();
                var nro = $('#modal_nro_sesion').val();
                var fecha = $('#modal_fecha_sesion').val();
                var ids = $('#docIds').val();
                if (!tipo || !nro || !fecha) {
                    Swal.fire('Atención', 'Complete todos los campos.', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Generando PDF...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: function() { Swal.showLoading(); }
                });

                $.ajax({
                    url: '{{ route("proyectos-en-estudio.sesiones.generar-pdf") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        doc_ids: ids,
                        tipo_sesion: tipo,
                        nro_sesion: nro,
                        fecha_sesion: fecha
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(data) {
                        Swal.close();
                        var blob = new Blob([data], { type: 'application/pdf' });
                        var url = window.URL.createObjectURL(blob);
                        var a = document.createElement('a');
                        a.href = url;
                        a.download = 'sesion_' + tipo.toLowerCase() + '_' + nro + '.pdf';
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        a.remove();
                        window.location.href = '{{ route("proyectos-en-estudio.asuntos-entrados") }}';
                    },
                    error: function() {
                        Swal.fire('Error', 'No se pudo generar el PDF.', 'error');
                    }
                });
            });
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-pdf"></i> Documentos con N° de Expediente
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-danger" id="btnGenerar" disabled>
                            <i class="fas fa-file-pdf"></i> Generar PDF de Sesión (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="filterFecha"><strong>Filtrar por fecha de recepción:</strong></label>
                            <select id="filterFecha" class="form-control">
                                <option value="">Todas las fechas</option>
                                @foreach($fechas as $f)
                                    <option value="{{ $f->fecha_recepcion }}">
                                        {{ strtoupper($f->fecha_texto) }} ({{ $f->cantidad }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8 d-flex align-items-end">
                            <label class="ml-auto">
                                <input type="checkbox" id="checkAll" class="check-all">
                                <strong>Seleccionar/Deseleccionar todos (visibles)</strong>
                            </label>
                        </div>
                    </div>

                    <table class="table table-bordered table-hover table-sm">
                        <thead>
                            <tr>
                                <th style="width: 30px">
                                </th>
                                <th>Expediente</th>
                                <th>Acapite</th>
                                <th>Destino</th>
                                <th>Fecha Recepción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proyectos as $proyecto)
                                @php
                                    $letra = '';
                                    if ($proyecto->camara == 'Senado') $letra = 'S';
                                    elseif ($proyecto->camara == 'Diputados') $letra = 'D';
                                    elseif ($proyecto->camara == 'Congreso') $letra = 'C';
                                    $aa = substr($proyecto->anho, -2);
                                    $expediente = $letra . '-' . $aa . $proyecto->nro_expediente;

                                    $acapite = strip_tags($proyecto->acapite ?? '');
                                    $acapite = html_entity_decode($acapite, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                                    $acapite = preg_replace('/\s+/', ' ', trim($acapite));
                                    if (mb_strlen($acapite) > 120) {
                                        $acapite = mb_substr($acapite, 0, 120) . '...';
                                    }

                                    $carbon = \Carbon\Carbon::parse($proyecto->mesaEntrada->fecha_recepcion);
                                    $dias = ['DOM', 'LUN', 'MAR', 'MIE', 'JUE', 'VIE', 'SAB'];
                                    $meses = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];
                                    $fechaCorta = $dias[$carbon->dayOfWeek] . ' ' . $carbon->format('d') . '/' . $meses[$carbon->month - 1] . '/' . $carbon->format('Y');
                                @endphp
                                <tr class="doc-row" data-fecha="{{ $proyecto->mesaEntrada->fecha_recepcion }}">
                                    <td>
                                        <input type="checkbox" class="doc-check" value="{{ $proyecto->id }}">
                                    </td>
                                    <td><strong>{{ $expediente }}</strong></td>
                                    <td>{{ $acapite }}</td>
                                    <td>{{ $proyecto->destino ?? '-' }}</td>
                                    <td>{{ $fechaCorta }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSesion" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><i class="fas fa-gavel"></i> Configurar Sesión</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form id="formSesion">
                    <div class="modal-body">
                        <input type="hidden" id="docIds" name="doc_ids">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="modal_fecha_sesion"><strong>Fecha de Sesión</strong></label>
                                    <input type="date" id="modal_fecha_sesion" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modal_tipo_sesion"><strong>Tipo de Sesión</strong></label>
                                    <select id="modal_tipo_sesion" class="form-control" required>
                                        <option value="EXTRAORDINARIA">EXTRAORDINARIA</option>
                                        <option value="ORDINARIA">ORDINARIA</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modal_nro_sesion"><strong>N° de Sesión</strong></label>
                                    <input type="number" id="modal_nro_sesion" class="form-control" min="1" value="1" required>
                                    <small class="text-muted" id="nroSesionHint"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-file-pdf"></i> Generar PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
