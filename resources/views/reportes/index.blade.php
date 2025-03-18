@extends('adminlte::page')

@section('content_header')
    <h1 class="m-0 custom-heading">Gráficos</h1>
    <style>
        #myChart {
            max-width: 400px;
            max-height: 300px;
        }
        #myChart1 {
            max-height: 900px;
        }
    </style>

@stop


@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Definir etiquetas (meses)
            const labels = @json($documentosporfechas->pluck('mes'));

            // Configuración del primer gráfico (Total de Documentos por mes)
            const data = {
                labels: labels,
                datasets: [{
                    label: 'Total de Documentos',
                    data: @json($documentosporfechas->pluck('cantidad')),
                    backgroundColor: '#6c757d',
                }]
            };

            const config = {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: 'Total de Documentos por Mes' }
                    },
                    scales: {
                        x: { stacked: false },
                        y: { stacked: false, beginAtZero: true }
                    }
                }
            };

            const ctx = document.getElementById('myChart').getContext('2d');
            new Chart(ctx, config);

            // Segundo gráfico: Tipos de Documentos por Mes
            const tiposDocsData = @json($portiposdocs); // Asegurar que portiposdocs está definido

            const tiposUnicos = [...new Set(tiposDocsData.map(item => item.tipo_doc))];

            const datasets = tiposUnicos.map((tipo, index) => {
                return {
                    label: tipo,
                    data: labels.map(mes => {
                        const found = tiposDocsData.find(item => item.mes === mes && item.tipo_doc === tipo);
                        return found ? found.cantidad : 0;
                    }),
                    borderColor: `hsl(${index * 50}, 70%, 50%)`,
                    backgroundColor: `hsla(${index * 50}, 70%, 50%, 0.5)`,
                    fill: false,
                };
            });

            const data1 = { labels: labels, datasets: datasets };

            const config1 = {
                type: 'line',
                data: data1,
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' },
                        title: { display: true, text: 'Tipos de Documentos por Mes' }
                    }
                }
            };

            const ctx1 = document.getElementById('myChart1').getContext('2d');
            new Chart(ctx1, config1);
        });
    </script>
@endpush


@section('content')
    <div class="row">
        <div class="col-12">
            <canvas id="myChart" width="30" height="30"></canvas>
        </div>

    </div>
    <div class="row">

        <div class="col-12">
            <canvas id="myChart1" width="30" height="30"></canvas>
        </div>
    </div>

@stop
