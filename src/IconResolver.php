<?php

declare(strict_types=1);

namespace ScottBoms\MoonPhases;

class IconResolver
{
  public function resolve(string $phaseKey, array $iconOptions = []): ?string
  {
    $mode = (string)($iconOptions['mode'] ?? 'preset');
    if ($mode === 'custom') {
      $custom = $this->resolveCustom($phaseKey, $iconOptions);
      if ($custom !== null) {
        return $custom;
      }
    }

    return $this->preset($phaseKey);
  }

  private function resolveCustom(string $phaseKey, array $iconOptions): ?string
  {
    $path = (string)($iconOptions['path'] ?? '');
    if ($path === '') {
      return null;
    }

    $map = $iconOptions['map'] ?? [];
    $filename = $map[$phaseKey] ?? ($phaseKey . '.svg');
    $fullPath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim((string)$filename, DIRECTORY_SEPARATOR);

    if (!is_file($fullPath) || !is_readable($fullPath)) {
      return null;
    }

    $svg = file_get_contents($fullPath);
    if ($svg === false || trim($svg) === '') {
      return null;
    }

    return $svg;
  }

  private function preset(string $phaseKey): ?string
  {
    $icons = [
      'new_moon' => '<svg id="a" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><circle cx="24" cy="24" r="20"/></svg>',
      'waxing_crescent' => '<svg id="a" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="M24,4c11.05,0,20,8.95,20,20s-8.95,20-20,20S4,35.05,4,24,12.95,4,24,4ZM24,6c-.4,0-.79.03-1.18.06,5.49,3.68,9.18,10.32,9.18,17.94s-3.68,14.26-9.18,17.94c.39.03.78.06,1.18.06,9.92,0,18-8.08,18-18S33.92,6,24,6Z"/></svg>',
      'first_quarter' => '<svg id="a" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="M24,44c-11.05,0-20-8.95-20-20S12.95,4,24,4s20,8.95,20,20-8.95,20-20,20ZM24,42c9.92,0,18-8.07,18-18S33.92,6,24,6v36Z"/></svg>',
      'waxing_gibbous' => '<svg id="a" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="M24,4c11.05,0,20,8.95,20,20s-8.95,20-20,20S4,35.05,4,24,12.95,4,24,4ZM25.18,41.94c-5.49-3.68-9.18-10.32-9.18-17.94s3.68-14.26,9.18-17.94c9.38.61,16.82,8.41,16.82,17.94s-7.45,17.33-16.82,17.94Z" style="fill-rule:evenodd;"/></svg>',
      'full_moon' => '<svg id="a" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="M24,4c11.05,0,20,8.95,20,20s-8.95,20-20,20S4,35.05,4,24,12.95,4,24,4ZM42,24c0-9.93-8.08-18-18-18S6,14.07,6,24s8.08,18,18,18,18-8.08,18-18Z"/></svg>',
      'waning_gibbous' => '<svg id="a" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="M24,44c-11.05,0-20-8.95-20-20S12.95,4,24,4s20,8.95,20,20-8.95,20-20,20ZM22.82,6.06c5.49,3.68,9.18,10.32,9.18,17.94s-3.68,14.26-9.18,17.94c-9.38-.61-16.82-8.41-16.82-17.94S13.45,6.67,22.82,6.06Z" style="fill-rule:evenodd;"/></svg>',
      'last_quarter' => '<svg id="a" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="M24,4c11.05,0,20,8.95,20,20s-8.95,20-20,20S4,35.05,4,24,12.95,4,24,4ZM24,6C14.08,6,6,14.07,6,24s8.08,18,18,18V6Z"/></svg>',
      'waning_crescent' => '<svg id="a" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"><path d="M24,44c-11.05,0-20-8.95-20-20S12.95,4,24,4s20,8.95,20,20-8.95,20-20,20ZM24,42c.4,0,.79-.03,1.18-.06-5.49-3.68-9.18-10.32-9.18-17.94s3.68-14.26,9.18-17.94c-.39-.03-.78-.06-1.18-.06C14.08,6,6,14.08,6,24s8.08,18,18,18Z"/></svg>',
    ];

    return $icons[$phaseKey] ?? null;
  }
}
