<template>
  <v-col class="threshold-container">
    <v-sheet class="threshold-sheet" rounded="lg">
      <div class="header-container">
        <div class="alert-indicator"></div>
        <h1 class="section-title">ALERT THRESHOLDS</h1>
        <p class="section-subtitle">Critical levels for flood monitoring</p>
      </div>
      
      <v-divider class="divider"></v-divider>
      
      <div class="threshold-section">
        <h2 class="parameter-title">
          <v-icon color="#D32F2F" class="mr-2">mdi-weather-pouring</v-icon>
          RAINFALL ALERTS
        </h2>
        
        <v-row class="threshold-cards">
          <v-col cols="12" sm="6" md="3">
            <v-card class="threshold-card info-card">
              <div class="card-icon">
                <img src="../../../../img/riverstatus-icon/rain.svg" alt="Rain" width="65">
              </div>
              <div class="card-content">
                <div class="parameter-name">RAIN</div>
                <div class="parameter-type">THRESHOLD</div>
              </div>
            </v-card>
          </v-col>
          
          <v-col cols="12" sm="6" md="3">
            <v-card class="threshold-card monitor">
              <div class="card-icon">
                <img src="../../../../img/riverstatus-icon/monitor-rain.svg" alt="Monitor" width="65">
              </div>
              <div class="card-content">
                <div class="value-range">7.5-15</div>
                <div class="unit">MM</div>
                <div class="status">MONITOR</div>
              </div>
            </v-card>
          </v-col>
          
          <v-col cols="12" sm="6" md="3">
            <v-card class="threshold-card alert">
              <div class="card-icon">
                <img src="../../../../img/riverstatus-icon/warning-rain.svg" alt="Alert" width="65">
              </div>
              <div class="card-content">
                <div class="value-range">15-30</div>
                <div class="unit">MM</div>
                <div class="status">ALERT</div>
              </div>
            </v-card>
          </v-col>
          
          <v-col cols="12" sm="6" md="3">
            <v-card class="threshold-card evacuation">
              <div class="card-icon">
                <img src="../../../../img/riverstatus-icon/alert-rain.svg" alt="Evacuation" width="65">
              </div>
              <div class="card-content">
                <div class="value-range">30+</div>
                <div class="unit">MM</div>
                <div class="status text-caption text-sm-body-6">EVACUATION</div>
              </div>
            </v-card>
          </v-col>
        </v-row>
      </div>
      
      <div class="threshold-section" style="border-bottom-right-radius: 11px; border-bottom-left-radius: 11px;">
        <h2 class="parameter-title">
          <v-icon color="#1976D2" class="mr-2">mdi-waves</v-icon>
          WATER LEVEL ALERTS
        </h2>
        
        <v-row class="threshold-cards">
          <v-col cols="12" sm="6" md="3">
            <v-card class="threshold-card info-card">
              <div class="card-icon">
                <img src="../../../../img/riverstatus-icon/water.svg" alt="Water" width="65">
              </div>
              <div class="card-content">
                <div class="parameter-name">WATER</div>
                <div class="parameter-type">THRESHOLD</div>
              </div>
            </v-card>
          </v-col>
          
          <v-col cols="12" sm="6" md="3">
            <v-card class="threshold-card monitor">
              <div class="card-icon">
                <img src="../../../../img/riverstatus-icon/monitor-water.svg" alt="Monitor" width="65">
              </div>
              <div class="card-content">
                <div class="value-range">60% BANKFULL</div>
                <div class="status">MONITOR</div>
              </div>
            </v-card>
          </v-col>
          
          <v-col cols="12" sm="6" md="3">
            <v-card class="threshold-card alert">
              <div class="card-icon">
                <img src="../../../../img/riverstatus-icon/warning-water.svg" alt="Alert" width="65">
              </div>
              <div class="card-content">
                <div class="value-range">80% BANKFULL</div>
                <div class="status">ALERT</div>
              </div>
            </v-card>
          </v-col>
          
          <v-col cols="12" sm="6" md="3">
            <v-card class="threshold-card evacuation">
              <div class="card-icon">
                <img src="../../../../img/riverstatus-icon/alert-water.svg" alt="Evacuation" width="65">
              </div>
              <div class="card-content">
                <div class="value-range">100% BANKFULL</div>
                <div class="status text-caption text-sm-body-6">EVACUATION</div>
              </div>
            </v-card>
          </v-col>
        </v-row>
      </div>
    </v-sheet>
  </v-col>
</template>

<script>
export default {
  // Component logic remains the same
}
</script>

<style scoped>
.threshold-container {
  padding: 0 !important;
  border-top-right-radius: 11px;
}

.threshold-sheet {
  padding: 0;
  border: 1px solid #E0E0E0;
  background: #FFFFFF;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
}

.header-container {
  padding: 8px 24px 8px;
  position: relative;
  background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
  border-bottom: 1px solid #d1d9e0;
}

.alert-indicator {
  position: absolute;
  left: 0;
  top: 0;
  height: 100%;
  width: 4px;
  background: var(--primary-color);
}

.section-title {
  font-size: 1.25rem;
  font-weight: 800;
  color: #2c3e50;
  margin-bottom: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  text-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.section-subtitle {
  font-size: 0.875rem;
  color: #546E7A;
  margin: 0;
  font-weight: 600;
}

.divider {
  margin: 0;
  border-color: rgba(0, 0, 0, 0.12) !important;
  height: 2px;
  background: linear-gradient(to right, transparent, #B0BEC5, transparent);
}

.threshold-section {
  padding: 14px 24px;
  background-color: #f8fafc;
}

.threshold-section:nth-child(odd) {
  background-color: #ffffff;
}

.threshold-section:not(:last-child) {
  border-bottom: 1px solid #CFD8DC;
}

.parameter-title {
  font-size: 1rem;
  font-weight: 700;
  color: #37474F;
  margin-bottom: 5px;
  display: flex;
  align-items: center;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.threshold-cards {
  margin: 0 -8px;
}

.threshold-card {
  height: 100px;
  border-radius: 10px !important;
  overflow: hidden;
  display: flex;
  align-items: center;
  padding: 18px;
  border: none;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.threshold-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
}

.info-card {
  background: #DEE3E7;
  border: 1px solid #B0BEC5;
}

.card-icon {
  width: 64px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 18px;
  flex-shrink: 0;
  background: rgba(255,255,255,0.7);
  border-radius: 12px;
  padding: 8px;
}

.monitor {
  /* background: linear-gradient(145deg, #E3F2FD, #BBDEFB); */
  background: #CAE8F9;
  border-left: 5px solid #1976D2;
}

.alert {
  background: #FFEFC6;
  border-left: 5px solid #FFA000;
}

.evacuation {
  background: #FDD8DF;
  border-left: 5px solid #D32F2F;
}

.card-content {
  display: flex;
  flex-direction: column;
}

.parameter-name {
  font-size: 1.1rem;
  font-weight: 800;
  color: #263238;
  line-height: 1.2;
  text-transform: uppercase;
}

.parameter-type {
  font-size: 0.8rem;
  color: #546E7A;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.value-range {
  font-size: 1rem;
  font-weight: 700;
  color: #263238;
  line-height: 1.2;
}

.unit {
  font-size: 0.7rem;
  color: #546E7A;
  margin: 4px 0;
  font-weight: 600;
}

.status {
  font-size: 0.8rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  margin-top: 6px;
}

.monitor .status {
  color: #1565C0;
  text-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.alert .status {
  color: #FF8F00;
  text-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.evacuation .status {
  color: #C62828;
  text-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

/* Add some subtle animations */
@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.02); }
  100% { transform: scale(1); }
}

.threshold-card:hover .card-icon {
  animation: pulse 1.5s infinite;
}
</style>