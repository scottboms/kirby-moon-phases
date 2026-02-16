<?php

declare(strict_types=1);

namespace ScottBoms\MoonPhases;

use DateTimeImmutable;
use DateTimeZone;
use Kirby\Cache\Cache;
use Kirby\Cms\App as Kirby;
use Throwable;

class MoonPhasesService
{
  private Kirby $kirby;
  private array $options;
  private ProviderInterface $provider;
  private IconResolver $iconResolver;
  private MoonMath $moonMath;

  public function __construct(
    Kirby $kirby,
    array $options,
    ProviderInterface $provider,
    IconResolver $iconResolver,
    MoonMath $moonMath
  ) {
    $this->kirby = $kirby;
    $this->options = $options;
    $this->provider = $provider;
    $this->iconResolver = $iconResolver;
    $this->moonMath = $moonMath;
  }

  public function getMoonData(): array
  {
    $cache = $this->cache();
    $ttl = (int)($this->options['cache']['ttl'] ?? 60);
    $cacheKey = $this->cacheKey();
    $liveCacheKey = $cacheKey . '.live';
    $lastCacheKey = $cacheKey . '.last';

    $cached = $cache->get($liveCacheKey);
    if (is_array($cached)) {
      return $cached;
    }

    try {
      $remote = $this->provider->fetch($this->options);
      $data = $this->buildMoonData($remote, false, null);

      $cache->set($liveCacheKey, $data, max(1, $ttl));
      $cache->set($lastCacheKey, $data, max(1440, $ttl));

      return $data;
    } catch (Throwable $error) {
      $fallback = $cache->get($lastCacheKey);
      if (is_array($fallback)) {
        $fallback['isFallback'] = true;
        $fallback['error'] = $error->getMessage();
        return $fallback;
      }

      return $this->buildMoonData([], true, $error->getMessage());
    }
  }

  private function buildMoonData(array $providerData, bool $isFallback, ?string $error): array
  {
    $timezone = (string)($this->options['location']['timezone'] ?? 'UTC');
    $dateString = (string)($providerData['date'] ?? gmdate('Y-m-d'));
    $date = new DateTimeImmutable($dateString . ' 12:00:00', new DateTimeZone($timezone));
    $age = $this->moonMath->moonAgeDays($date);
    $phaseKey = $this->moonMath->phaseKey($age);
    $phaseLabel = $this->normalizePhaseLabel(
        (string)($providerData['phaseLabel'] ?? $this->moonMath->phaseLabel($phaseKey))
    );
    $iconSvg = $this->iconResolver->resolve($phaseKey, $this->options['icons'] ?? []);
        $illumination = $providerData['illumination'] ?? 0;

    return [
      'phase' => $phaseKey,
      'phaseLabel' => $phaseLabel,
            'illumination' => is_numeric($illumination) ? (float)$illumination : 0.0,
      'daysUntilFullMoon' => $this->moonMath->daysUntilFullMoon($age),
      'daysUntilNewMoon' => $this->moonMath->daysUntilNewMoon($age),
      'iconSvg' => $iconSvg,
      'fetchedAt' => gmdate('c'),
      'source' => (string)($providerData['source'] ?? 'computed'),
      'isFallback' => $isFallback,
      'error' => $error,
      'raw' => $providerData['raw'] ?? null,
    ];
  }

  private function cache(): Cache
  {
    return $this->kirby->cache('scottboms.moon-phases');
  }

  private function cacheKey(): string
  {
    $location = $this->options['location'] ?? [];
    $lat = (string)($location['lat'] ?? '');
    $lng = (string)($location['lng'] ?? '');
    $timezone = (string)($location['timezone'] ?? 'UTC');

    return 'moon.' . md5($lat . '|' . $lng . '|' . $timezone);
  }

    private function normalizePhaseLabel(string $label): string
    {
        $normalized = str_replace('_', ' ', trim($label));
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;
        $normalized = strtolower($normalized);
        $normalized = ucwords($normalized);

        return $normalized;
    }
}
