<div class="moon-phases">
  <div class="current-phase">
    <div class="moon-icon"><?= $site->moonPhaseIcon() ?></div>
    <h4><?= $site->moonPhaseLabel() ?></h4>
  </div>

  <div class="moon-phase-meta">
    <h4><?= $site->daysUntilFullMoon(0) ?> days</h4>
    <p>until the next full moon</p>
  </div>

  <div class="moon-phase-meta">
    <h4><?= $site->daysUntilNewMoon(0) ?> days</h4>
    <p>until the next new moon</p>
  </div>

  <div class="moon-phase-meta">
    <h4><?= $site->moonIllumination(0) ?>%</h4>
    <p>illumination</p>
  </div>
</div>
