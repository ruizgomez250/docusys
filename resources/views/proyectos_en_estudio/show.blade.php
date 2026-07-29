@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1 class="m-0 custom-heading">Proyecto en Estudio N° 
                @php
                    if ($proyecto->usar_documento_padre && $proyecto->documentoPadre) {
                        $expediente = $proyecto->documentoPadre;
                    } else {
                        $expediente = $proyecto;
                    }
                    $letra = '';
                    if ($expediente->camara == 'Senado') $letra = 'S';
                    elseif ($expediente->camara == 'Diputados') $letra = 'D';
                    elseif ($expediente->camara == 'Congreso') $letra = 'C';
                    $aa = substr($expediente->anho, -2);
                @endphp
                {{ $letra }}-{{ $aa }}{{ $expediente->nro_expediente }}
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
@section('plugins.Select2', true)

@php
    $puedeCabecera = Auth::user()->can('Editar Cabecera Proyectos');
    $puedeContenido = Auth::user()->can('Editar Contenido Proyectos');
@endphp

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
                    case 'documento_padre':
                        var texto = $('#documento_padre_formato').val();
                        if (texto) {
                            html = '<strong>' + texto + '</strong>';
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

            function normalize(s) {
                return s.toUpperCase().replace(/Á/g, 'A').replace(/É/g, 'E').replace(/Í/g, 'I').replace(/Ó/g, 'O').replace(/Ú/g, 'U').replace(/Ü/g, 'U').replace(/Ñ/g, 'N');
            }
            var labels = ['FECHA DE RECEPCION', 'PRESENTADO POR', 'OBSERVACION', 'EXPEDIENTE', 'FIRMA', 'NRO EXPEDIENTE'];

            function formatLine(line) {
                line = line.trim();
                if (!line) return '';
                var idx = line.indexOf(':');
                if (idx > 0) {
                    var before = line.substring(0, idx).trim();
                    var after = line.substring(idx + 1).trim();
                    if (labels.indexOf(normalize(after)) !== -1) {
                        var tmp = before;
                        before = after;
                        after = tmp;
                    }
                    return '<p><em><u>' + before.toUpperCase() + '</u></em>: <strong>' + after + '</strong></p>';
                }
                return '<p>' + line + '</p>';
            }

            function stripTags(html) {
                var t = document.createElement('div');
                t.innerHTML = html;
                return t.textContent || t.innerText || '';
            }

            function formatHtml(html) {
                var tableHtml = '';
                html = html.replace(/<table[^>]*class="[^"]*obs-tramites-table[^"]*"[^>]*>[\s\S]*?<\/table>/i, function(m) {
                    tableHtml = m;
                    return '%%TABLE%%';
                });
                html = html.replace(/<\/?(?:em|u|strong|i|b)[^>]*>/gi, '');
                html = html.replace(/<\/p>/gi, '\n').replace(/<br\s*\/?>/gi, '\n');
                html = html.replace(/<[^>]+>/g, '');
                var text = stripTags(html);
                var lines = text.split('\n');
                var result = [];
                for (var i = 0; i < lines.length; i++) {
                    var f = formatLine(lines[i]);
                    if (f) result.push(f);
                }
                var output = result.join('\n');
                if (tableHtml) {
                    output = output.replace('%%TABLE%%', tableHtml);
                }
                return output;
            }

            var sel = window.getSelection();
            if (sel && sel.rangeCount > 0 && !sel.isCollapsed && $editor.find('.note-editable').has(sel.anchorNode).length > 0) {
                var range = sel.getRangeAt(0);
                var selectedText = sel.toString();
                if (!selectedText.trim()) return;

                var lines = selectedText.split('\n');
                var result = [];
                for (var i = 0; i < lines.length; i++) {
                    var f = formatLine(lines[i]);
                    if (f) result.push(f);
                }
                var formatted = result.join('');

                range.deleteContents();
                var temp = document.createElement('div');
                temp.innerHTML = formatted;
                var frag = document.createDocumentFragment();
                while (temp.firstChild) frag.appendChild(temp.firstChild);
                range.insertNode(frag);

                sel.removeAllRanges();
                $editor.summernote('triggerSave');
            } else {
                var html = $editor.summernote('code');
                var formatted = formatHtml(html);
                $editor.summernote('code', formatted);
            }
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

            @if ($puedeContenido)
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
            @endif

            @if ($puedeCabecera)
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
            @endif

            $(document).on('keydown', function(e) {
                if (!e.ctrlKey || !e.altKey) return;
                switch (e.key.toLowerCase()) {
                    case 'o': e.preventDefault(); insertarEnEditor('observacion'); break;
                    case 'p': e.preventDefault(); insertarEnEditor('presentado_por'); break;
                    case 'f': e.preventDefault(); insertarEnEditor('fecha'); break;
                    case 's': e.preventDefault(); insertarEnEditor('firma'); break;
                    case 't': e.preventDefault(); agregarTramite(); break;
                    case 'd': e.preventDefault(); if ($('#usar_documento_padre').is(':checked')) { abrirModalDocumentoPadre(); } break;
                }
            });

            @if ($puedeCabecera)
            @if ($proyecto->destino)
            var option = new Option('{{ $proyecto->destino }}', '{{ $proyecto->destino }}', true, true);
            $('#destino').append(option).trigger('change');
            @endif
            @endif

            $('#usar_documento_padre').on('change', function() {
                var checked = $(this).is(':checked');
                $('#btnDocumentoPadre').prop('disabled', !checked);
                if (!checked) {
                    $('#documento_padre_id').val('');
                    $('#documento_padre_formato').val('');
                }
            }).trigger('change');

            var tablaDocumentoPadre = null;

            window.abrirModalDocumentoPadre = function() {
                if (!$('#usar_documento_padre').is(':checked')) return;
                $('#modalDocumentoPadre').modal('show');
                $('#documento-padre-seleccionado').hide();
                $('#btnConfirmarDocumentoPadre').prop('disabled', true);

                if (tablaDocumentoPadre) {
                    tablaDocumentoPadre.ajax.reload();
                } else {
                    tablaDocumentoPadre = $('#tabla-documento-padre').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: '{{ route("proyectos-en-estudio.listado-data-modal") }}',
                            data: function(d) {
                                d.exclude_id = '{{ $proyecto->id }}';
                            }
                        },
                        columns: [
                            { data: 'expediente_formato', title: 'Expediente' },
                            { data: 'presentado_por', name: 'presentado_por', title: 'Presentado Por' },
                            { data: 'fecha_recepcion_texto', name: 'fecha_recepcion_texto', title: 'Fecha Recepción' },
                            { data: 'camara', name: 'camara', title: 'Cámara' },
                            { data: 'acciones', title: 'Acciones', orderable: false, searchable: false }
                        ],
                        language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
                        order: [[0, 'desc']]
                    });
                }
            };

            $(document).on('click', '.btn-seleccionar-padre', function() {
                $('.btn-seleccionar-padre').removeClass('btn-success').addClass('btn-primary').html('<i class="fas fa-check"></i> Seleccionar');
                $(this).removeClass('btn-primary').addClass('btn-success').html('<i class="fas fa-check-double"></i> Seleccionado');
                var expediente = $(this).data('expediente');
                var id = $(this).data('id');
                $('#documento-padre-seleccionado').show();
                $('#documento-padre-texto').text(expediente);
                $('#btnConfirmarDocumentoPadre').prop('disabled', false).data('id', id).data('expediente', expediente);
            });

            $('#btnConfirmarDocumentoPadre').on('click', function() {
                var id = $(this).data('id');
                var expediente = $(this).data('expediente');
                $('#documento_padre_id').val(id);
                $('#documento_padre_formato').val(expediente);
                $('#modalDocumentoPadre').modal('hide');
                insertarEnEditor('documento_padre');
                Swal.fire({
                    icon: 'success',
                    title: 'Documento Padre asignado',
                    text: expediente + ' ha sido seleccionado.',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        });

        function abrirModalDestino() {
            $('#nuevo_destino').val('');
            $('#modalDestino').modal('show');
            setTimeout(function() { $('#nuevo_destino').focus(); }, 300);
        }

        $(document).on('click', '#btnGuardarDestino', function() {
            var nombre = $('#nuevo_destino').val().trim();
            if (!nombre) {
                Swal.fire('Atención', 'Debe escribir el nombre del destino.', 'warning');
                return;
            }

            $.ajax({
                url: "{{ route('destino.store-ajax') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    nombre: nombre
                },
                success: function(data) {
                    var newOption = new Option(data.nombre, data.nombre, true, true);
                    $('#destino').append(newOption).trigger('change');
                    $('#modalDestino').modal('hide');
                    Swal.fire('Éxito', 'Destino agregado correctamente.', 'success');
                },
                error: function(xhr) {
                    var msg = 'Error al guardar.';
                    if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors.nombre) {
                        msg = xhr.responseJSON.errors.nombre[0];
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        $('#nuevo_destino').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#btnGuardarDestino').click();
            }
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
                        @php
                            if ($proyecto->usar_documento_padre && $proyecto->documentoPadre) {
                                $expediente = $proyecto->documentoPadre;
                            } else {
                                $expediente = $proyecto;
                            }
                        @endphp
                        @if ($expediente->nro_expediente > 0)
                            @php
                                $letra = '';
                                if ($expediente->camara == 'Senado') $letra = 'S';
                                elseif ($expediente->camara == 'Diputados') $letra = 'D';
                                elseif ($expediente->camara == 'Congreso') $letra = 'C';
                                $aa = substr($expediente->anho, -2);
                            @endphp
                            Expediente N° {{ $letra }}-{{ $aa }}{{ $expediente->nro_expediente }}
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="camara">Cámara de Origen</label>
                                    <select name="camara" id="camara" class="form-control" {{ !$puedeCabecera ? 'disabled' : '' }}>
                                        <option value="">Seleccionar...</option>
                                        <option value="Senado" {{ $proyecto->camara == 'Senado' ? 'selected' : '' }}>Senado</option>
                                        <option value="Diputados" {{ $proyecto->camara == 'Diputados' ? 'selected' : '' }}>Diputados</option>
                                        <option value="Congreso" {{ $proyecto->camara == 'Congreso' ? 'selected' : '' }}>Congreso</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="destino">Destino
                                        @if ($puedeCabecera)
                                        <a href="javascript:void(0)" onclick="abrirModalDestino()" title="Agregar nuevo destino"
                                            style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:50%; border:1px solid #6c757d; color:#6c757d; font-size:14px; font-weight:bold; margin-left:6px; vertical-align:middle; line-height:1; cursor:pointer; transition: all 0.2s;"
                                            onmouseover="this.style.backgroundColor='#007bff'; this.style.borderColor='#007bff'; this.style.color='#fff';"
                                            onmouseout="this.style.backgroundColor=''; this.style.borderColor='#6c757d'; this.style.color='#6c757d';">
                                            +
                                        </a>
                                        @endif
                                    </label>
                                    <x-adminlte-select2 name="destino" id="destino"
                                        fgroup-class="col-md-12" :disabled="!$puedeCabecera">
                                        <option value="">Seleccionar...</option>
                                        @foreach ($destinos as $dest)
                                            <option value="{{ $dest->nombre }}" {{ $proyecto->destino == $dest->nombre ? 'selected' : '' }}>{{ $dest->nombre }}</option>
                                        @endforeach
                                    </x-adminlte-select2>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="acapite">Acapite</label>
                                    @if ($puedeCabecera)
                                    <textarea name="acapite" id="acapite" class="form-control">{{ old('acapite', $proyecto->acapite) }}</textarea>
                                    @else
                                        <div class="form-control" style="min-height:120px; background-color:#e9ecef; cursor:default; overflow:auto;">{!! $proyecto->acapite !!}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="presentado_por" value="{{ old('presentado_por', $proyecto->presentado_por) }}">
                        <input type="hidden" name="fecha_recepcion_texto" value="{{ old('fecha_recepcion_texto', $proyecto->fecha_recepcion_texto) }}">
                        <input type="hidden" name="cantidad_observaciones" value="{{ old('cantidad_observaciones', $proyecto->cantidad_observaciones) }}">
                        <input type="hidden" name="documento_padre_id" id="documento_padre_id" value="{{ old('documento_padre_id', $proyecto->documento_padre_id) }}">

                        <hr>

                        <div id="campos-ocultos" style="display:none;">
                            <textarea id="insert_observacion">{{ $proyecto->mesaEntrada->observacion ?? '' }}</textarea>
                            <input type="text" id="insert_presentado_por" value="{{ old('presentado_por', $proyecto->presentado_por) }}">
                            <input type="text" id="insert_fecha" value="{{ old('fecha_recepcion_texto', $proyecto->fecha_recepcion_texto) }}">
                            <input type="text" id="insert_firma" value="SECRETARIO GENERAL">
                            @if ($proyecto->nro_expediente > 0)
                                @php
                                    $letra = '';
                                    if ($proyecto->camara == 'Senado') $letra = 'S';
                                    elseif ($proyecto->camara == 'Diputados') $letra = 'D';
                                    elseif ($proyecto->camara == 'Congreso') $letra = 'C';
                                    $aa = substr($proyecto->anho, -2);
                                @endphp
                                <input type="text" id="insert_expediente" value="EXP No {{ $letra }} - {{ $aa }}{{ $proyecto->nro_expediente }}">
                            @endif
                            <input type="text" id="documento_padre_formato" value="@if($proyecto->documento_padre_id && $proyecto->documentoPadre)({{ 'Exp. No. ' . (($proyecto->documentoPadre->camara == 'Senado') ? 'S' : (($proyecto->documentoPadre->camara == 'Diputados') ? 'D' : 'C')) . '-' . substr($proyecto->documentoPadre->anho, -2) . $proyecto->documentoPadre->nro_expediente }})@endif">
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

                        <div class="form-group">
                            <label for="contenido">Contenido del Documento</label>
                            @if ($puedeContenido)
                            <div style="float: right; display: flex; align-items: center; gap: 4px; flex-wrap: wrap; justify-content: flex-end;">
                                <div style="display: inline-flex; align-items: center; gap: 3px; background: #dc3545; border-radius: 4px; padding: 2px 6px;">
                                    <input type="checkbox" id="usar_documento_padre" name="usar_documento_padre" value="1" {{ old('usar_documento_padre', $proyecto->usar_documento_padre) ? 'checked' : '' }} style="margin: 0; cursor: pointer;">
                                    <label for="usar_documento_padre" style="color: #fff; font-size: 0.8rem; margin: 0; cursor: pointer; white-space: nowrap;">Doc. Padre</label>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="btnDocumentoPadre" onclick="abrirModalDocumentoPadre()" title="Documento Padre" disabled>
                                    <i class="fas fa-sitemap"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info" onclick="formatearContenido()" title="Formatear selección o todo el contenido">
                                    <i class="fas fa-magic"></i> Formatear
                                </button>
                                <button type="button" class="btn btn-sm btn-info" onclick="insertarEnEditor('observacion')" title="Observación (Ctrl+Alt+O)">
                                    <i class="fas fa-sticky-note"></i> Obs <small class="text-white">Ctrl+Alt+O</small>
                                </button>
                                <button type="button" class="btn btn-sm btn-success" onclick="insertarEnEditor('presentado_por')" title="Presentado Por (Ctrl+Alt+P)">
                                    <i class="fas fa-user"></i> Presentado por <small class="text-white">Ctrl+Alt+P</small>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" onclick="insertarEnEditor('fecha')" title="Fecha de Recepción (Ctrl+Alt+F)">
                                    <i class="fas fa-calendar"></i> Fecha <small class="text-white">Ctrl+Alt+F</small>
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="insertarEnEditor('firma')" title="Firma (Ctrl+Alt+S)">
                                    <i class="fas fa-signature"></i> Firma <small class="text-muted">Ctrl+Alt+S</small>
                                </button>
                                <button type="button" class="btn btn-sm btn-dark" onclick="agregarTramite()" title="Trámites (Ctrl+Alt+T)">
                                    <i class="fas fa-list"></i> Trámites <small class="text-white">Ctrl+Alt+T</small>
                                </button>
                            </div>
                            <div style="clear: both;"></div>
                            @endif
                            @if ($puedeContenido)
                            <textarea name="contenido" id="contenido" class="form-control">{{ old('contenido', $proyecto->contenido) }}</textarea>
                            @else
                                <div class="form-control" style="min-height:500px; background-color:#e9ecef; cursor:default; overflow:auto;">{!! $proyecto->contenido !!}</div>
                            @endif
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-12">
                                @if ($puedeCabecera || $puedeContenido)
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                                @endif

                                @if ($proyecto->nro_expediente > 0)
                                    @if ($proyecto->usar_documento_padre && $proyecto->documentoPadre)
                                    <a href="{{ route('proyectos-en-estudio.word-padre', $proyecto->id) }}" class="btn btn-success btn-generar-doc">
                                        <i class="fas fa-file-word"></i> Word (Doc. Padre)
                                    </a>
                                    <a href="{{ route('proyectos-en-estudio.pdf-padre', $proyecto->id) }}" class="btn btn-danger btn-generar-doc" target="_blank">
                                        <i class="fas fa-file-pdf"></i> PDF (Doc. Padre)
                                    </a>
                                    @endif
                                    <a href="{{ route('proyectos-en-estudio.word', $proyecto->id) }}" class="btn btn-success btn-generar-doc">
                                        <i class="fas fa-file-word"></i> Descargar Word
                                    </a>
                                    <a href="{{ route('proyectos-en-estudio.pdf', $proyecto->id) }}" class="btn btn-danger btn-generar-doc" target="_blank">
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

    @if ($puedeCabecera)
    <div class="modal fade" id="modalDestino" tabindex="-1" role="dialog" aria-labelledby="modalDestinoLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDestinoLabel">Agregar Nuevo Destino</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nuevo_destino">Nombre del Destino</label>
                        <input type="text" class="form-control" id="nuevo_destino" placeholder="Escriba el nombre del destino...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarDestino">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if ($puedeContenido)
    <div class="modal fade" id="modalDocumentoPadre" tabindex="-1" role="dialog" aria-labelledby="modalDocumentoPadreLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalDocumentoPadreLabel"><i class="fas fa-sitemap"></i> Seleccionar Documento Padre</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="documento-padre-seleccionado" style="display:none;" class="alert alert-success">
                        <strong>Seleccionado:</strong> <span id="documento-padre-texto"></span>
                    </div>
                    <table id="tabla-documento-padre" class="table table-bordered table-striped table-sm" style="width:100%">
                        <thead>
                            <tr>
                                <th>Expediente</th>
                                <th>Presentado Por</th>
                                <th>Fecha Recepción</th>
                                <th>Cámara</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btnConfirmarDocumentoPadre" disabled>
                        <i class="fas fa-check"></i> Confirmar Selección
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
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
