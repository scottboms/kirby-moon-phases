<?php

declare(strict_types=1);

namespace ScottBoms\MoonPhases;

interface ProviderInterface
{
  public function fetch(array $options = []): array;
}
