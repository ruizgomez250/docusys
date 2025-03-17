@extends('adminlte::page')

@section('content_header')
<h1 class="m-0 custom-heading">Gráficos</h1>
@stop

@section('plugins.Sweetalert2', true)

@push('js')
    <script>
        const labels = @json($documentosporfechas->pluck('mes')); // Las etiquetas del gráfico (meses)

        const data = {
            labels: labels,
            datasets: [{
                label: 'Total de Documentos',
                data: @json($documentosporfechas->pluck('cantidad')), // Las cantidades por mes
                backgroundColor: '#6c757d', // Color para las barras
            }]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Total de Documentos por Mes'
                    }
                },
                scales: {
                    x: {
                        stacked: false,
                    },
                    y: {
                        stacked: false,
                        beginAtZero: true
                    }
                }
            },
        };

        // Crear el gráfico en el canvas
        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, config);
    </script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <canvas id="myChart" width="400" height="200"></canvas>
        </div>
    </div>
@stop
