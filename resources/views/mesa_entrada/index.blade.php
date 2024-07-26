@extends('adminlte::page')

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h1 class="m-0 custom-heading">Lista de Mesas de Entradas</h1>
        </div>
        <div class="col-6">
            <a href="{{ route('mesaentrada.create') }}" class="btn btn-primary" style="float: right;">Registrar Nueva Mesa de
                Entrada</a>
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
            $('.enviar-button').on('click', function() {
                var form = $(this).closest('.enviar-form');
                Swal.fire({
                    title: 'Confirmar',
                    text: '¿Estás seguro de que ya envio el formulario?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, enviar',
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
                    @php
                        $config = [
                            'order' => [[3, 'desc']], // Ordenar por la cuarta columna (índice 3) en orden ascendente
                        ];
                    @endphp
                    <x-adminlte-datatable id="table1" :heads="$heads" head-theme="dark" theme="light" striped
                        hoverable with-buttons :config="$config">



                        @foreach ($mesasEntrada as $row)
                            <tr>
                                <td>{{ $row->id }}</td>
                                <td>{{ $row->nro_mentrada }}</td>
                                <td>{{ $row->anho }}</td>
                                <td>{{ $row->fecha_recepcion }}</td>
                                <td>{{ $row->origen->nombre ?? 'N/A' }}</td>
                                <td>{{ $row->tipoDoc->nombre ?? 'N/A' }}</td>
                                <td>{{ $row->destino->nombre ?? 'N/A' }}</td>
                                <td>{{ $row->observacion }}</td>
                                <td class="{{ $row->estado == '1' ? 'text-danger' : 'text-success' }}">
                                    {{ $row->estado == '1' ? 'Recepcionado' : 'Enviado' }}</td>
                                <td>{{ $row->user->name ?? 'N/A' }}</td>
                                <td style="float:right;">
                                    @if ($row->estado == 1)
                                        <a href="{{ route('mesaentrada.edit', $row->id) }}"
                                            class="btn btn-sm  btn-outline-secondary">
                                            <i class="fa fa-sm fa-fw fa-pen"></i>
                                        </a>
                                        <form action="{{ route('mesaentrada.destroy', $row->id) }}" method="post"
                                            class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="id_mentrada" value="{{ $row->id }}" />
                                            <button type="button" class="btn btn-sm  btn-outline-secondary delete-button">
                                                <ion-icon name="trash-outline"><i
                                                        class="fa fa-sm fa-fw fa-trash"></i></ion-icon>
                                            </button>
                                        </form>
                                        <form action="{{ route('mesaentrada.enviar', $row->id) }}" method="post"
                                            class="d-inline enviar-form">
                                            @csrf
                                            <button type="button" class="btn btn-sm  btn-outline-secondary enviar-button">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if ($row->tiene_documentos)
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            onclick="openDocumentosModal({{ $row->id }})">
                                            <i class="fa fa-sm fa-fw fa-print"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </x-adminlte-datatable>
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
    </div>
@stop
@push('js')
    <script>
        function openDocumentosModal(id) {
            $.ajax({
                url: '{{ route('mesaentrada.documentos', '') }}/' + id,
                method: 'GET',
                success: function(data) {
                    $('#documentosList').empty();
                    if (data.length > 0) {
                        data.forEach(function(doc) {
                            var extension = doc.nombre_archivo.split('.').pop().toLowerCase();
                            var folder = (extension === 'pdf' || extension === 'docx' || extension ===
                                'doc') ? 'documentos' : 'archivos';
                            var url = '{{ asset('') }}' + folder + '/' + doc.nombre_archivo;

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
                            var linkHtml = '<a href="' + url + '" target="_blank">' + iconHtml + '</a>';
                            $('#documentosList').append('<li>' + linkHtml + '</li>');
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
