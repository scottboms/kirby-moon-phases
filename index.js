panel.plugin("scottboms/moon-phases", {
  sections: {
    "moon-phases": {
      data() {
        return {
          moonData: null,
          loadError: null
        };
      },
      props: {
        title: String,
        showIllumination: {
          type: Boolean,
          default: true
        },
        precision: {
          type: Number,
          default: 1
        }
      },
      created() {
        this.load()
          .then((response) => {
            this.moonData = response?.moonData || null;
            this.loadError = null;
          })
          .catch(() => {
            this.loadError = "Could not load data.";
          });
      },
      computed: {
        fullMoon() {
          const value = this.moonData?.daysUntilFullMoon;
          return typeof value === "number" ? value.toFixed(this.precision) : "n/a";
        },
        newMoon() {
          const value = this.moonData?.daysUntilNewMoon;
          return typeof value === "number" ? value.toFixed(this.precision) : "n/a";
        },
        illuminationLabel() {
          const value = this.moonData?.illumination;
          return typeof value === "number" ? `${Math.round(value)}%` : null;
        }
      },
      template: `
        <k-section label="">
          <div class="moon-phase-card">
            <k-grid style="gap: 0.25rem; --columns: 12">

              <div style="--width: 1/4">
                <div class="current-moon-phase">
                  <div v-if="moonData?.iconSvg" class="moon-phase-icon" v-html="moonData.iconSvg" class="moon-phase-icon"></div>
                  <h3 class="moon-phase-heading">{{ moonData?.phaseLabel || "Unknown" }}</h3>
                </div>
              </div>

              <div theme="passive" style="--width: 3/4">
                <div class="moon-phase-stats">
                  <div style="--width: 1/4" class="moon-phase-stat">
                    <strong>{{ fullMoon }} days</strong>
                    <span>until full moon</span>
                  </div>
                  <div style="--width: 1/4" class="moon-phase-stat">
                    <strong>{{ newMoon }} days</strong>
                    <span>until new moon</span>
                  </div>
                  <div v-if="showIllumination && illuminationLabel" style="--width: 1/4" class="moon-phase-stat">
                    <strong>{{ illuminationLabel }}</strong>
                    <span>illumination</span>
                  </div>
                  <div style="--width: 1/4" class="moon-phase-stat">
                    <p v-if="moonData?.isFallback" class="moon-phase-warning">Using cached fallback data.</p>
                    <p v-if="moonData?.error" class="moon-phase-error">{{ moonData.error }}</p>
                    <p v-if="loadError" class="moon-phase-error">{{ loadError }}</p>
                  </div>
                </div>
              </div>
            </k-grid>
          </div>
        </k-section>
      `
    }
  }
});
