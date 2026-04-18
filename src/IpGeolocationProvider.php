<?php

declare(strict_types=1);

namespace Scottboms\MoonPhases;

use Exception;
use Kirby\Http\Remote;
use RuntimeException;

class IpGeolocationProvider implements ProviderInterface
{
    public function fetch(array $options = []): array
    {
        $apiOptions = $options['api'] ?? [];
        $apiKey = (string)option('scottboms.moon-phases.api.key', $apiOptions['key'] ?? '');
        if ($apiKey === '') {
            throw new RuntimeException('Missing API key. Set scottboms.moon-phases.api.key in config.');
        }

        $baseUrl = (string)option('scottboms.moon-phases.api.baseUrl', $apiOptions['baseUrl'] ?? 'https://api.ipgeolocation.io/astronomy');
        $timeout = (int)option('scottboms.moon-phases.api.timeout', $apiOptions['timeout'] ?? 8);
        $location = $options['location'] ?? [];
        $timezone = (string)option('scottboms.moon-phases.location.timezone', $location['timezone'] ?? 'UTC');

        $params = [
            'apiKey' => $apiKey,
            'output' => 'json',
            'timezone' => $timezone,
            'date' => gmdate('Y-m-d'),
        ];

        $lat = option('scottboms.moon-phases.location.lat', $location['lat'] ?? null);
        if ($lat !== null && $lat !== '') {
            $params['lat'] = (string)$lat;
        }

        $lng = option('scottboms.moon-phases.location.lng', $location['lng'] ?? null);
        if ($lng !== null && $lng !== '') {
            $params['long'] = (string)$lng;
        }

        $response = Remote::get($baseUrl, [
            'data' => $params,
            'timeout' => $timeout,
        ]);

      if ($response->code() !== 200) {
        throw new RuntimeException('Moon API request failed with HTTP ' . $response->code() . '.');
      }

      try {
        $payload = $response->json(true);
      } catch (Exception $e) {
        throw new RuntimeException('Moon API returned invalid JSON.', 0, $e);
      }

      if (!is_array($payload)) {
        throw new RuntimeException('Moon API returned an unexpected response shape.');
      }

      $date = (string)($payload['date'] ?? gmdate('Y-m-d'));
      $phaseLabel = (string)($payload['moon_phase'] ?? '');
      $illuminationRaw = $payload['moon_illumination_percentage'] ?? $payload['moon_illumination'] ?? null;

      return [
        'date' => $date,
        'phaseLabel' => $phaseLabel !== '' ? $phaseLabel : null,
        'illumination' => $this->normalizeIllumination($illuminationRaw),
        'source' => 'ipgeolocation',
        'raw' => $payload,
      ];
    }

    private function normalizeIllumination(mixed $value): ?float
    {
        if (!is_numeric($value)) {
            return null;
        }

        $illumination = (float)$value;

        // Some payloads expose illumination as a fraction (0.21) instead of a percent (21.0).
        if ($illumination >= 0.0 && $illumination <= 1.0) {
            return $illumination * 100.0;
        }

        return $illumination;
    }
}
