@extends('adminlte::page')

@section('content_header')
    <h1>Registro de Asistencia</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Registro de Asistencia para el {{ \Carbon\Carbon::parse($fechaSesion)->format('d/m/Y') }}</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('registro_asistencias.store') }}" method="POST">
                @csrf
                <input type="hidden" name="fecha_sesion" value="{{ $fechaSesion }}">

                <table class="table">
                    <thead>
                        <tr>
                            <th>Legislador</th>
                            <th>Presente</th>
                            <th>Justificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($legisladores as $legislador)
                            <tr>
                                <td>{{ $legislador->nombre }} {{ $legislador->apellido }}</td>
                                <td>
                                    <input type="checkbox" class="attendance-checkbox"
                                        data-legislador-id="{{ $legislador->id }}"
                                        {{ isset($asistencias[$legislador->id]) && $asistencias[$legislador->id]->estado == 'Presente' ? 'checked' : '' }}>
                                    <input type="hidden" name="legisladores[]" value="{{ $legislador->id }}">
                                    <input type="hidden" name="estado[]" id="estado-{{ $legislador->id }}"
                                        value="{{ isset($asistencias[$legislador->id]) ? $asistencias[$legislador->id]->estado : 'Ausente' }}">
                                </td>
                                <td>
                                    @if ($legislador->permiso && $legislador->permiso->fecha == $fechaSesion)
                                        <input type="text" class="form-control" name="justificacion[]"
                                            value="{{ $legislador->permiso->tipo_permiso }}" readonly>
                                    @else
                                        <input type="text" class="form-control" name="justificacion[]" value="" readonly>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <button type="submit" style="float: right;" class="btn btn-primary">Registrar Asistencia</button>
                
            </form>
            <a href="{{ route('reporte.asistencia', ['fecha_sesion' => $fechaSesion]) }}" style="float: right;" class="btn btn-danger mr-2" target="_blank" rel="noopener noreferrer"><i class="fas fa-file-pdf"></i></a>

        </div>
    </div>

    @push('js')
    <script>
        document.querySelectorAll('.attendance-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const legisladorId = this.getAttribute('data-legislador-id');
                const estadoField = document.getElementById('estado-' + legisladorId);
                estadoField.value = this.checked ? 'Presente' : 'Ausente';
            });
        });
    </script>
    @endpush
@stop
