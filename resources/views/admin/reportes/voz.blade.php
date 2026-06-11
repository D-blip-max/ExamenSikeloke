@extends('adminlte::page')

@section('content_header')
    <h1><b>Consulta por voz</b></h1>
    <hr>
@stop

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Habla para consultar la base de datos</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <button id="startButton" class="btn btn-primary">Iniciar voz</button>
                <button id="stopButton" class="btn btn-secondary" disabled>Detener</button>
            </div>

            <div class="form-group">
                <label for="mensaje">Texto reconocido</label>
                <textarea id="mensaje" class="form-control" rows="3" readonly></textarea>
            </div>

            <form id="voiceForm" method="post" action="{{ route('admin.reportes.voz.consultar') }}">
                @csrf
                <input type="hidden" name="mensaje" id="hiddenMensaje" value="">
                <button type="submit" class="btn btn-success mt-3" id="sendButton" disabled>Enviar consulta</button>
            </form>

            @if(session('error'))
                <div class="alert alert-danger mt-3">{{ session('error') }}</div>
            @endif

            @if(!empty($consulta))
                <div class="alert alert-info mt-3">
                    <strong>Consulta:</strong> {{ $consulta }}
                </div>
            @endif

            @if(!empty($resultados))
                <div class="mt-4">
                    <h5>Resultados</h5>
                    <div class="table-responsive">
                        @if(isset($entity) && $entity === 'docente_horario')
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Docente</th>
                                        <th>Materia</th>
                                        <th>Grupo</th>
                                        <th>Día</th>
                                        <th>Horario</th>
                                        <th>Aula</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resultados as $item)
                                        <tr>
                                            <td>{{ $item->id ?? '' }}</td>
                                            <td>{{ optional($item->docente)->nombres ?? '' }} {{ optional($item->docente)->apellidos ?? '' }}</td>
                                            <td>{{ optional($item->materia)->nombre ?? '' }}</td>
                                            <td>{{ optional($item->grupo)->nombre ?? '' }}</td>
                                            <td>{{ optional($item->dia)->nombre ?? '' }}</td>
                                            <td>{{ optional($item->horario)->horaInicio ?? '' }} - {{ optional($item->horario)->horaFin ?? '' }}</td>
                                            <td>{{ optional($item->aula)->nombre ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            @php
                                $firstItem = $resultados->first();
                                if ($firstItem) {
                                    if (is_array($firstItem)) {
                                        $columns = array_keys($firstItem);
                                    } elseif (method_exists($firstItem, 'getAttributes')) {
                                        $columns = array_keys($firstItem->getAttributes());
                                    } else {
                                        $columns = array_keys((array) $firstItem);
                                    }
                                } else {
                                    $columns = [];
                                }
                            @endphp
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        @foreach($columns as $column)
                                            <th>{{ ucfirst(str_replace('_', ' ', $column)) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resultados as $item)
                                        <tr>
                                            @foreach($columns as $column)
                                                <td>{{ data_get($item, $column, '') }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @endif

            @if(!empty($hint))
                <div class="alert alert-info mt-3">
                    <strong>Aclaración:</strong> {{ $hint }}
                </div>
            @endif

            @if(!empty($response))
                <div class="alert alert-secondary mt-3">
                    <strong>Interpretación IA:</strong>
                    <pre>{{ json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
        </div>
    </div>
@stop

@section('js')
    <script>
        const startButton = document.getElementById('startButton');
        const stopButton = document.getElementById('stopButton');
        const mensajeTextarea = document.getElementById('mensaje');
        const hiddenMensaje = document.getElementById('hiddenMensaje');
        const sendButton = document.getElementById('sendButton');

        let recognition;

        if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
            startButton.disabled = true;
            stopButton.disabled = true;
            mensajeTextarea.value = 'Tu navegador no soporta Web Speech API.';
        } else {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            recognition = new SpeechRecognition();
            recognition.lang = 'es-ES';
            recognition.interimResults = false;
            recognition.maxAlternatives = 1;

            recognition.addEventListener('result', (event) => {
                const transcript = event.results[0][0].transcript;
                mensajeTextarea.value = transcript;
                hiddenMensaje.value = transcript;
                sendButton.disabled = false;
            });

            recognition.addEventListener('end', () => {
                stopButton.disabled = true;
                startButton.disabled = false;
            });

            startButton.addEventListener('click', () => {
                recognition.start();
                startButton.disabled = true;
                stopButton.disabled = false;
                mensajeTextarea.value = 'Escuchando...';
            });

            stopButton.addEventListener('click', () => {
                recognition.stop();
                stopButton.disabled = true;
                startButton.disabled = false;
            });
        }
    </script>
@stop