@extends('adminlte::page')

@section('content_header')
    <h1 class="m-0 custom-heading">Reporte de documentos Ingresados</h1>
@stop
@section('plugins.Sweetalert2', true)

@push('js')
    <script>
        $(document).ready(function() {
            var Toast = Swal.mixin({
                toast: true,
                position: 'bottom-end',
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
        });
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="row">
                    <div class="form-group">
                        <label for="fechaemision">DESDE</label>
                        <input type="date" class="form-control" id="desde" name="desde" value="{{ date('Y-m-d') }}"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="fechaemision">HASTA</label>
                        <input type="date" class="form-control" id="hasta" name="hasta" value="{{ date('Y-m-d') }}"
                            required>
                    </div>
                </div>

                <button onclick="mostrarpdf()" class="btn btn-secondary mt-2" type="button">
                    <i class="fas fa-file-pdf"></i> Generar Reporte
                </button>
            </div>
        </div>
    </div>
@stop
@push('js')
    <script src="{{ asset('vendor/jquery-ui-1.13.2/jquery-ui.min.js') }}"></script>
    <script>
        // Función para agregar un nuevo ítem de compra
        function mostrarpdf() {
            const desde = document.getElementById('desde').value;
            const hasta = document.getElementById('hasta').value;

            if (!desde || !hasta) {
                alert("Por favor, seleccione ambas fechas.");
                return;
            }

            // Construir la URL con las fechas como parámetros
            const url = `/docusys/public/generar-planilla?desde=${desde}&hasta=${hasta}`;
            window.open(url, '_blank'); // Abrir el PDF en una nueva pestaña
        }
    </script>
@endpush
