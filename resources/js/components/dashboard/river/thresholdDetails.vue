<template>
  <v-col style="padding: 0 !important;">
    <v-sheet class="pa-4 elevation-3" rounded="lg" style="position: relative; background: #F8FAF0; border: 1px solid #E0E0E0;">
      <span class="header-bar"></span>
      <h1 class="text-h6">THRESHOLD DETAILS</h1>
      <hr class="section-divider" />

      <v-row>
        <!-- Hardcoded Cards -->
        <v-col cols="12" sm="6" md="3">
          <v-card class="d-flex align-center" style="height: 100px; background: #C0E3F4;">
            <v-row class="pa-2">
              <v-col cols="5" class="d-flex justify-center align-center">
                <img src="../../../../img/riverstatus-icon/rain.svg" alt="" contain width="80" height="80">
              </v-col>
              <v-col cols="7" class="text-dark text-start d-flex flex-column justify-center">
                <p class="font-weight-bold text-body-2" style="color: #000000;">RAIN</p>
                <p class="text-caption" style="color: #000000;">THRESHOLD</p>
              </v-col>
            </v-row>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="d-flex align-center" style="height: 100px; background: #FFF9C4;">
            <v-row class="pa-2">
              <v-col cols="5" class="d-flex justify-center align-center">
                <img src="../../../../img/riverstatus-icon/monitor-rain.svg" alt="" contain width="70" height="70">
              </v-col>
              <v-col cols="7" class="text-dark text-start d-flex flex-column justify-center">
                <p class="font-weight-bold text-body-2" style="color: #000000;">7.5-15</p>
                <p class="text-caption" style="color: #000000;">MM</p>
                <p class="font-weight-bold" style="font-size: 15px; color: #000000;">MONITOR</p>
              </v-col>
            </v-row>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="d-flex align-center" style="height: 100px; background: #FFCC80;">
            <v-row class="pa-2">
              <v-col cols="5" class="d-flex justify-center align-center">
                <img src="../../../../img/riverstatus-icon/warning-rain.svg" alt="" contain width="70" height="70">
              </v-col>
              <v-col cols="7" class="text-dark text-start d-flex flex-column justify-center">
                <p class="font-weight-bold text-body-2" style="color: #000000;">15-30</p>
                <p class="text-caption" style="color: #000000;">MM</p>
                <p class="font-weight-bold" style="font-size: 15px; color: #000000;">ALERT</p>
              </v-col>
            </v-row>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="d-flex align-center" style="height: 100px; background: #FFCDD2;">
            <v-row class="pa-2">
              <v-col cols="5" class="d-flex justify-center align-center">
                <img src="../../../../img/riverstatus-icon/alert-rain.svg" alt="" contain width="70" height="70">
              </v-col>
              <v-col cols="7" class="text-dark text-start d-flex flex-column justify-center">
                <p class="font-weight-bold text-body-2" style="color: #000000;">30+</p>
                <p class="text-caption" style="color: #000000;">MM</p>
                <p class="font-weight-bold" style="font-size: 15px; color: #000000;">EVACUATION</p>
              </v-col>
            </v-row>
          </v-card>
        </v-col>
      </v-row>
      <v-row>
        <!-- Hardcoded Cards -->
        <v-col cols="12" sm="6" md="3">
          <v-card class="d-flex align-center" style="height: 100px; background: #C0E3F4;">
            <v-row class="pa-2">
              <v-col cols="5" class="d-flex justify-center align-center">
                <img src="../../../../img/riverstatus-icon/water.svg" alt="" contain width="80" height="80">
              </v-col>
              <v-col cols="7" class="text-dark text-start d-flex flex-column justify-center">
                <p class="font-weight-bold text-body-2" style="color: #000000;">RAIN</p>
                <p class="text-caption" style="color: #000000;">THRESHOLD</p>
              </v-col>
            </v-row>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="d-flex align-center" style="height: 100px; background: #FFF9C4;">
            <v-row class="pa-2">
              <v-col cols="5" class="d-flex justify-center align-center">
                <img src="../../../../img/riverstatus-icon/monitor-water.svg" alt="" contain width="70" height="70">
              </v-col>
              <v-col cols="7" class="text-dark text-start d-flex flex-column justify-center">
                <p class="font-weight-bold text-body-2" style="color: #000000;">7.5-15</p>
                <p class="text-caption" style="color: #000000;">MM</p>
                <p class="font-weight-bold" style="font-size: 15px; color: #000000;">MONITOR</p>
              </v-col>
            </v-row>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="d-flex align-center" style="height: 100px; background: #FFCC80;">
            <v-row class="pa-2">
              <v-col cols="5" class="d-flex justify-center align-center">
                <img src="../../../../img/riverstatus-icon/warning-water.svg" alt="" contain width="70" height="70">
              </v-col>
              <v-col cols="7" class="text-dark text-start d-flex flex-column justify-center">
                <p class="font-weight-bold text-body-2" style="color: #000000;">15-30</p>
                <p class="text-caption" style="color: #000000;">MM</p>
                <p class="font-weight-bold" style="font-size: 15px; color: #000000;">ALERT</p>
              </v-col>
            </v-row>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="3">
          <v-card class="d-flex align-center" style="height: 100px; background: #FFCDD2;">
            <v-row class="pa-2">
              <v-col cols="5" class="d-flex justify-center align-center">
                <img src="../../../../img/riverstatus-icon/alert-water.svg" alt="" contain width="70" height="70">
              </v-col>
              <v-col cols="7" class="text-dark text-start d-flex flex-column justify-center">
                <p class="font-weight-bold text-body-2" style="color: #000000;">30+</p>
                <p class="text-caption" style="color: #000000;">MM</p>
                <p class="font-weight-bold" style="font-size: 15px; color: #000000;">EVACUATION</p>
              </v-col>
            </v-row>
          </v-card>
        </v-col>
      </v-row>
      
    </v-sheet>
  </v-col>
</template>

<style scoped>
.header-bar {
  background: var(--primary-color);
  position: absolute;
  left: 0;
  right: 0;
  top: 0;
  border-top-left-radius: 11px;
  border-top-right-radius: 11px;
  height: 11px;
}
.section-divider {
  border: 2px solid var(--primary-color);
  margin: 10px 0;
}
.text-body-2 {
  font-size: 12px;
  line-height: 1.2;
  margin: 0;
}
.text-caption {
  font-size: 10px;
  line-height: 1.2;
  margin: 0;
}
</style>
