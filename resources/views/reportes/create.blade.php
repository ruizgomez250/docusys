@extends('adminlte::page')



@section('content_header')
    <h1 class="m-0 custom-heading">Generar Reportes por Fechas</h1>
@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/jquery-ui-1.13.2/jquery-ui.min.css') }}">
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{-- 'id', 'codigo', 'descripcion', 'detalle', 'id_categoria', 'id_estado','pcosto', 'pventa', 'observacion' --}}





                    <div class="row">
                        {{-- With Label --}}

                        <div class="form-group">
                            <label for="fechaemision">FECHA DESDE</label>
                            <input type="date" class="form-control" id="desde1" name="fechaemision"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="fechaemision">FECHA HASTA</label>
                            <input type="date" class="form-control" id="hasta1" name="fechaemision"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <x-adminlte-select2 name="idorigen" id="idorigen" label="ORIGEN DOCUMENTO"
                             fgroup-class="col-md-4">
                            <x-slot name="prependSlot">
                                <div class="input-group-text bg-gradient-secondary">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                            </x-slot>
                            @foreach ($origen as $item)
                                <option value={{ $item->id }}>{{ $item->nombre }}</option>
                            @endforeach
                        </x-adminlte-select2>
                        <button class="btn btn-secondary" style="float: right;" onclick="generarPDForigen()">Generar</button>



                    </div>

                    <hr>

                    <div class="row">
                        {{-- With Label --}}
                        @php
                            $config1 = ['format' => 'DD-MM-YYYY'];
                        @endphp
                        <div class="form-group">
                            <label for="fechaemision">FECHA DESDE</label>
                            <input type="date" class="form-control" id="desde2" name="fechaemision"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="fechaemision">FECHA HASTA</label>
                            <input type="date" class="form-control" id="hasta2" name="fechaemision"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <x-adminlte-select2 name="idtipodoc" id="idtipodoc" label="TIPO DOCUMENTO"
                             fgroup-class="col-md-4">
                            <x-slot name="prependSlot">
                                <div class="input-group-text bg-gradient-secondary">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                            </x-slot>
                            @foreach ($tiposdocs as $item)
                                <option value={{ $item->id }}>{{ $item->nombre }}</option>
                            @endforeach
                        </x-adminlte-select2>
                        <x-adminlte-button class="btn" style="float: right;" label="Generar" theme="secondary" onclick="generarPDFsinuser()"
                             />


                    </div>
                    <hr>

                    <div class="row">
                        {{-- With Label --}}
                        @php
                            $config1 = ['format' => 'DD-MM-YYYY'];
                        @endphp
                        <div class="form-group">
                            <label for="fechaemision">FECHA DESDE</label>
                            <input type="date" class="form-control" id="desde2" name="fechaemision"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="fechaemision">FECHA HASTA</label>
                            <input type="date" class="form-control" id="hasta2" name="fechaemision"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <x-adminlte-button class="btn" style="float: right;" label="Generar" theme="secondary" onclick="generarPDFsinuser()"
                             />


                    </div>
                    <!-- Agrega este elemento para mostrar la suma total -->

                    

                </div>
            </div>
        </div>
    </div>

@stop

@push('js')
    <script src="{{ asset('vendor/jquery-ui-1.13.2/jquery-ui.min.js') }}"></script>
    <script>
        function generarPDForigen() {
            var desde = document.getElementById('desde1').value;
            var hasta = document.getElementById('hasta1').value;
        
        
            var idorigen = document.getElementById('idorigen').value;

            var url = `/docusys/public/reportefechaspdf/${desde}/${hasta}/${idorigen}/1`;
            window.open(url, '_blank');
        }
        function generarPDFsinuser() {
            var desde = document.getElementById('desde2').value;
            var hasta = document.getElementById('hasta2').value;

            var url = `/controlventa/public/gananciareportepdf/${desde}/${hasta}`;
            window.open(url, '_blank');
        }
    </script>
@endpush