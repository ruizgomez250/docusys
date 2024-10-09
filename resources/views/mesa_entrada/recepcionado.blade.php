@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1 class="m-0 custom-heading">Lista de Documentos</h1>
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

            // Agregar confirmación de eliminación
            $('.delete-button').on('click', function() {
                var form = $(this).closest('.delete-form');
                Swal.fire({
                    title: 'Confirmar eliminación',
                    text: '¿Estás seguro de que deseas eliminar esta mesa de entrada?',
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

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <x-adminlte-datatable id="table1" :heads="$heads" head-theme="dark" theme="light" striped hoverable
                        with-buttons :config="['order' => [[10, 'desc']]]">
                        @foreach ($mesasEntrada as $row)
                            <tr>
                                <td>{{ $row->id }}  <i class="fa fa-plus-circle text-primary"></i></td>
                                <td>{{ $row->nro_mentrada }}</td>
                                <td>{{ $row->anho }}</td>
                                <td>{{ $row->fecha_recepcion }}</td>
                                <td>{{ $row->origen->nombre ?? 'N/A' }}</td>
                                <td>{{ $row->tipoDoc->nombre ?? 'N/A' }}</td>
                                <td>{{ $row->observacion }}</td>
                                <td class="{{ $row->estado == '2' ? 'text-danger' : 'text-success' }}">
                                    @if ($row->estado == '2')
                                        Recepcionado
                                    @elseif ($row->estado == '3')
                                        Aceptado
                                    @else
                                        Redireccionado
                                    @endif
                                </td>
                                <td>{{ $row->user->name ?? 'N/A' }}</td>
                                <td style="float:right;">
                                    @if ($row->estado == 2 && $row->mapa_estado == 1)
                                        <form action="{{ route('mesaentrada.aceptar', $row->id) }}" method="post"
                                            class="d-inline enviar-form">
                                            @csrf
                                            <button type="button" class="btn btn-sm  btn-outline-secondary enviar-button">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @elseif($row->estado == 2 && $row->mapa_estado == 2 )
                                        {{-- <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                                            data-bs-target="#modalDestinos">
                                            <i class="fas fa-paper-plane"></i>
                                        </button> --}}
                                        <x-adminlte-button theme="outline-danger" data-toggle="modal"
                                            data-target="#modalDestinos" class="btn-sm " icon="fas fa-paper-plane"
                                            onclick="cargarmentrada({{ $row->id }})" />
                                        <x-adminlte-button theme="outline-danger" data-toggle="modal"
                                            data-target="#modalCargaDocs" onclick="cargarmentrada({{ $row->id }})"
                                            class="btn-sm " icon="fas fa-file-upload" />
                                        <form action="{{ route('mesaentrada.finalizar', $row->id) }}" method="post"
                                            class="d-inline enviar-form">
                                            @csrf
                                            <button type="button" class="btn btn-sm  btn-outline-secondary enviar-button">
                                                <i class="fas fa-flag-checkered"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('reporte.recorrido', $row) }}" target="_blank"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-file-pdf"></i>
                                    </a>
                                    @if ($row->tiene_documentos)
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            onclick="openDocumentosModal({{ $row->id }})">
                                            <i class="fa fa-sm fa-fw fa-print"></i>
                                        </button>
                                    @endif
                                </td>
                                <td>{{ $row->mapa_created_at }}</td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
                </div>
            </div>
        </div>
    </div>
    <!--Modal-->
    <div class="modal fade" id="modalDestinos">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Fijar nuevo destino</h4>
                </div>
                <div class="modal-body d-flex justify-content-center align-items-center">
                    <form action="{{ route('reenviardoc') }}" method="POST">
                        @csrf
                        <x-adminlte-select2 name="id_destino" label="Destino" fgroup-class="col-md-12" required>
                            @foreach ($destinos as $destino)
                                <option value="{{ $destino->id }}" {{ $destino->default == 1 ? 'selected' : '' }}>
                                    {{ $destino->nombre }}
                                </option>
                            @endforeach
                        </x-adminlte-select2>
                        <input type="hidden" name="masdestinos" id="masdestinos" value="0">
                        <input type="hidden" name="idmentrada" id="idmentrada" required>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- fin Modal-->
    <!--Modal-->
    <div class="modal fade" id="modalCargaDocs">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Adjuntar Archivos</h4>
                </div>
                <div class="modal-body d-flex justify-content-center align-items-center">
                    <form action="{{ route('mesaentrada.storedocs') }}" method="post" enctype="multipart/form-data"
                        autocomplete="off">
                        @csrf
                        <input type="hidden" name="idmentrada1" id="idmentrada1">
                        <div class="row">
                            <div class="col-md-5 form-group">
                                <label for="documento">Documento (PDF o DOC)</label>
                                <input type="file" name="documento" id="documento" class="form-control">
                            </div>
                            <div class="col-md-5 form-group">
                                <label for="archivo">Archivo (ZIP o RAR)</label>
                                <input type="file" name="archivo" id="archivo" class="form-control">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="link">Link</label>
                                <input type="text" name="link" id="link" class="form-control">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="observacion">Observación</label>
                                <textarea name="observacion" id="observacion" rows="4" class="form-control"></textarea>
                            </div>
                            <div class="col-md-12 form-group">
                                <x-adminlte-button class="btn-group" style="float: right;" type="submit"
                                    label="Registrar" theme="primary" icon="fas fa-lg fa-save" />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- fin Modal-->
    <!-- Modal -->
    <div class="modal fade" id="documentosModal" tabindex="-1" role="dialog" aria-labelledby="documentosModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentosModalLabel">Documentos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul id="documentosList"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@stop
@push('js')
    <script>
        $('.enviar-button').on('click', function() {
            var form = $(this).closest('.enviar-form');
            Swal.fire({
                title: 'Confirmar',
                text: '¿Estás seguro de Realizar esta accion?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, aceptar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        function cargarmentrada(id) {
            document.getElementById('idmentrada').value = id;
            document.getElementById('idmentrada1').value = id;
        }

        function openDocumentosModal(id) {
            $.ajax({
                url: '{{ route('mesaentrada.documentos', '') }}/' + id,
                method: 'GET',
                success: function(data) {
                    $('#documentosList').empty();
                    if (data.length > 0) {
                        data.forEach(function(doc) {
                            if (doc.link) {
                                var linkHtml = '<a href="' + doc.link +
                                    '" target="_blank"><i class="fa fa-3x fa-link"></i></a>';

                                var observacionHtml = doc.observacion ? '<span class="observacion">' +
                                    doc.observacion + '</span>' : '';

                                $('#documentosList').append('<li>' + linkHtml + ' ' + observacionHtml +
                                    '</li>');
                            }
                            if (doc.nombre_archivo) { // Verificación añadida
                                var extension = doc.nombre_archivo.split('.').pop().toLowerCase();
                                var folder = (extension === 'pdf' || extension === 'docx' ||
                                    extension === 'doc') ? 'documentos' : 'archivos';
                                var url = 'http://localhost/docusys/public/' + folder + '/' + doc
                                    .nombre_archivo;

                                var iconClass = '';
                                if (extension === 'pdf') {
                                    iconClass = 'fa-file-pdf';
                                } else if (extension === 'docx' || extension === 'doc') {
                                    iconClass = 'fa-file-word';
                                } else {
                                    iconClass =
                                        'fa-file-archive'; // Puedes ajustar este icono según el tipo de archivo
                                }

                                var iconHtml = '<i class="fa fa-3x ' + iconClass +
                                    '"></i>'; // Tamaño grande del icono (fa-3x)
                                var linkHtml = '<a href="' + url + '" target="_blank">' + iconHtml +
                                    '</a>';
                                var observacionHtml = doc.observacion ? '<span class="observacion">' +
                                    doc.observacion + '</span>' : '';

                                $('#documentosList').append('<li>' + linkHtml + ' ' + observacionHtml +
                                    '</li>');
                            }
                        });
                    } else {
                        $('#documentosList').append('<li>No hay documentos disponibles.</li>');
                    }
                    $('#documentosModal').modal('show');
                },
                error: function(e) {
                    console.log(e);
                    alert('Error al obtener los documentos.');
                }
            });
        }
    </script>
@endpush
