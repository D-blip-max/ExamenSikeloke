<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Postulante;
use App\Models\Asignacion;
use App\Models\PostGrupo;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Carrera;
use App\Models\Bitacora;
use App\Models\Admitido;
use App\Models\Reprobado;

class ReporteController extends Controller
{
    public function index()
    {
        return view('admin.reportes.index');
    }

    public function voz()
    {
        return view('admin.reportes.voz');
    }

    public function consultarVoz(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|string|max:1000',
        ]);

        $texto = $request->input('mensaje');

        $response = $this->llamarGroq($texto);

        if (!$response || !isset($response['intent']) || $response['intent'] === 'invalid') {
            $response = $this->interpretarTextoLocal($texto);
        }

        if (!$response || (!isset($response['intent']) && !isset($response['raw_sql']) && !isset($response['table']))) {
            return back()->with('error', 'No se pudo interpretar la consulta.');
        }

        // Si Groq devuelve SQL crudo (solo SELECT permitido), ejecútalo de forma segura
        if (!empty($response['raw_sql']) || !empty($response['sql'])) {
            $sql = $response['raw_sql'] ?? $response['sql'];
            $resultados = $this->executeRawSql($sql);
        } elseif (!empty($response['table'])) {
            // Consulta genérica construida por IA: tabla + filters
            $resultados = $this->buildGenericQuery($response['table'], $response['filters'] ?? []);
        } else {
            $resultados = $this->procesarIntento($response);
        }

        return view('admin.reportes.voz', [
            'consulta' => $texto,
            'resultados' => $resultados,
            'response' => $response,
            'entity' => $response['entity'] ?? null,
            'table' => $response['table'] ?? null,
            'hint' => $response['hint'] ?? null,
        ]);
    }

    protected function llamarGroq(string $texto)
    {
        $apiKey = env('GROQ_API_KEY');
        $endpoint = env('GROQ_API_URL', 'https://api.groq.com/v1');
        $model = env('GROQ_MODEL', 'groq-1');

        if (!$apiKey) {
            return null;
        }

        // Incluir un snapshot del esquema (tablas y columnas) para ayudar al modelo a generar consultas precisas
        $schemaSnapshot = $this->getSchemaSnapshot();
        $schemaRelations = $this->getSchemaRelations();

        $prompt = "Eres un asistente que interpreta consultas en español para una base de datos académica. " .
            "Recibe solo una frase de usuario y responde únicamente con JSON válido usando las claves: intent, entity, filters, table, raw_sql. " .
            "No incluyas texto extra ni explicaciones. " .
            "Si puedes mapear la consulta a un intent conocido, devuelve intent y filters. " .
            "Si la consulta es genérica o sobre una tabla no listada, devuelve table y filters. " .
            "Si puedes construir SQL seguro, devuelve raw_sql con SELECT. " .
            "Intent puede ser 'show_students', 'show_admitted', 'show_approved', 'show_reprobados', 'show_by_ci', 'show_by_carrera', 'show_carreras', 'show_docentes_horario' o 'invalid'. " .
            "Entity puede ser 'postulante', 'admitido', 'reprobado', 'carrera', 'docente_horario', o null. " .
            "Filters debe ser un objeto con pares nombre:valor para campos válidos: carrera, ci, nombre, apellido, pago_confirmado, nota_min, docente, fecha, fecha_inicio, fecha_fin, estado, especialidad, turno, grupo, materia, aula, dia, correo, ciudad, colegio, gestion. " .
            "Para pagos usa pago_confirmado:true o pago_confirmado:false. " .
            "Para estados de pago usa estado:'PENDIENTE' o estado:'CONFIRMADO'. " .
            "Para rangos de fecha usa fecha_inicio y fecha_fin. " .
            "En español, estudiantes, alumnos y candidatos se interpretan como postulantes. " .
            "No dependas de mayúsculas, minúsculas ni tildes al comparar valores. " .
            "Si el usuario escribe 'ingenieria' debe corresponder a 'Ingeniería'. " .
            "Si el usuario escribe la tabla 'materias', devuelve table:'materias'. " .
            "Si el usuario escribe la tabla 'asignaciones', devuelve table:'asignaciones'. " .
            "Si no puedes interpretar la consulta, responde {\"intent\":\"invalid\",\"entity\":null,\"filters\":{},\"table\":null}. " .
            "Ejemplos: " .
            "Frase: 'Mostrame todos los postulantes' => {\"intent\":\"show_students\",\"entity\":\"postulante\",\"filters\":{}}. " .
            "Frase: 'Mostrame los estudiantes admitidos en Computación' => {\"intent\":\"show_admitted\",\"entity\":\"admitido\",\"filters\":{\"carrera\":\"Computación\"}}. " .
            "Frase: 'Mostrar reprobados' => {\"intent\":\"show_reprobados\",\"entity\":\"reprobado\",\"filters\":{}}. " .
            "Frase: 'Mostrar estudiantes llamados Alvaro que no pagaron' => {\"intent\":\"show_students\",\"entity\":\"postulante\",\"filters\":{\"nombre\":\"Alvaro\",\"pago_confirmado\":false}}. " .
            "Frase: 'Mostrar todos los aprobados con nota mayor a 60' => {\"intent\":\"show_approved\",\"entity\":\"admitido\",\"filters\":{\"nota_min\":60}}. " .
            "Frase: 'Buscar estudiantes con CI 123456' => {\"intent\":\"show_by_ci\",\"entity\":\"postulante\",\"filters\":{\"ci\":\"123456\"}}. " .
            "Frase: 'Mostrame las carreras' => {\"intent\":\"show_carreras\",\"entity\":\"carrera\",\"filters\":{}}. " .
            "Frase: 'Mostrar carreras de Computación' => {\"intent\":\"show_carreras\",\"entity\":\"carrera\",\"filters\":{\"carrera\":\"Computación\"}}. " .
            "Frase: 'Mostrar materias' => {\"table\":\"materias\",\"filters\":{}}. " .
            "Frase: 'Mostrar pagos de postulantes que no pagaron en mayo' => {\"table\":\"pagos\",\"filters\":{\"estado\":\"PENDIENTE\"}}. " .
            "Frase: 'Mostrar las asignaciones de los docentes' => {\"table\":\"asignaciones\",\"filters\":{}}. " .
            "Frase: 'Por favor dame la carga horaria de los docentes' => {\"intent\":\"show_docentes_horario\",\"entity\":\"docente_horario\",\"filters\":{}}. " .
            "Frase: 'Mostrar carga horaria del docente Juan Perez' => {\"intent\":\"show_docentes_horario\",\"entity\":\"docente_horario\",\"filters\":{\"docente\":\"Juan Perez\"}}. " .
            "Frase: 'Mostrar alumnos de la carrera Ingeniería con pagos confirmados' => {\"intent\":\"show_students\",\"entity\":\"postulante\",\"filters\":{\"carrera\":\"Ingeniería\",\"pago_confirmado\":true}}. " .
            "Frase: 'Mostrar notas de postulantes en la materia Matemáticas con nota mayor a 70' => {\"table\":\"notas\",\"filters\":{\"materia_id\":\"Matemáticas\",\"nota\":\">70\"}}. " .
            "Frase: 'Mostrar pagos confirmados en junio' => {\"table\":\"pagos\",\"filters\":{\"estado\":\"CONFIRMADO\",\"fecha\":\"2026-06\"}}. " .
            "Frase: 'Mostrar asignaciones por aula 101 los lunes' => {\"table\":\"asignaciones\",\"filters\":{\"aula_id\":\"101\",\"dia_id\":\"Lunes\"}}. " .
            "Frase: \"{$texto}\"";
        // Agregar esquema y relaciones al final del prompt
        $prompt .= "\nBaseDeDatos: " . $schemaSnapshot . "\n";
        $prompt .= "Relaciones: " . $schemaRelations . "\n";

        $body = [
            'model' => $model,
            'input' => $prompt,
            'max_output_tokens' => 512,
            'temperature' => 0.1,
        ];

        try {
            $client = new \GuzzleHttp\Client();
            $responses = [];

            // Intento 1: endpoint predict
            try {
                $res = $client->post(rtrim($endpoint, '/') . '/predict', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $apiKey,
                    ],
                    'json' => $body,
                    'timeout' => 20,
                ]);
                $responses[] = $res->getBody()->getContents();
            } catch (\Exception $e) {
                // Ignorar, se intentará el segundo endpoint
            }

            // Intento 2: endpoint de modelo estándar
            try {
                $res = $client->post(rtrim($endpoint, '/') . '/models/' . $model . '/outputs', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $apiKey,
                    ],
                    'json' => ['input' => $prompt],
                    'timeout' => 20,
                ]);
                $responses[] = $res->getBody()->getContents();
            } catch (\Exception $e) {
                // Ignorar
            }

            foreach ($responses as $raw) {
                $data = json_decode($raw, true);
                if (is_array($data)) {
                    $decoded = $this->parseGroqResult($data);
                    if ($decoded !== null) {
                        return $decoded;
                    }
                }

                $decoded = $this->extractJsonFromText($raw);
                if ($decoded !== null) {
                    return $decoded;
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::debug('Groq request error', ['exception' => $e->getMessage()]);
            return null;
        }
    }

    protected function procesarIntento(array $response)
    {
        $intent = $response['intent'] ?? 'invalid';
        $filters = $response['filters'] ?? [];

        switch ($intent) {
            case 'show_students':
            case 'show_by_ci':
            case 'show_by_carrera':
                return $this->queryPostulantes($filters);
            case 'show_admitted':
            case 'show_approved':
                return $this->queryAprobados($filters);
            case 'show_reprobados':
                return $this->queryReprobados($filters);
            case 'show_carreras':
                return $this->queryCarreras($filters);
            case 'show_docentes_horario':
                return $this->queryDocentesHorario($filters);
            default:
                return [];
        }
    }

    protected function parseGroqResult(array $data)
    {
        if (isset($data['output']) && is_array($data['output'])) {
            foreach ($data['output'] as $item) {
                if (isset($item['content']) && is_array($item['content'])) {
                    foreach ($item['content'] as $content) {
                        if (isset($content['text'])) {
                            $decoded = json_decode(trim($content['text']), true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                return $decoded;
                            }
                        }
                    }
                }
            }
        }

        if (isset($data['results']) && is_array($data['results'])) {
            foreach ($data['results'] as $result) {
                if (isset($result['output_text'])) {
                    $decoded = json_decode(trim($result['output_text']), true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return $decoded;
                    }
                }
            }
        }

        return null;
    }

    protected function getSchemaSnapshot()
    {
        $tables = [
            'users','grupos','aulas','carreras','horarios','dias','gestiones','materias','turnos','docentes','postulantes','pagos','notas','admitidos','reprobados','post_grupos','asignaciones','bitacoras'
        ];
        $parts = [];
        foreach ($tables as $t) {
            try {
                $cols = Schema::getColumnListing($t);
                $parts[] = $t . '(' . implode(',', $cols) . ')';
            } catch (\Exception $e) {
                // tabla inexistente en este esquema o permiso
            }
        }
        return implode('; ', $parts);
    }

    protected function getSchemaRelations()
    {
        $relations = [
            'postulantes.carrera1_id -> carreras.id',
            'postulantes.carrera2_id -> carreras.id',
            'postulantes.gestion_id -> gestions.id',
            'pagos.postulante_id -> postulantes.id',
            'notas.postulante_id -> postulantes.id',
            'notas.materia_id -> materias.id',
            'notas.config_examen_id -> config_porcentaje.id',
            'admitidos.postulante_id -> postulantes.id',
            'admitidos.carrera_id -> carreras.id',
            'reprobados.postulante_id -> postulantes.id',
            'docentes.turno_id -> turnos.id',
            'asignaciones.grupo_id -> grupos.id',
            'asignaciones.materia_id -> materias.id',
            'asignaciones.docente_id -> docentes.id',
            'asignaciones.aula_id -> aulas.id',
            'asignaciones.dia_id -> dias.id',
            'asignaciones.horario_id -> horarios.id',
            'post_grupos.postulante_id -> postulantes.id',
            'post_grupos.grupo_id -> grupos.id',
            'bitacoras.user_id -> users.id',
        ];

        return implode('; ', $relations);
    }

    protected function executeRawSql(string $sql)
    {
        // Security: permitir solo SELECT y limitar filas
        $lower = ltrim(mb_strtolower($sql, 'UTF-8'));
        if (stripos($lower, 'select') !== 0) {
            return [];
        }
        // Añadir límite si no existe
        if (stripos($lower, 'limit') === false) {
            $sql = rtrim($sql, " ;") . ' LIMIT 200';
        }
        try {
            $rows = DB::select(DB::raw($sql));
            return collect($rows);
        } catch (\Exception $e) {
            Log::debug('SQL execution error', ['error' => $e->getMessage()]);
            return [];
        }
    }

    protected function buildGenericQuery(string $table, array $filters)
    {
        // Construye una consulta genérica con filtros simples y joins básicos entre tablas relacionadas
        if (!Schema::hasTable($table)) {
            return [];
        }

        $columns = Schema::getColumnListing($table);
        $query = DB::table($table);
        $dateRangeApplied = false;

        // Joins básicos para tablas relacionadas
        if ($table === 'asignaciones') {
            $query->leftJoin('docentes', 'asignaciones.docente_id', '=', 'docentes.id')
                  ->leftJoin('materias', 'asignaciones.materia_id', '=', 'materias.id')
                  ->leftJoin('aulas', 'asignaciones.aula_id', '=', 'aulas.id')
                  ->leftJoin('dias', 'asignaciones.dia_id', '=', 'dias.id')
                  ->leftJoin('horarios', 'asignaciones.horario_id', '=', 'horarios.id')
                  ->leftJoin('grupos', 'asignaciones.grupo_id', '=', 'grupos.id');
        }

        if ($table === 'pagos') {
            $query->leftJoin('postulantes', 'pagos.postulante_id', '=', 'postulantes.id');
        }

        if (in_array($table, ['notas', 'admitidos', 'reprobados'], true)) {
            $query->leftJoin('postulantes', $table . '.postulante_id', '=', 'postulantes.id');
        }

        if ($table === 'notas') {
            $query->leftJoin('materias', 'notas.materia_id', '=', 'materias.id');
            $query->leftJoin('config_porcentaje', 'notas.config_examen_id', '=', 'config_porcentaje.id');
        }

        if ($table === 'admitidos') {
            $query->leftJoin('carreras', 'admitidos.carrera_id', '=', 'carreras.id');
        }

        if ($table === 'postulantes') {
            $query->leftJoin('carreras as carrera1', 'postulantes.carrera1_id', '=', 'carrera1.id')
                  ->leftJoin('carreras as carrera2', 'postulantes.carrera2_id', '=', 'carrera2.id')
                  ->leftJoin('gestions', 'postulantes.gestion_id', '=', 'gestions.id');
        }

        foreach ($filters as $k => $v) {
            if (is_null($v) || $v === '') {
                continue;
            }
            if ($k === 'fecha_inicio' || $k === 'fecha_fin') {
                continue;
            }
            if (is_array($v)) {
                if (in_array($k, $columns, true)) {
                    $query->whereIn($table . '.' . $k, $v);
                }
                continue;
            }
            if ($k === 'pago_confirmado') {
                if (in_array('pago_confirmado', $columns, true)) {
                    $query->where($table . '.pago_confirmado', $v ? 1 : 0);
                } elseif ($table === 'postulantes') {
                    $query->where('postulantes.pago_confirmado', $v ? 1 : 0);
                }
                continue;
            }
            if (!in_array($k, $columns, true) && preg_match('/^(.*)_min$/', $k, $m) && in_array($m[1], $columns, true)) {
                $query->where($table . '.' . $m[1], '>=', $v);
                continue;
            }
            if (!in_array($k, $columns, true) && preg_match('/^(.*)_max$/', $k, $m) && in_array($m[1], $columns, true)) {
                $query->where($table . '.' . $m[1], '<=', $v);
                continue;
            }
            if (!$dateRangeApplied && isset($filters['fecha_inicio'], $filters['fecha_fin']) && in_array('fecha', $columns, true)) {
                $query->whereBetween($table . '.fecha', [trim($filters['fecha_inicio']), trim($filters['fecha_fin'])]);
                $dateRangeApplied = true;
                continue;
            }
            if ($k === 'fecha' && in_array('fecha', $columns, true)) {
                $query = $this->addInsensitiveLike($query, $table . '.fecha', trim($v));
                continue;
            }

            if ($k === 'postulante' && in_array('postulante_id', $columns, true)) {
                $query->where(function ($q) use ($v) {
                    $q = $this->addInsensitiveLike($q, 'postulantes.nombres', $v)
                        ->orWhereRaw('postulantes.apellidos LIKE ? COLLATE utf8mb4_general_ci', ['%' . $v . '%'])
                        ->orWhereRaw('postulantes.ci LIKE ? COLLATE utf8mb4_general_ci', ['%' . $v . '%']);
                });
                continue;
            }
            if (in_array($k, ['carrera', 'materia', 'docente', 'aula', 'dia', 'turno', 'gestion'], true)) {
                $relationColumn = null;
                switch ($k) {
                    case 'carrera':
                        if ($table === 'postulantes') {
                            $query->where(function ($q) use ($v) {
                                $q = $this->addInsensitiveLike($q, 'carrera1.nombre', $v)
                                  ->orWhereRaw('carrera2.nombre LIKE ? COLLATE utf8mb4_general_ci', ['%' . $v . '%']);
                            });
                            continue 2;
                        }
                        if (in_array('carrera_id', $columns, true)) {
                            $relationColumn = 'carreras.nombre';
                        }
                        break;
                    case 'materia':
                        if (in_array('materia_id', $columns, true) || $table === 'asignaciones') {
                            $relationColumn = 'materias.nombre';
                        }
                        break;
                    case 'docente':
                        if (in_array('docente_id', $columns, true) || $table === 'asignaciones') {
                            $relationColumn = 'docentes.nombre';
                        }
                        break;
                    case 'aula':
                        if (in_array('aula_id', $columns, true) || $table === 'asignaciones') {
                            $relationColumn = 'aulas.nombre';
                        }
                        break;
                    case 'dia':
                        if (in_array('dia_id', $columns, true) || $table === 'asignaciones') {
                            $relationColumn = 'dias.nombre';
                        }
                        break;
                    case 'turno':
                        if ($table === 'docentes' || $table === 'grupos') {
                            $query = $this->addInsensitiveLike($query, 'turnos.nombre', $v);
                            continue 2;
                        }
                        break;
                    case 'gestion':
                        if ($table === 'postulantes') {
                            $query = $this->addInsensitiveLike($query, 'gestions.nombre', $v);
                            continue 2;
                        }
                        break;
                }
                if ($relationColumn) {
                    $query = $this->addInsensitiveLike($query, $relationColumn, $v);
                    continue;
                }
            }

            if (!in_array($k, $columns, true)) {
                if ($k === 'nombre' && in_array('nombres', $columns, true)) {
                    $query = $this->addInsensitiveLike($query, $table . '.nombres', $v);
                    continue;
                }
                if ($k === 'nombre' && in_array('nombre', $columns, true)) {
                    $query = $this->addInsensitiveLike($query, $table . '.nombre', $v);
                    continue;
                }
                continue;
            }
            if (is_string($v) && preg_match('/^(>=|<=|>|<|=)\s*(\d+(?:\.\d+)?)$/', trim($v), $m)) {
                $op = $m[1]; $num = $m[2];
                $query->where($table . '.' . $k, $op, $num);
                continue;
            }
            if (is_string($v) && strpos($v, '..') !== false) {
                [$from, $to] = explode('..', $v, 2);
                $query->whereBetween($table . '.' . $k, [trim($from), trim($to)]);
                continue;
            }
            if (is_numeric($v) && in_array(Schema::getColumnType($table, $k), ['integer', 'bigint', 'smallint', 'mediumint', 'tinyint', 'float', 'double', 'decimal'], true)) {
                $query->where($table . '.' . $k, $v);
                continue;
            }
            $query = $this->addInsensitiveLike($query, $table . '.' . $k, $v);
        }

        return $query->limit(200)->get();
    }

    protected function extractJsonFromText(string $text)
    {
        preg_match('/\{.*\}/s', $text, $matches);
        if (!empty($matches[0])) {
            $decoded = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
        return null;
    }

    protected function interpretarTextoLocal(string $texto)
    {
        $lower = mb_strtolower($texto, 'UTF-8');
        $filters = [];
        $hint = null;

        if (preg_match('/\b(ci|c[íi]|identificaci[oó]n)\b/i', $texto) && preg_match('/(\d{4,})/', $texto, $matches)) {
            $filters['ci'] = $matches[1];
            return ['intent' => 'show_by_ci', 'entity' => 'postulante', 'filters' => $filters];
        }

        if (preg_match('/(?:llamad[oa]s?|se llaman|se llama|con nombre|nombre(?:s)?)(?:\s+a[l]?)?\s*([\p{L}]+(?:\s+[\p{L}]+){0,2})/iu', $texto, $matches)) {
            $name = trim($matches[1]);
            $parts = preg_split('/\s+/', $name);
            if (count($parts) === 1) {
                $filters['nombre'] = $parts[0];
            } else {
                $filters['nombre'] = $parts[0];
                $filters['apellido'] = implode(' ', array_slice($parts, 1));
            }
        }

        if (preg_match('/\b(?:postulante|estudiante|alumno|candidato|aspirante)s?\b\s+(?!por\b|de\b|en\b|con\b|llamado\b|llamada\b|llamados\b|llamadas\b|nombre\b)([\p{L}]+(?:\s+[\p{L}]+){0,2})/iu', $texto, $matches)) {
            $name = trim($matches[1]);
            $parts = preg_split('/\s+/', $name);
            if (count($parts) === 1) {
                $filters['nombre'] = $parts[0];
            } else {
                $filters['nombre'] = $parts[0];
                $filters['apellido'] = implode(' ', array_slice($parts, 1));
            }
        }

        if (preg_match('/\b(apellidos?|apellido)\b.*\b([\p{L} ]+)/iu', $texto, $matches)) {
            $filters['apellido'] = trim($matches[2]);
        }

        if (preg_match('/carrera(?:s)?(?: en| de)?\s+([\p{L} ]+)/iu', $texto, $matches)) {
            $filters['carrera'] = trim($matches[1]);
        }

        if (preg_match('/\b(materia|materias)\b(?:\s+(?:de|en|:)?\s*([\p{L}0-9 ]+))/iu', $texto, $matches) && !empty(trim($matches[2]))) {
            $filters['materia'] = trim($matches[2]);
        }

        if (preg_match('/\b(docente|docentes)\b(?:\s+(?:de|en|:)?\s*([\p{L} ]+))/iu', $texto, $matches) && !empty(trim($matches[2]))) {
            $filters['docente'] = trim($matches[2]);
        }

        if (preg_match('/\b(aula|aulas)\b(?:\s+(?:n(?:ro|º)?|de|:)?\s*([\p{L}0-9 ]+))/iu', $texto, $matches) && !empty(trim($matches[2]))) {
            $filters['aula'] = trim($matches[2]);
        }

        if (preg_match('/\b(grupo|grupos)\b(?:\s+(?:n(?:ro|º)?|de|:)?\s*([\p{L}0-9 ]+))/iu', $texto, $matches) && !empty(trim($matches[2]))) {
            $filters['grupo'] = trim($matches[2]);
        }

        if (preg_match('/\b(turno|turnos)\b(?:\s+(?:de|:)?\s*([\p{L} ]+))/iu', $texto, $matches) && !empty(trim($matches[2]))) {
            $filters['turno'] = trim($matches[2]);
        }

        if (preg_match('/\b(gestion|gestiones)\b(?:\s+(?:de|:)?\s*([\p{L}0-9 ]+))/iu', $texto, $matches) && !empty(trim($matches[2]))) {
            $filters['gestion'] = trim($matches[2]);
        }

        if (preg_match('/(?:entre|desde)\s+(\d{4}-\d{2}-\d{2}|\d{1,2}\/\d{1,2}\/\d{2,4})\s+(?:y|hasta)\s+(\d{4}-\d{2}-\d{2}|\d{1,2}\/\d{1,2}\/\d{2,4})/iu', $texto, $matches)) {
            $filters['fecha_inicio'] = trim($matches[1]);
            $filters['fecha_fin'] = trim($matches[2]);
        } elseif (preg_match('/\b(?:en|el|fecha)\s+(\d{4}-\d{2}-\d{2}|\d{1,2}\/\d{1,2}\/\d{2,4})/iu', $texto, $matches)) {
            $filters['fecha'] = trim($matches[1]);
        }

        if (preg_match('/(?:nota(?:s)?|promedio(?: final)?)(?:s)?\s*(?:mayor(?:es)? a|superior a|>=|>)\s*(\d{1,3})/iu', $texto, $matches)) {
            $filters['nota_min'] = (int) $matches[1];
        }

        if (preg_match('/\b(?:sin pagar|no pagaron|no ha[n]? pagado|no pag[oó]|no efectu[oó] pago)\b/iu', $texto)) {
            $filters['pago_confirmado'] = false;
        } elseif (preg_match('/\b(?:pagaron|pagado|con pago|ya pag[oó]n)\b/iu', $texto)) {
            $filters['pago_confirmado'] = true;
        }

        if (preg_match('/\b(reprobad[oa]s?|reprobado)\b/i', $texto)) {
            return ['intent' => 'show_reprobados', 'entity' => 'reprobado', 'filters' => $filters];
        }

        if (preg_match('/\b(aprobad[oa]s?|aprobados|aprobada)\b/i', $texto)) {
            return ['intent' => 'show_approved', 'entity' => 'admitido', 'filters' => $filters];
        }

        if (preg_match('/\b(admitid[oa]s?|admitidos|admitida)\b/i', $texto)) {
            return ['intent' => 'show_admitted', 'entity' => 'admitido', 'filters' => $filters];
        }

        if (preg_match('/\b(carrera(?:s)?)(?: en| de)?\s*[\p{L} ]*/iu', $texto) && !preg_match('/\b(postulantes|estudiantes|alumnos)\b/i', $texto)) {
            return ['intent' => 'show_carreras', 'entity' => 'carrera', 'filters' => $filters];
        }

        if (preg_match('/\b(postulantes|estudiantes|alumnos|candidatos|aspirantes)\b/i', $texto)) {
            if (preg_match('/\b(estudiantes|alumnos)\b/i', $texto)) {
                $hint = 'Se interpretó "estudiantes" como postulantes.';
            }
            return ['intent' => 'show_students', 'entity' => 'postulante', 'filters' => $filters, 'hint' => $hint];
        }

        // Detectar consultas sobre carga horaria o horario de docentes
        if (preg_match('/\b(carga horaria|carga de horas|horario de los docentes|carga horaria de docentes|horas de los docentes|carga horaria de (?:el|los) docente?s?)\b/iu', $texto)
            || (preg_match('/\b(docentes|profesores|docente)s?\b/iu', $texto) && preg_match('/\b(horario|carga horaria|horas)\b/iu', $texto))) {
            // Extraer nombre de docente si existe
            if (preg_match('/(?:docente|profesor|profesores)\s+([\p{L} ]{2,50})/iu', $texto, $m)) {
                $filters['docente'] = trim($m[1]);
            } elseif (!empty($filters['nombre']) || !empty($filters['apellido'])) {
                $filters['docente'] = trim(($filters['nombre'] ?? '') . ' ' . ($filters['apellido'] ?? ''));
            }
            return ['intent' => 'show_docentes_horario', 'entity' => 'docente_horario', 'filters' => $filters];
        }

        $tableMap = [
            'postulantes' => 'postulantes',
            'postulante' => 'postulantes',
            'carreras' => 'carreras',
            'carrera' => 'carreras',
            'materias' => 'materias',
            'materia' => 'materias',
            'docentes' => 'docentes',
            'docente' => 'docentes',
            'pagos' => 'pagos',
            'pago' => 'pagos',
            'notas' => 'notas',
            'nota' => 'notas',
            'admitidos' => 'admitidos',
            'admitido' => 'admitidos',
            'reprobados' => 'reprobados',
            'reprobado' => 'reprobados',
            'grupos' => 'grupos',
            'grupo' => 'grupos',
            'horarios' => 'horarios',
            'horario' => 'horarios',
            'aulas' => 'aulas',
            'aula' => 'aulas',
            'gestiones' => 'gestiones',
            'gestion' => 'gestiones',
            'turnos' => 'turnos',
            'turno' => 'turnos',
            'asignaciones' => 'asignaciones',
            'asignacion' => 'asignaciones',
            'bitacoras' => 'bitacoras',
            'bitacora' => 'bitacoras',
            'users' => 'users',
            'usuarios' => 'users',
        ];

        foreach ($tableMap as $keyword => $tableName) {
            if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $texto)) {
                $filters = array_merge($filters, $this->extractColumnFiltersForTable($texto, $tableName));
                return ['table' => $tableName, 'filters' => $filters];
            }
        }

        return ['intent' => 'invalid', 'entity' => null, 'filters' => []];
    }

    protected function extractColumnFiltersForTable(string $texto, string $table): array
    {
        $columns = Schema::getColumnListing($table);
        $filters = [];

        $synonyms = [
            'nombres' => ['nombre', 'nombres'],
            'apellidos' => ['apellido', 'apellidos'],
            'fecha_nac' => ['fecha de nacimiento', 'fecha nacimiento', 'nacimiento', 'fecha_nac'],
            'pago_confirmado' => ['pago confirmado', 'pagado', 'no pagado', 'sin pagar', 'no pagaron', 'confirmado', 'pendiente'],
            'correo' => ['correo', 'email', 'e-mail', 'mail'],
            'telefono' => ['telefono', 'teléfono', 'celular', 'movil', 'móvil'],
            'ci' => ['ci', 'cedula', 'cédula', 'identificacion', 'identificación'],
            'estado' => ['estado', 'estados'],
        ];

        $relationIdKeys = [
            'carrera_id' => 'carrera',
            'materia_id' => 'materia',
            'docente_id' => 'docente',
            'aula_id' => 'aula',
            'dia_id' => 'dia',
            'horario_id' => 'horario',
            'grupo_id' => 'grupo',
            'gestion_id' => 'gestion',
            'postulante_id' => 'postulante',
            'config_examen_id' => 'config_examen',
        ];

        foreach ($columns as $column) {
            if (in_array($column, ['carrera1_id', 'carrera2_id'], true)) {
                continue;
            }

            $terms = [$column];
            if (isset($synonyms[$column])) {
                $terms = array_merge($terms, $synonyms[$column]);
            }

            if (strpos($column, '_') !== false) {
                $terms[] = str_replace('_', ' ', $column);
                if (substr($column, -3) === '_id') {
                    $base = substr($column, 0, -3);
                    $terms[] = $base;
                    $terms[] = rtrim($base, 's');
                }
            }

            $terms = array_unique($terms);

            foreach ($terms as $term) {
                $term = preg_quote($term, '/');

                if (preg_match('/\b' . $term . '\b\s*(?:>=|<=|>|<|=)\s*(\d+(?:\.\d+)?)/iu', $texto, $matches)) {
                    $filters[$column] = trim($matches[0]);
                    break;
                }

                if (preg_match('/\b' . $term . '\b\s*(?:mayor(?:es)? a|superior a)\s*(\d+(?:\.\d+)?)/iu', $texto, $matches)) {
                    $filters[$column] = '>' . trim($matches[1]);
                    break;
                }

                if (preg_match('/\b' . $term . '\b\s*(?:menor(?:es)? a|inferior a)\s*(\d+(?:\.\d+)?)/iu', $texto, $matches)) {
                    $filters[$column] = '<' . trim($matches[1]);
                    break;
                }

                if (preg_match('/\b' . $term . '\b\s+(\d+(?:\.\d+)?)/iu', $texto, $matches)) {
                    $filters[$column] = trim($matches[1]);
                    break;
                }

                $pattern = '/\b' . $term . '\b(?:\s*(?:es|=|:|de|del|de la|de el|de los|de las|en|con)\s*)([\p{L}0-9áéíóúÁÉÍÓÚñÑ\-\. ]+)/iu';
                if (preg_match($pattern, $texto, $matches)) {
                    $value = trim($matches[1]);
                    if ($value !== '') {
                        if (substr($column, -3) === '_id' && !is_numeric($value)) {
                            $relationKey = $relationIdKeys[$column] ?? substr($column, 0, -3);
                            $filters[$relationKey] = $value;
                        } else {
                            $filters[$column] = $value;
                        }
                        break;
                    }
                }
            }
        }

        return $filters;
    }

    protected function addInsensitiveLike($query, string $column, string $value)
    {
        return $query->whereRaw($column . ' LIKE ? COLLATE utf8mb4_general_ci', ['%' . trim($value) . '%']);
    }

    protected function queryPostulantes(array $filters)
    {
        $query = Postulante::query();
        $query = $this->applyFiltersToQuery($query, $filters, 'postulante');
        return $query->with(['carrera1', 'carrera2', 'gestion'])->get();
    }

    protected function queryAdmitidos(array $filters)
    {
        $query = Admitido::with(['postulante', 'carrera']);
        $query = $this->applyFiltersToQuery($query, $filters, 'admitido');
        return $query->get();
    }

    protected function queryAprobados(array $filters)
    {
        $query = Admitido::with(['postulante', 'carrera']);
        $query = $this->applyFiltersToQuery($query, $filters, 'admitido');
        return $query->get();
    }

    protected function queryCarreras(array $filters)
    {
        $query = Carrera::query();
        if (!empty($filters['carrera'])) {
            $query = $this->addInsensitiveLike($query, 'nombre', $filters['carrera']);
        }

        $columns = Schema::getColumnListing('carreras');
        foreach ($filters as $key => $value) {
            if ($key === 'carrera' || empty($value) || !in_array($key, $columns, true)) {
                continue;
            }
            if (is_string($value) && preg_match('/^(>=|<=|>|<|=)\s*(\d+(?:\.\d+)?)$/', trim($value), $matches)) {
                $query->where($key, $matches[1], $matches[2]);
                continue;
            }
            $query = $this->addInsensitiveLike($query, $key, $value);
        }

        return $query->get();
    }

    protected function queryDocentesHorario(array $filters)
    {
        $query = \App\Models\Asignacion::with(['docente', 'grupo', 'materia', 'aula', 'dia', 'horario']);

        if (!empty($filters['docente'])) {
            $name = $filters['docente'];
            $query->whereHas('docente', function ($q) use ($name) {
                $q = $this->addInsensitiveLike($q, 'nombre', $name);
            });
        } elseif (!empty($filters['nombre'])) {
            $query->whereHas('docente', function ($q) use ($filters) {
                $q = $this->addInsensitiveLike($q, 'nombre', $filters['nombre']);
            });
        }

        return $query->get();
    }

    /**
     * Aplica filtros comunes a una consulta de postulantes o entidades relacionadas.
     * Garantiza que las condiciones de carrera estén agrupadas correctamente para evitar OR globales.
     */
    protected function applyFiltersToQuery($query, array $filters, $entity = 'postulante')
    {
        if (!empty($filters['ci'])) {
            if ($entity === 'postulante') {
                $query->whereRaw('ci LIKE ? COLLATE utf8mb4_general_ci', ['%' . $filters['ci'] . '%']);
            } else {
                $query->whereHas('postulante', function ($q) use ($filters) {
                    $q->whereRaw('ci LIKE ? COLLATE utf8mb4_general_ci', ['%' . $filters['ci'] . '%']);
                });
            }
        }

        if (!empty($filters['nombre'])) {
            if ($entity === 'postulante') {
                $query = $this->addInsensitiveLike($query, 'nombres', $filters['nombre']);
            } else {
                $query->whereHas('postulante', function ($q) use ($filters) {
                    $q = $this->addInsensitiveLike($q, 'nombres', $filters['nombre']);
                });
            }
        }

        if (!empty($filters['apellido'])) {
            if ($entity === 'postulante') {
                $query = $this->addInsensitiveLike($query, 'apellidos', $filters['apellido']);
            } else {
                $query->whereHas('postulante', function ($q) use ($filters) {
                    $q = $this->addInsensitiveLike($q, 'apellidos', $filters['apellido']);
                });
            }
        }

        if (isset($filters['pago_confirmado'])) {
            $paymentValue = $filters['pago_confirmado'];
            if (is_string($paymentValue)) {
                $paymentValue = in_array(mb_strtolower($paymentValue, 'UTF-8'), ['1', 'true', 'si', 'sí', 'yes'], true);
            }
            if ($entity === 'postulante') {
                $query->where('pago_confirmado', $paymentValue ? 1 : 0);
            } else {
                $query->whereHas('postulante', function ($q) use ($paymentValue) {
                    $q->where('pago_confirmado', $paymentValue ? 1 : 0);
                });
            }
        }

        if (!empty($filters['nota_min']) && is_numeric($filters['nota_min'])) {
            if ($entity === 'postulante') {
                $query->whereIn('id', function ($sub) use ($filters) {
                    $sub->select('postulante_id')
                        ->from('notas')
                        ->groupBy('postulante_id')
                        ->havingRaw('AVG(nota) >= ?', [$filters['nota_min']]);
                });
            } else {
                // Para admitidos se usa promedio_final
                $query->where(function ($q) use ($filters) {
                    $q->where('promedio_final', '>=', $filters['nota_min'])
                      ->orWhereHas('postulante', function ($pq) use ($filters) {
                          $pq->whereIn('id', function ($sub) use ($filters) {
                              $sub->select('postulante_id')
                                  ->from('notas')
                                  ->groupBy('postulante_id')
                                  ->havingRaw('AVG(nota) >= ?', [$filters['nota_min']]);
                          });
                      });
                });
            }
        }

        if (!empty($filters['carrera'])) {
            // Agrupar condiciones de carrera para evitar que un or global anule otros where
            if ($entity === 'postulante') {
                $query->where(function ($q) use ($filters) {
                    $q->whereHas('carrera1', function ($cq) use ($filters) {
                        $cq->whereRaw('nombre LIKE ? COLLATE utf8mb4_general_ci', ['%' . $filters['carrera'] . '%']);
                    })->orWhereHas('carrera2', function ($cq) use ($filters) {
                        $cq->whereRaw('nombre LIKE ? COLLATE utf8mb4_general_ci', ['%' . $filters['carrera'] . '%']);
                    });
                });
            } else {
                $query->whereHas('carrera', function ($cq) use ($filters) {
                    $cq->whereRaw('nombre LIKE ? COLLATE utf8mb4_general_ci', ['%' . $filters['carrera'] . '%']);
                })->orWhereHas('postulante', function ($pq) use ($filters) {
                    $pq->where(function ($q2) use ($filters) {
                        $q2->whereHas('carrera1', function ($c1) use ($filters) {
                            $c1->whereRaw('nombre LIKE ? COLLATE utf8mb4_general_ci', ['%' . $filters['carrera'] . '%']);
                        })->orWhereHas('carrera2', function ($c2) use ($filters) {
                            $c2->whereRaw('nombre LIKE ? COLLATE utf8mb4_general_ci', ['%' . $filters['carrera'] . '%']);
                        });
                    });
                });
            }
        }

        $table = $query->getModel()->getTable();
        $columns = Schema::getColumnListing($table);
        $handledFilters = ['ci', 'nombre', 'nombres', 'apellido', 'apellidos', 'pago_confirmado', 'nota_min', 'carrera'];

        foreach ($filters as $key => $value) {
            if (in_array($key, $handledFilters, true) || empty($value)) {
                continue;
            }
            if (!in_array($key, $columns, true)) {
                continue;
            }

            if (is_string($value) && preg_match('/^(>=|<=|>|<|=)\s*(\d+(?:\.\d+)?)$/', trim($value), $matches)) {
                $query->where($table . '.' . $key, $matches[1], $matches[2]);
                continue;
            }

            if (is_string($value) && strpos($value, '..') !== false) {
                [$from, $to] = explode('..', $value, 2);
                $query->whereBetween($table . '.' . $key, [trim($from), trim($to)]);
                continue;
            }

            $query = $this->addInsensitiveLike($query, $table . '.' . $key, $value);
        }

        return $query;
    }

    protected function queryReprobados(array $filters)
    {
        $query = Reprobado::with('postulante');
        $query = $this->applyFiltersToQuery($query, $filters, 'reprobado');
        return $query->get();
    }

    public function datosTabla(Request $request)
    {
        $tables = [
            'users' => 'Usuarios',
            'grupos' => 'Grupos',
            'aulas' => 'Aulas',
            'carreras' => 'Carreras',
            'horarios' => 'Horarios',
            'dias' => 'Días',
            'gestiones' => 'Gestiones',
            'materias' => 'Materias',
            'turnos' => 'Turnos',
            'docentes' => 'Docentes',
            'postulantes' => 'Postulantes',
            'pagos' => 'Pagos',
            'notas' => 'Notas',
            'admitidos' => 'Admitidos',
            'reprobados' => 'Reprobados',
            'post_grupos' => 'Post Grupos',
        ];

        $selectedTable = $request->query('table', array_key_first($tables));
        if (!isset($tables[$selectedTable])) {
            $selectedTable = array_key_first($tables);
        }

        $columns = Schema::getColumnListing($selectedTable);
        $selectedColumns = $request->query('columns', $columns);
        $selectedColumns = array_values(array_intersect($selectedColumns, $columns));
        if (empty($selectedColumns)) {
            $selectedColumns = $columns;
        }

        $rows = DB::table($selectedTable)->select($selectedColumns)->get();

        return view('admin.reportes.datos_tabla', compact('tables', 'selectedTable', 'columns', 'selectedColumns', 'rows'))->with('tipo', 'datos_tabla');
    }

    public function generar(Request $request, $tipo)
    {
        switch ($tipo) {
            case 'lista':
                $postulantes = Postulante::orderBy('apellidos')->get();
                return view('admin.reportes.lista', ['postulantes' => $postulantes, 'tipo' => 'lista']);

            case 'aprobados':
                // Obtener aprobados desde la tabla Admitido y permitir búsqueda por postulante
                $aprobQuery = Admitido::with('postulante');
                if ($request->filled('q')) {
                    $term = $request->get('q');
                    $aprobQuery->whereHas('postulante', function ($q) use ($term) {
                        $q->where('nombres', 'like', "%{$term}%")
                          ->orWhere('apellidos', 'like', "%{$term}%")
                          ->orWhere('ci', 'like', "%{$term}%");
                    });
                }
                $aprobados = $aprobQuery->get()->pluck('promedio_final', 'postulante_id');
                $postulantes = Postulante::whereIn('id', $aprobados->keys())->orderBy('apellidos')->get();
                return view('admin.reportes.lista', ['postulantes' => $postulantes, 'titulo' => 'Postulantes aprobados', 'promedios' => $aprobados, 'tipo' => 'aprobados']);

            case 'reprobados':
                // Obtener reprobados desde la tabla Reprobado y aplicar filtro de búsqueda si viene q
                $reprobadosQuery = Reprobado::with('postulante');
                if ($request->filled('q')) {
                    $term = $request->get('q');
                    $reprobadosQuery->whereHas('postulante', function ($q) use ($term) {
                        $q->where('nombres', 'like', "%{$term}%")
                          ->orWhere('apellidos', 'like', "%{$term}%")
                          ->orWhere('ci', 'like', "%{$term}%");
                    });
                }
                $reprobados = $reprobadosQuery->get()->pluck('promedio_final', 'postulante_id');
                $postulantes = Postulante::whereIn('id', $reprobados->keys())->orderBy('apellidos')->get();
                return view('admin.reportes.lista', ['postulantes' => $postulantes, 'titulo' => 'Postulantes reprobados', 'promedios' => $reprobados, 'tipo' => 'reprobados']);

            case 'promedios':
                $promedioGlobal = DB::table('notas')->avg('nota');
                $promediosPorPost = DB::table('notas')
                    ->select('postulante_id', DB::raw('AVG(nota) as promedio'))
                    ->groupBy('postulante_id')
                    ->get();

                return view('admin.reportes.promedios', compact('promedioGlobal', 'promediosPorPost'))->with('tipo', 'promedios');

            case 'cantidad_grupos':
                $total = Postulante::count();
                $capacidad = Grupo::max('cupo_maximo') ?: 1;
                $gruposNecesarios = (int) ceil($total / $capacidad);
                return view('admin.reportes.cantidad_grupos', compact('total', 'capacidad', 'gruposNecesarios'))->with('tipo', 'cantidad_grupos');

            case 'estadisticas_materia':
                $materias = ['Computación', 'Matemáticas', 'Inglés', 'Física'];
                $stats = [];
                foreach ($materias as $mat) {
                    $q = DB::table('notas')
                        ->join('materias', 'notas.materia_id', '=', 'materias.id')
                        ->where('materias.nombre', 'like', "%{$mat}%");

                    $avg = $q->avg('notas.nota');
                    $total = $q->count();
                    $aprob = $q->where('notas.nota', '>=', 60)->count();
                    $stats[$mat] = ['promedio' => $avg ?: 0, 'total' => $total, 'aprobados' => $aprob];
                }
                return view('admin.reportes.estadisticas_materia', compact('stats'))->with('tipo', 'estadisticas_materia');

            case 'docentes_por_grupos':
                $asignaciones = Asignacion::with(['docente', 'grupo', 'materia', 'horario', 'aula', 'dia'])->get()->groupBy('grupo_id');
                return view('admin.reportes.docentes_por_grupos', compact('asignaciones'))->with('tipo', 'docentes_por_grupos');

            case 'grupos_mejor_rendimiento':
                $postGrupos = PostGrupo::with('postulante', 'grupo')->get();
                $grupos = [];
                foreach ($postGrupos as $pg) {
                    $pid = $pg->postulante_id;
                    $gid = $pg->grupo_id;
                    if (!isset($grupos[$gid])) {
                        $grupos[$gid] = ['grupo' => $pg->grupo->nombre ?? 'Grupo '.$gid, 'aprobados' => 0, 'total' => 0];
                    }
                    // Considerar aprobado si existe entrada en Admitido
                    if (Admitido::where('postulante_id', $pid)->exists()) {
                        $grupos[$gid]['aprobados']++;
                    }
                    $grupos[$gid]['total']++;
                }
                usort($grupos, function ($a, $b) { return $b['aprobados'] <=> $a['aprobados']; });
                return view('admin.reportes.grupos_mejor_rendimiento', compact('grupos'))->with('tipo', 'grupos_mejor_rendimiento');

            default:
                abort(404);
        }
    }

    public function export(Request $request, $tipo)
    {
        $format = $request->query('format', 'csv');

        // Reuse generar to get data via internal call-type handling
        // For simplicity call same logic and then export the rendered view or CSV
        $headers = [];
        $rows = [];
        $titulo = ucfirst(str_replace('_', ' ', $tipo));

        switch ($tipo) {
            case 'lista':
                $postulantes = Postulante::orderBy('apellidos')->get();
                $headers = ['ID', 'Nombres', 'Apellidos', 'CI'];
                $rows = $postulantes->map(function ($p) {
                    return [$p->id, $p->nombres ?? '', $p->apellidos ?? '', $p->ci ?? ''];
                })->toArray();
                break;

            case 'aprobados':
            case 'reprobados':
                $proms = DB::table('notas')
                    ->select('postulante_id', DB::raw('AVG(nota) as promedio'))
                    ->groupBy('postulante_id')
                    ->get();
                $filtered = $proms->filter(function ($r) use ($tipo) {
                    return $tipo == 'aprobados' ? $r->promedio >= 60 : $r->promedio < 60;
                });
                $postulantes = Postulante::whereIn('id', $filtered->pluck('postulante_id'))->orderBy('apellidos')->get();
                $headers = ['ID', 'Nombres', 'Apellidos', 'Promedio'];
                $rows = $postulantes->map(function ($p) use ($filtered) {
                    $prom = $filtered->firstWhere('postulante_id', $p->id)->promedio ?? 0;
                    return [$p->id, $p->nombres ?? '', $p->apellidos ?? '', round($prom, 2)];
                })->toArray();
                break;

            case 'promedios':
                $promediosPorPost = DB::table('notas')
                    ->select('postulante_id', DB::raw('AVG(nota) as promedio'))
                    ->groupBy('postulante_id')
                    ->get();
                $headers = ['ID', 'Nombres', 'Apellidos', 'Promedio'];
                $rows = $promediosPorPost->map(function ($item) {
                    $post = Postulante::find($item->postulante_id);
                    return [
                        optional($post)->id ?? $item->postulante_id,
                        optional($post)->nombres ?? '',
                        optional($post)->apellidos ?? '',
                        round($item->promedio, 2),
                    ];
                })->toArray();
                break;

            case 'cantidad_grupos':
                $total = Postulante::count();
                $capacidad = Grupo::max('cupo_maximo') ?: 1;
                $gruposNecesarios = (int) ceil($total / $capacidad);
                $headers = ['Total inscritos', 'Capacidad por grupo', 'Grupos necesarios'];
                $rows = [[$total, $capacidad, $gruposNecesarios]];
                break;

            case 'estadisticas_materia':
                $materias = ['Computación', 'Matemáticas', 'Inglés', 'Física'];
                $headers = ['Materia', 'Promedio', 'Total notas', 'Aprobados'];
                foreach ($materias as $mat) {
                    $q = DB::table('notas')
                        ->join('materias', 'notas.materia_id', '=', 'materias.id')
                        ->where('materias.nombre', 'like', "%{$mat}%");
                    $avg = $q->avg('notas.nota');
                    $total = $q->count();
                    $aprob = $q->where('notas.nota', '>=', 60)->count();
                    $rows[] = [
                        $mat,
                        round($avg ?: 0, 2),
                        $total,
                        $aprob,
                    ];
                }
                break;

            case 'docentes_por_grupos':
                $asignaciones = Asignacion::with(['docente', 'grupo', 'materia', 'aula', 'dia', 'horario'])->get();
                $headers = ['Grupo', 'Docente', 'Materia', 'Aula', 'Día', 'Horario'];
                foreach ($asignaciones as $a) {
                    $rows[] = [
                        optional($a->grupo)->nombre ?? '',
                        optional($a->docente)->nombre ?? '',
                        optional($a->materia)->nombre ?? '',
                        optional($a->aula)->nombre ?? '',
                        optional($a->dia)->nombre ?? '',
                        (optional($a->horario)->horaInicio ?? '') . ' - ' . (optional($a->horario)->horaFin ?? ''),
                    ];
                }
                break;

            case 'grupos_mejor_rendimiento':
                $postGrupos = PostGrupo::with('postulante', 'grupo')->get();
                $grupos = [];
                foreach ($postGrupos as $pg) {
                    $pid = $pg->postulante_id;
                    $gid = $pg->grupo_id;
                    $prom = DB::table('notas')->where('postulante_id', $pid)->avg('nota');
                    if (!isset($grupos[$gid])) {
                        $grupos[$gid] = ['grupo' => $pg->grupo->nombre ?? 'Grupo '.$gid, 'aprobados' => 0, 'total' => 0];
                    }
                    if ($prom >= 60) {
                        $grupos[$gid]['aprobados']++;
                    }
                    $grupos[$gid]['total']++;
                }
                usort($grupos, function ($a, $b) { return $b['aprobados'] <=> $a['aprobados']; });
                $headers = ['Grupo', 'Aprobados', 'Total'];
                foreach ($grupos as $g) {
                    $rows[] = [$g['grupo'], $g['aprobados'], $g['total']];
                }
                break;

            default:
                return back()->with('error', 'Tipo de reporte no soportado');
        }

        if ($format === 'csv') {
            $filename = "report_{$tipo}_".date('Ymd_His').".csv";
            $callback = function() use ($rows, $headers) {
                $f = fopen('php://output', 'w');
                fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF));
                fputcsv($f, $headers);
                foreach ($rows as $row) {
                    fputcsv($f, $row);
                }
                fclose($f);
            };
            return response()->streamDownload($callback, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        if ($format === 'pdf') {
            $data = ['headers' => $headers, 'rows' => $rows, 'titulo' => $titulo];
            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                return \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reportes.pdf_simple', $data)->download("report_{$tipo}_".date('Ymd_His').".pdf");
            }
            if (app()->bound('dompdf.wrapper')) {
                $pdf = app('dompdf.wrapper')->loadView('admin.reportes.pdf_simple', $data);
                return $pdf->download("report_{$tipo}_".date('Ymd_His').".pdf");
            }
            return back()->with('error', 'PDF no disponible en este entorno.');
        }

        return back()->with('error', 'Formato no soportado');
    }
}
