<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;

class Position
{
    /**
     * @return mixed
     */
    public static function all(): mixed
    {
        $apiUrl = env('API_URL');
        $response = Http::get($apiUrl . '/positions');

        if ($response->successful()) {
            return $response->json()['positions'] ?? [];
        }

        return [];
    }
}
