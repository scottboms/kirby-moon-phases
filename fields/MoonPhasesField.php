<?php

declare(strict_types=1);

namespace ScottBoms\MoonPhases;

use Kirby\Form\Field\DisplayField;

class MoonPhasesField extends DisplayField
{
  protected string $moonTitle;
  protected bool $moonShowIllumination;
  protected int $moonPrecision;

  public function __construct(
    string $title = 'Moon Phases',
    bool $showIllumination = true,
    int $precision = 1,
    mixed ...$args
  ) {
    parent::__construct(...$args);
    $this->moonTitle = $title;
    $this->moonShowIllumination = $showIllumination;
    $this->moonPrecision = max(0, min(4, $precision));
  }

  public function props(): array
  {
    return [
      ...parent::props(),
      'title' => $this->moonTitle,
      'showIllumination' => $this->moonShowIllumination,
      'precision' => $this->moonPrecision,
      'moonData' => $this->moonData(),
    ];
  }

  public function moonData(): array
  {
    return scottbomsMoonPhasesService(
      $this->model()->kirby()
    )->getMoonData();
  }
}
