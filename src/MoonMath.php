<?php

declare(strict_types=1);

namespace ScottBoms\MoonPhases;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class MoonMath
{
  private const SYNODIC_MONTH = 29.53058867;
  private const KNOWN_NEW_MOON = '2000-01-06 18:14:00';

  public function moonAgeDays(DateTimeInterface $date): float
  {
    $utcDate = DateTimeImmutable::createFromInterface($date)->setTimezone(new DateTimeZone('UTC'));
    $base = new DateTimeImmutable(self::KNOWN_NEW_MOON, new DateTimeZone('UTC'));
    $daysSinceBase = ((float)$utcDate->getTimestamp() - (float)$base->getTimestamp()) / 86400.0;
    $age = fmod($daysSinceBase, self::SYNODIC_MONTH);

    if ($age < 0.0) {
      $age += self::SYNODIC_MONTH;
    }

    return $age;
  }

  public function daysUntilNewMoon(float $age): float
  {
    $days = self::SYNODIC_MONTH - $age;
    if ($days >= self::SYNODIC_MONTH - 0.01) {
      return 0.0;
    }

    return max(0.0, $days);
  }

  public function daysUntilFullMoon(float $age): float
  {
    $fullAge = self::SYNODIC_MONTH / 2.0;
    $days = $fullAge - $age;
    if ($days < 0.0) {
      $days += self::SYNODIC_MONTH;
    }

    return max(0.0, $days);
  }

  public function phaseKey(float $age): string
  {
    $slice = self::SYNODIC_MONTH / 8.0;
    $index = (int)floor(($age + ($slice / 2.0)) / $slice) % 8;
    $map = [
      'new_moon',
      'waxing_crescent',
      'first_quarter',
      'waxing_gibbous',
      'full_moon',
      'waning_gibbous',
      'last_quarter',
      'waning_crescent',
    ];

    return $map[$index];
  }

  public function phaseLabel(string $phaseKey): string
  {
    $labels = [
      'new_moon' => 'New Moon',
      'waxing_crescent' => 'Waxing Crescent',
      'first_quarter' => 'First Quarter',
      'waxing_gibbous' => 'Waxing Gibbous',
      'full_moon' => 'Full Moon',
      'waning_gibbous' => 'Waning Gibbous',
      'last_quarter' => 'Last Quarter',
      'waning_crescent' => 'Waning Crescent',
    ];

    return $labels[$phaseKey] ?? 'Moon Phase';
  }
}
