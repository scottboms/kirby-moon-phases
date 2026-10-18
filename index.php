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

use Kirby\Cms\App as App;
use ScottBoms\MoonPhases\IconResolver;
use ScottBoms\MoonPhases\IpGeolocationProvider;
use ScottBoms\MoonPhases\MoonMath;
use ScottBoms\MoonPhases\MoonPhasesService;
use ScottBoms\MoonPhases\MoonPhasesField;

require_once __DIR__ . '/src/ProviderInterface.php';
require_once __DIR__ . '/src/IpGeolocationProvider.php';
require_once __DIR__ . '/src/IconResolver.php';
require_once __DIR__ . '/src/MoonMath.php';
require_once __DIR__ . '/src/MoonPhasesService.php';
require_once __DIR__ . '/fields/MoonPhasesField.php';

$version = App::version() ?? '0.0.0';
if (
    version_compare($version, '6.0.0-beta.1', '<') ||
    version_compare($version, '7.0.0', '>=')
) {
    throw new Exception('Moon Phases requires Kirby 6');
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

Kirby::plugin('scottboms/moon-phases',
  info: [
    'homepage' => 'https://github.com/scottboms/kirby-moon-phases',
    'version'  => '6.0.0',
    'license'  => 'MIT',
    'authors'  => [[ 'name' => 'Scott Boms' ]],
  ],
  extends: [
    'options' => [],

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

    'fields' => [
      'moon-phases' => MoonPhasesField::class,
    ],

    'snippets' => [
      'moon-phases' => __DIR__ . '/snippets/moon-phases.php'
    ],
  ]
);
