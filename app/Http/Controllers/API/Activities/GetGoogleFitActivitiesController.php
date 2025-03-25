<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Activities;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GetGoogleFitActivitiesController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $client_id = 'SEU_CLIENT_ID'; // Substitua pelo seu Client ID
        $client_secret = 'SEU_CLIENT_SECRET'; // Substitua pelo seu Client Secret
        $access_token = 'SEU_ACCESS_TOKEN'; // Obtido após autenticação OAuth 2.0

        $before_date = '2026-01-01'; // Pegar atividades antes de 2026 (limite superior)
        $after_date = '2025-01-01';  // Pegar atividades após o início de 2025
        $sort = 'asc';               // Ordenar por data crescente
        $limit = 50;                 // Número máximo de atividades por chamada (máx. 50)
        $offset = 0;                 // Para paginação, começa em 0

        $api_url = "https://api.fitbit.com/1/user/-/activities/list.json?afterDate={$after_date}&sort={$sort}&limit={$limit}&offset={$offset}";

        // Obter os dados
        $data = $this->getFitbitData($api_url, $access_token);
        dd($data);
        // Verificar e exibir atividades de 2025
        if (isset($data['activities']) && !empty($data['activities'])) {
            foreach ($data['activities'] as $activity) {
                $start_time = $activity['startTime']; // Data e hora da atividade
                $activity_date = substr($start_time, 0, 10); // Extrai apenas a data (YYYY-MM-DD)

                // Filtrar apenas atividades de 2025
                if (strpos($activity_date, '2025') === 0) {
                    $activity_name = $activity['activityName'] ?? 'Atividade sem nome';
                    $duration = $activity['duration'] / 60000; // Converter de milissegundos para minutos
                    $calories = $activity['calories'] ?? 'N/A';
                    $distance = $activity['distance'] ?? 'N/A'; // Pode não estar disponível para todas as atividades

                    echo "Data: $activity_date<br>";
                    echo "Atividade: $activity_name<br>";
                    echo "Duração: $duration minutos<br>";
                    echo "Calorias: $calories<br>";
                    echo "Distância: $distance km<br>";
                    echo "---------------------<br>";
                }
            }
        } else {
            return JsonResponse(["Nenhuma atividade encontrada para 2025 ou erro na requisição."]);
        }

        // Para paginação (se houver mais de 50 atividades)
        if (isset($data['pagination']['next']) && !empty($data['pagination']['next'])) {
            return JsonResponse(["Há mais atividades. Ajuste o 'offset' para continuar.<br>"]);
        }
    }

    public function getFitbitData($url, $token): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Accept: application/json"
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
