<?php

//namespace scottboms\moon-phases;

/**
 * Moon Phases for Kirby
 *
 * @author Scott Boms <plugins@scottboms.com>
 * @link https://github.com/scottboms/kirby-moon-phases
 * @license MIT
**/

declare(strict_types=1);

use Kirby\Cms\App as Kirby;
use ScottBoms\MoonPhases\IconResolver;
use ScottBoms\MoonPhases\IpGeolocationProvider;
use ScottBoms\MoonPhases\MoonMath;
use ScottBoms\MoonPhases\MoonPhasesService;

require_once __DIR__ . '/src/ProviderInterface.php';
require_once __DIR__ . '/src/IpGeolocationProvider.php';
require_once __DIR__ . '/src/IconResolver.php';
require_once __DIR__ . '/src/MoonMath.php';
require_once __DIR__ . '/src/MoonPhasesService.php';

// kirby version check
if (
	version_compare(Kirby::version() ?? '0.0.0', '6.0.0', '<') ||
	version_compare(Kirby::version() ?? '0.0.0', '7.0.0', '>=')
) {
	throw new Exception('Moon Phases requires Kirby v5');
}

if (!function_exists('scottbomsMoonPhasesService')) {
  function scottbomsMoonPhasesService(?Kirby $kirby = null): MoonPhasesService
  {
    $kirby ??= kirby();
    $options = $kirby->option('scottboms.moon-phases', []);

    return new MoonPhasesService(
      $kirby,
      $options,
      new IpGeolocationProvider(),
      new IconResolver(),
      new MoonMath()
    );
  }
}

Kirby::plugin('scottboms/moon-phases', [
	'options' => [
	],

  'siteMethods' => [
    'moonPhases' => function (): array {
      return scottbomsMoonPhasesService($this->kirby())->getMoonData();
    },
    'moonPhase' => function (): ?string {
      return $this->moonPhases()['phase'] ?? null;
    },
    'moonPhaseLabel' => function (): ?string {
      return $this->moonPhases()['phaseLabel'] ?? null;
    },
    'moonIllumination' => function (int $precision = 0): ?float {
      $value = $this->moonPhases()['illumination'] ?? null;
      return $value === null ? null : round((float)$value, $precision);
    },
    'moonDaysUntilFullMoon' => function (int $precision = 1): ?float {
      $value = $this->moonPhases()['daysUntilFullMoon'] ?? null;
      return $value === null ? null : round((float)$value, $precision);
    },
    'moonDaysUntilNewMoon' => function (int $precision = 1): ?float {
      $value = $this->moonPhases()['daysUntilNewMoon'] ?? null;
      return $value === null ? null : round((float)$value, $precision);
    },
    'daysUntilFullMoon' => function (int $precision = 1): ?float {
      return $this->moonDaysUntilFullMoon($precision);
    },
    'daysUntilNewMoon' => function (int $precision = 1): ?float {
      return $this->moonDaysUntilNewMoon($precision);
    },
    'moonPhaseIcon' => function (): ?string {
      return $this->moonPhases()['iconSvg'] ?? null;
    },
  ],

  'sections' => [
    'moon-phases' => [
      'props' => [
        'title' => function (string $title = 'Moon Phases'): string {
          return $title;
        },
        'showIllumination' => function (bool $showIllumination = true): bool {
          return $showIllumination;
        },
        'precision' => function (int $precision = 1): int {
          return max(0, min(4, $precision));
        },
      ],
      'computed' => [
        'moonData' => function (): array {
          return scottbomsMoonPhasesService($this->model()->kirby())->getMoonData();
        },
      ],
    ],
  ],

  'snippets' => [
    'moon-phases' => __DIR__ . '/snippets/moon-phases.php'
  ],

	'info' => [
		'version'  => '6.0.0',
		'homepage' => 'https://github.com/scottboms/kirby-moon-phases',
		'license'  => 'MIT',
		'authors'  => [[ 'name' => 'Scott Boms' ]],
	]
]);
