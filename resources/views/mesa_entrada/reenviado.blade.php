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
                        with-buttons :config="['order' => [[11, 'desc']]]">
                        @php
                            // Convertir la colección de destinos a un array con los IDs como claves y los nombres como valores
                            $destinosArray = $destinos->pluck('nombre', 'id')->toArray();
                        @endphp
                        @foreach ($mesasEntrada as $row)
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td>{{ $row->nro_mentrada }}</td>
                                <td>{{ $row->anho }}</td>
                                <td>{{ $row->fecha_recepcion }}</td>
                                <td>{{ $row->origen->nombre ?? 'N/A' }}</td>
                                <td>{{ $row->tipoDoc->nombre ?? 'N/A' }}</td>
                                <td></td>
                                <td>{{ $row->observacion }}</td>
                                <td class="{{ $row->estado_recorrido == '2' ? 'text-danger' : 'text-success' }}">
                                    @if ($row->estado_recorrido == '2')
                                        Enviado
                                    @elseif ($row->estado_recorrido == '0')
                                        Aceptado
                                    @elseif ($row->estado_recorrido == '4')
                                        Redireccionado
                                    @elseif ($row->estado == '0')
                                        Trámite Finalizado
                                    @endif

                                </td>
                                <td>{{ $row->user->name ?? 'N/A' }}</td>
                                <td>
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
                                    @if ($row->estado_recorrido > 0)
                                        <x-adminlte-button theme="outline-danger" data-toggle="modal"
                                            data-target="#modalDestinos" class="btn-sm " icon="fas fa-paper-plane"
                                            onclick="cargarmentrada({{ $row->id }})" />
                                    @endif
                                </td>
                                <td>{{ $row->fecha_creacion_recorrido }}</td>
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

                        <input type="hidden" name="idmentrada" id="idmentrada" required>

                        <!-- Campo hidden para masdestinos -->
                        <input type="hidden" name="masdestinos" id="masdestinos" value="0">

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-info" onclick="setMasDestinos(1)"
                                data-bs-dismiss="modal">Fijar Otro destino más</button>
                            <button type="submit" class="btn btn-primary" onclick="setMasDestinos(0)">Cambiar Destino
                                Enviado</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
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

    <!-- fin Modal-->
@stop
@push('js')
    <script>
        function setMasDestinos(value) {
            // Cambia el valor del campo hidden a 0 o 1
            document.getElementById('masdestinos').value = value;
        }
        $('.enviar-button').on('click', function() {
            var form = $(this).closest('.enviar-form');
            Swal.fire({
                title: 'Confirmar',
                text: '¿Estás seguro de que ya recepciono el formulario?',
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
