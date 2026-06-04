<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MetrikaService
{
    protected $clientId;
    protected $clientSecret;
    protected $redirectUri;

    public function __construct()
    {
        $this->clientId = config('services.yandex.client_id');
        $this->clientSecret = config('services.yandex.client_secret');
        $this->redirectUri = config('services.yandex.redirect_uri');
    }

    /**
     * URL для редиректа на авторизацию Яндекс
     */
    public function getAuthUrl(): string
    {
        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
        ];
        return 'https://oauth.yandex.ru/authorize?' . http_build_query($params);
    }

    /**
     * Обменять код на токен
     */
    public function getToken(string $code): array
    {
        $response = Http::asForm()->post('https://oauth.yandex.ru/token', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
        ]);

        if ($response->failed()) {
            throw new \Exception('Ошибка получения токена: ' . $response->body());
        }

        $data = $response->json();

        return [
            'access_token' => $data['access_token'],
            'expires_in' => $data['expires_in'],
        ];
    }

    /**
     * Получить список счётчиков
     */
    public function getCounters(string $accessToken): array
    {
        $response = Http::withToken($accessToken)
            ->get('https://api-metrika.yandex.net/management/v1/counters');

        if ($response->failed()) {
            throw new \Exception('Ошибка получения счётчиков: ' . $response->body());
        }

        return $response->json()['counters'] ?? [];
    }

    /**
     * Получить данные по выбранной метрике за период
     */
    public function getStat(string $counterId, string $accessToken, string $date1, string $date2, string $metric): float
    {
        $response = Http::withToken($accessToken)
            ->get('https://api-metrika.yandex.net/stat/v1/data', [
                'ids' => $counterId,
                'date1' => $date1,
                'date2' => $date2,
                'metrics' => $metric,
                'accuracy' => 'full',
            ]);

        if ($response->failed()) {
            throw new \Exception('Ошибка получения статистики: ' . $response->body());
        }

        $data = $response->json();
        return (float) ($data['data'][0]['metrics'][0][0] ?? 0);
    }
}
