<template>
  <k-field
    :name="name"
    :type="type"
    :label="title || label"
    :help="help"
    :input="false"
    class="k-moon-phases-field"
  >
    <div class="moon-phase-card">
      <k-grid style="gap: 0.25rem; --columns: 12">
        <div style="--width: 1/4" class="moon-phase-stats">
          <div
            v-if="moonData?.iconSvg"
            class="moon-phase-icon"
            v-html="moonData.iconSvg"
          ></div>

          <div class="moon-phase-stat">
            <strong>{{ moonData?.phaseLabel || "Unknown" }}</strong>
          </div>
        </div>

        <div style="--width: 3/4">
          <div class="moon-phase-stats">
            <div class="moon-phase-stat">
              <strong>{{ fullMoon }} days</strong>
              <span>until full moon</span>
            </div>

            <div class="moon-phase-stat">
              <strong>{{ newMoon }} days</strong>
              <span>until new moon</span>
            </div>

            <div v-if="showIllumination && illuminationLabel" class="moon-phase-stat">
              <strong>{{ illuminationLabel }}</strong>
              <span>illumination</span>
            </div>

            <div v-if="moonData?.isFallback || moonData?.error" class="moon-phase-stat">
              <p v-if="moonData?.error" class="moon-phase-error">
                {{ moonData.error }}
              </p>
            </div>
          </div>
        </div>
      </k-grid>
    </div>
  </k-field>
</template>

<script>
export default {
  name: "MoonPhasesField",

  inheritAttrs: false,

  props: {
    name: String,
    type: String,
    label: String,
    help: String,
    title: String,
    showIllumination: Boolean,
    precision: {
      type: Number,
      default: 1
    },
    moonData: {
      type: Object,
      default: () => ({})
    }
  },

  computed: {
    fullMoon() {
      const value = this.moonData?.daysUntilFullMoon;

      return typeof value === "number"
        ? value.toFixed(this.precision)
        : "n/a";
    },

    newMoon() {
      const value = this.moonData?.daysUntilNewMoon;

      return typeof value === "number"
        ? value.toFixed(this.precision)
        : "n/a";
    },

    illuminationLabel() {
      const value = this.moonData?.illumination;

      return typeof value === "number"
        ? `${Math.round(value)}%`
        : null;
    }
  }
};
</script>

<style scoped>
.moon-phase-card {
  padding: var(--spacing-4);
  border-radius: var(--input-rounded);
  background: var(--input-color-back);
  border: 1px solid var(--input-color-border);
  color: var(--color-text);
}

.current-moon-phase {
  display: flex;
  align-items: center;
  gap: var(--spacing-2);
}

.moon-phase-icon {
  width: 3rem;
  height: 3rem;
  fill: var(--color-text);
}

.moon-phase-stats {
  display: flex;
  flex-wrap: wrap;
  gap: var(--spacing-4);
  align-items: center;
  align-content: center;
}

.moon-phase-stat {
  display: flex;
  flex: 1 1 8rem;
  flex-direction: column;
  gap: var(--spacing-1);
  justify-content: flex-start;
}

.moon-phase-stat strong {
  font-size: var(--text-lg);
}

.moon-phase-stat span {
  color: var(--color-text-dimmed);
}

.moon-phase-warning {
  color: var(--color-warning);
}

.moon-phase-error {
  color: var(--color-negative);
}
</style>
