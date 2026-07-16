@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1 class="m-0 custom-heading">Proyecto en Estudio N° 
                @php
                    $letra = '';
                    if ($proyecto->camara == 'Senado') $letra = 'S';
                    elseif ($proyecto->camara == 'Diputados') $letra = 'D';
                    elseif ($proyecto->camara == 'Congreso') $letra = 'C';
                    $aa = substr($proyecto->anho, -2);
                @endphp
                {{ $letra }}-{{ $aa }}{{ $proyecto->nro_expediente }}
            </h1>
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
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/jquery-ui-1.13.2/jquery-ui.min.css') }}">
    <style>
        .note-editor.note-frame .note-editing-area .note-editable {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
        }
        #tabla-tramites th,
        #tabla-tramites td,
        .obs-tramites-table th,
        .obs-tramites-table td {
            border: none !important;
            padding: 4px;
        }
        #tabla-tramites,
        .obs-tramites-table {
            border: 2px solid #adb5bd;
            border-collapse: collapse;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="{{ asset('vendor/jquery-ui-1.13.2/jquery-ui.min.js') }}"></script>
    <script>
        function insertarEnEditor(tipo) {
            var fn = function() {
                var html = '';
                switch (tipo) {
                    case 'observacion':
                        var texto = $('#insert_observacion').val();
                        if (texto) {
                            html = texto.replace(/\n/g, '<br>');
                        }
                        break;
                    case 'presentado_por':
                        var texto = $('#insert_presentado_por').val();
                        if (texto) {
                            html = '<em><u>PRESENTADO POR</u></em>: <strong>' + texto + '</strong>';
                        }
                        break;
                    case 'fecha':
                        var texto = $('#insert_fecha').val();
                        if (texto) {
                            html = '<em><u>FECHA DE RECEPCI\u00D3N</u></em>: <strong>' + texto.toUpperCase() + '</strong>';
                        }
                        break;
                    case 'firma':
                        var texto = $('#insert_firma').val();
                        if (texto) {
                            html = '<br><p style="text-align: center;">_____________________________<br><strong>' + texto + '</strong></p>';
                        }
                        break;
                    case 'expediente':
                        var texto = $('#insert_expediente').val();
                        if (texto) {
                            html = '<p style="text-align: right;"><strong>' + texto + '</strong></p>';
                        }
                        break;
                }
                if (html) {
                    $('#contenido').summernote('pasteHTML', html);
                }
            };
            if ($('#contenido').data('summernote')) {
                fn();
            } else {
                setTimeout(fn, 500);
            }
        }

        var obsCounter = 0;

        function getTramiteLabel(i) {
            if (i === 1) return '1er. Trámite';
            if (i === 2) return '2do. Trámite';
            if (i === 3) return '3er. Trámite';
            return i + '\u00B0 Trámite';
        }

        function buildObsTableHtml(count) {
            var html = '<table class="obs-tramites-table" border="0" cellpadding="4" cellspacing="0" style="width: 100%; margin: 10px 0; border-collapse: collapse;">';
            html += '<thead><tr>';
            html += '<th border="0" width="18%" style="padding: 4px;"></th>';
            html += '<th border="0" width="28%" style="padding: 4px;">FECHA DE SESI\u00D3N</th>';
            html += '<th border="0" width="22%" style="padding: 4px;">C\u00C1MARA</th>';
            html += '<th border="0" width="32%" style="padding: 4px;">RESULTADO</th>';
            html += '</tr></thead><tbody>';
            for (var i = 1; i <= count; i++) {
                var label = getTramiteLabel(i);
                html += '<tr>';
                html += '<td border="0" width="18%" style="padding: 4px;"><strong>' + label + '</strong></td>';
                html += '<td border="0" width="28%" style="padding: 4px;">__________</td>';
                html += '<td border="0" width="22%" style="padding: 4px;">__________</td>';
                html += '<td border="0" width="32%" style="padding: 4px;">__________</td>';
                html += '</tr>';
            }
            html += '</tbody></table>';
            return html;
        }

        function agregarTramite() {
            obsCounter++;
            var label = getTramiteLabel(obsCounter);
            var row = '<tr>';
            row += '<td border="0" width="18%" style="padding: 4px;"><strong>' + label + '</strong></td>';
            row += '<td border="0" width="28%" style="padding: 4px;">__________</td>';
            row += '<td border="0" width="22%" style="padding: 4px;">__________</td>';
            row += '<td border="0" width="32%" style="padding: 4px;">__________</td>';
            row += '</tr>';
            $('#tramites-body').append(row);
            $('#tabla-tramites').show();
            $('#sin-tramites').hide();
            var tableHtml = buildObsTableHtml(obsCounter);
            var $contenido = $('#contenido');
            if ($contenido.data('summernote')) {
                var content = $contenido.summernote('code');
                if (content.indexOf('obs-tramites-table') !== -1) {
                    content = content.replace(/<table[^>]*class="[^"]*obs-tramites-table[^"]*"[^>]*>[\s\S]*?<\/table>/i, tableHtml);
                    $contenido.summernote('code', content);
                } else {
                    $contenido.summernote('pasteHTML', tableHtml);
                }
            }
        }

        function formatearContenido() {
            var $editor = $('#contenido');
            if (!$editor.data('summernote')) return;
            var html = $editor.summernote('code');

            var tableHtml = '';
            html = html.replace(/<table[^>]*class="[^"]*obs-tramites-table[^"]*"[^>]*>[\s\S]*?<\/table>/i, function(m) {
                tableHtml = m;
                return '%%TABLE%%';
            });

            html = html.replace(/<\/?(?:em|u|strong|i|b)[^>]*>/gi, '');
            html = html.replace(/<\/p>/gi, '\n').replace(/<br\s*\/?>/gi, '\n');
            html = html.replace(/<[^>]+>/g, '');
            var text = $('<div>').html(html).text();

            function normalize(s) {
                return s.toUpperCase().replace(/Á/g, 'A').replace(/É/g, 'E').replace(/Í/g, 'I').replace(/Ó/g, 'O').replace(/Ú/g, 'U').replace(/Ü/g, 'U').replace(/Ñ/g, 'N');
            }
            var labels = ['FECHA DE RECEPCION', 'PRESENTADO POR', 'OBSERVACION', 'EXPEDIENTE', 'FIRMA', 'NRO EXPEDIENTE'];
            var lines = text.split('\n');
            var result = [];
            for (var i = 0; i < lines.length; i++) {
                var line = lines[i].trim();
                if (!line) continue;
                var idx = line.indexOf(':');
                if (idx > 0) {
                    var before = line.substring(0, idx).trim();
                    var after = line.substring(idx + 1).trim();
                    if (labels.indexOf(normalize(after)) !== -1) {
                        var tmp = before;
                        before = after;
                        after = tmp;
                    }
                    result.push('<p><em><u>' + before.toUpperCase() + '</u></em>: <strong>' + after + '</strong></p>');
                } else {
                    result.push('<p>' + line + '</p>');
                }
            }

            var output = result.join('\n');
            if (tableHtml) {
                output = output.replace('%%TABLE%%', tableHtml);
            }

            $editor.summernote('code', output);
        }

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

            $('form:first').on('submit', function() {
                if ($('#contenido').data('summernote')) {
                    $('#contenido').val($('#contenido').summernote('code'));
                }
                if ($('#acapite').data('summernote')) {
                    $('#acapite').val($('#acapite').summernote('code'));
                }
            });

            $('#insert_presentado_por').on('input', function() {
                $('input[name="presentado_por"]').val($(this).val());
            });
            $('#insert_fecha').on('input', function() {
                $('input[name="fecha_recepcion_texto"]').val($(this).val());
            });

            $('#contenido').summernote({
                height: 500,
                toolbar: [
                    ['font', ['bold', 'underline', 'italic', 'clear']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                fontNames: ['Times New Roman', 'Arial', 'Courier New', 'Georgia', 'Verdana'],
                fontNamesIgnoreCheck: ['Times New Roman'],
                defaultFontName: 'Times New Roman',
            });

            $('#acapite').summernote({
                height: 120,
                toolbar: [
                    ['font', ['bold', 'underline', 'italic', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                ],
                fontNames: ['Times New Roman', 'Arial'],
                fontNamesIgnoreCheck: ['Times New Roman'],
                defaultFontName: 'Times New Roman',
                placeholder: 'Escriba el acapite con formato...',
            });

            $(document).on('keydown', function(e) {
                if (!e.ctrlKey || !e.altKey) return;
                switch (e.key.toLowerCase()) {
                    case 'o': e.preventDefault(); insertarEnEditor('observacion'); break;
                    case 'p': e.preventDefault(); insertarEnEditor('presentado_por'); break;
                    case 'f': e.preventDefault(); insertarEnEditor('fecha'); break;
                    case 's': e.preventDefault(); insertarEnEditor('firma'); break;
                    case 'e': e.preventDefault(); insertarEnEditor('expediente'); break;
                    case 't': e.preventDefault(); agregarTramite(); break;
                }
            });

            $('#destino').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{ route('obtenerdestino') }}",
                        dataType: "json",
                        data: { term: request.term },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                minLength: 1,
                autoFocus: true,
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
                        <i class="fas fa-file-alt"></i>
                        @if ($proyecto->nro_expediente > 0)
                            @php
                                $letra = '';
                                if ($proyecto->camara == 'Senado') $letra = 'S';
                                elseif ($proyecto->camara == 'Diputados') $letra = 'D';
                                elseif ($proyecto->camara == 'Congreso') $letra = 'C';
                                $aa = substr($proyecto->anho, -2);
                            @endphp
                            Expediente N° {{ $letra }}-{{ $aa }}{{ $proyecto->nro_expediente }}
                        @else
                            <span class="text-warning"><em>Pendiente</em></span>
                        @endif
                    </h3>
                    <div class="card-tools">
                        <small class="text-muted">
                            Documento de recepción N° {{ $proyecto->mesaEntrada->nro_mentrada }}/{{ $proyecto->mesaEntrada->anho }}
                        </small>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('proyectos-en-estudio.update', $proyecto->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="camara">Cámara de Origen</label>
                                    <select name="camara" id="camara" class="form-control">
                                        <option value="">Seleccionar...</option>
                                        <option value="Senado" {{ $proyecto->camara == 'Senado' ? 'selected' : '' }}>Senado</option>
                                        <option value="Diputados" {{ $proyecto->camara == 'Diputados' ? 'selected' : '' }}>Diputados</option>
                                        <option value="Congreso" {{ $proyecto->camara == 'Congreso' ? 'selected' : '' }}>Congreso</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="acapite">Acapite</label>
                                    <textarea name="acapite" id="acapite" class="form-control">{{ old('acapite', $proyecto->acapite) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="destino">Destino</label>
                                    <input type="text" name="destino" id="destino" class="form-control autocomplete-field"
                                        value="{{ old('destino', $proyecto->destino) }}"
                                        placeholder="Escriba o seleccione..." autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="presentado_por" value="{{ old('presentado_por', $proyecto->presentado_por) }}">
                        <input type="hidden" name="fecha_recepcion_texto" value="{{ old('fecha_recepcion_texto', $proyecto->fecha_recepcion_texto) }}">
                        <input type="hidden" name="cantidad_observaciones" value="{{ old('cantidad_observaciones', $proyecto->cantidad_observaciones) }}">

                        <hr>
                        <h5><i class="fas fa-tools"></i> Composición del Documento</h5>
                        <p class="text-muted">Use los botones para insertar cada elemento en la posición del cursor dentro del editor.</p>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-outline card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">Observación
                                            <small class="badge badge-light text-muted font-weight-normal" style="font-size: 10px; margin-left: 4px;">Ctrl+Alt+O</small>
                                        </h3>
                                        <button type="button" class="btn btn-sm btn-info float-right" onclick="insertarEnEditor('observacion')">
                                            <i class="fas fa-plus"></i> Agregar al documento
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <textarea id="insert_observacion" class="form-control" rows="3">{{ $proyecto->mesaEntrada->observacion ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-outline card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">Presentado Por
                                            <small class="badge badge-light text-muted font-weight-normal" style="font-size: 10px; margin-left: 4px;">Ctrl+Alt+P</small>
                                        </h3>
                                        <button type="button" class="btn btn-sm btn-success float-right" onclick="insertarEnEditor('presentado_por')">
                                            <i class="fas fa-plus"></i> Agregar
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <input type="text" id="insert_presentado_por" class="form-control"
                                            value="{{ old('presentado_por', $proyecto->presentado_por) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-outline card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">Fecha Recepción
                                            <small class="badge badge-light text-muted font-weight-normal" style="font-size: 10px; margin-left: 4px;">Ctrl+Alt+F</small>
                                        </h3>
                                        <button type="button" class="btn btn-sm btn-warning float-right" onclick="insertarEnEditor('fecha')">
                                            <i class="fas fa-plus"></i> Agregar
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <input type="text" id="insert_fecha" class="form-control"
                                            value="{{ old('fecha_recepcion_texto', $proyecto->fecha_recepcion_texto) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card card-outline card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">Firma
                                            <small class="badge badge-light text-muted font-weight-normal" style="font-size: 10px; margin-left: 4px;">Ctrl+Alt+S</small>
                                        </h3>
                                        <button type="button" class="btn btn-sm btn-secondary float-right" onclick="insertarEnEditor('firma')">
                                            <i class="fas fa-plus"></i> Agregar
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <input type="text" id="insert_firma" class="form-control" value="SECRETARIO GENERAL">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-outline card-dark">
                                    <div class="card-header">
                                        <h3 class="card-title">Trámites
                                            <small class="badge badge-light text-muted font-weight-normal" style="font-size: 10px; margin-left: 4px;">Ctrl+Alt+T</small>
                                        </h3>
                                        <button type="button" class="btn btn-sm btn-dark float-right" onclick="agregarTramite()">
                                            <i class="fas fa-list"></i> Obs
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <p class="text-muted mb-0" id="sin-tramites">Sin trámites. Click en "Obs".</p>
                                        <table id="tabla-tramites" class="table table-sm mb-0" border="0" cellpadding="4" cellspacing="0" style="display: none; border-collapse: collapse;">
                                            <thead>
                                                <tr>
                                                    <th border="0" width="18%" style="padding: 4px;"></th>
                                                    <th border="0" width="28%" style="padding: 4px;">FECHA DE SESIÓN</th>
                                                    <th border="0" width="22%" style="padding: 4px;">CÁMARA</th>
                                                    <th border="0" width="32%" style="padding: 4px;">RESULTADO</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tramites-body"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @if ($proyecto->nro_expediente > 0)
                                <div class="col-md-4">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                        <h3 class="card-title">N° Expediente
                                            <small class="badge badge-light text-muted font-weight-normal" style="font-size: 10px; margin-left: 4px;">Ctrl+Alt+E</small>
                                        </h3>
                                        <button type="button" class="btn btn-sm btn-primary float-right" onclick="insertarEnEditor('expediente')">
                                                <i class="fas fa-plus"></i> Agregar
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $letra = '';
                                                if ($proyecto->camara == 'Senado') $letra = 'S';
                                                elseif ($proyecto->camara == 'Diputados') $letra = 'D';
                                                elseif ($proyecto->camara == 'Congreso') $letra = 'C';
                                                $aa = substr($proyecto->anho, -2);
                                            @endphp
                                            <input type="text" id="insert_expediente" class="form-control"
                                                value="EXP No {{ $letra }} - {{ $aa }}{{ $proyecto->nro_expediente }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <hr>

                        <div class="form-group">
                            <label for="contenido">Contenido del Documento</label>
                            <button type="button" class="btn btn-sm btn-outline-info float-right" onclick="formatearContenido()" title="Aplica: cursiva+subrayado a lo que está antes de «:» y negrita a lo que está después">
                                <i class="fas fa-magic"></i> Formatear
                            </button>
                            <textarea name="contenido" id="contenido" class="form-control">{{ old('contenido', $proyecto->contenido) }}</textarea>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>

                                @if ($proyecto->nro_expediente > 0)
                                    <a href="{{ route('proyectos-en-estudio.word', $proyecto->id) }}" class="btn btn-success btn-generar-doc">
                                        <i class="fas fa-file-word"></i> Descargar Word
                                    </a>
                                    <a href="{{ route('proyectos-en-estudio.pdf', $proyecto->id) }}" class="btn btn-danger btn-generar-doc">
                                        <i class="fas fa-file-pdf"></i> Descargar PDF
                                    </a>
                                @endif

                                <a href="{{ route('proyectos-en-estudio.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>

                    <form action="{{ route('proyectos-en-estudio.destroy', $proyecto->id) }}" method="POST"
                        class="delete-form mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger delete-button">
                            <i class="fas fa-trash"></i> Eliminar Proyecto
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        $(document).ready(function() {
            $(document).on('click', '.btn-generar-doc', function() {
                Swal.fire({
                    title: 'Generando documento...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: function() { Swal.showLoading(); }
                });
                var timer = setInterval(function() {
                    if (document.hasFocus()) {
                        clearInterval(timer);
                        Swal.close();
                    }
                }, 500);
            });

            $('.delete-button').on('click', function() {
                var form = $(this).closest('.delete-form');
                Swal.fire({
                    title: 'Confirmar eliminación',
                    text: '¿Estás seguro de que deseas eliminar este proyecto?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
