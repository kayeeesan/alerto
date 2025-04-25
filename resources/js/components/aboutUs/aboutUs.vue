<template>
  <v-col class="threshold-container ml-5">
    <v-sheet class="threshold-sheet" rounded="lg">
      <div class="header-container">
        <div class="alert-indicator"></div>
        <h1 class="section-title">
          Automated Water-Level and Rain Monitoring <br />
          using Near-Real Time Observation (ALeRTO)
        </h1>
      </div>

      <v-divider class="divider"></v-divider>

      <div class="content-container">
        <!-- Description Section -->
        <p class="description-text">
          The Automated Water-Level and Rain Monitoring Using Near-Real Time Observation (ALeRTO) is an early warning system developed by the Ateneo de Zamboanga University-GeoSAFER Mindanao ZAMBASULTA Program. The system makes use of the Automated Rain Gauges (ARGs) and Automated Water-Level Sensors (AWLS) installed in various river systems in the region. It monitors data from DOST-ASTI and PAGASA to interpret hydro-meteorological thresholds. Once a threshold is reached, the system sends alerts via SMS to Local Government Units (LGUs) and provides updates through the ALeRTO website, enhancing preparedness and aiding decision-making to mitigate flood risks.
        </p>

        <v-divider class="content-divider"></v-divider>

        <!-- Map Image Section -->
        <div class="image-section d-flex justify-center">
          <v-img
            src="https://rdrrmc9-alerto.com/assets/images/AlertoMap.jpg"
            alt="ALeRTO Map"
            class="content-image"
            cover
          />
        </div>

        <v-divider class="content-divider"></v-divider>

        <!-- Pilot Areas Section -->
        <h2 class="content-heading">10 Pilot Areas</h2>

        <div class="pilot-areas">
          <div class="area-column">
            <h3 class="area-region">Zamboanga del Norte</h3>
            <ul class="area-list">
              <li>Siocon River - Siocon</li>
              <li>Kipit River - Labason</li>
              <li>Salug River - Salug</li>
              <li>Sindangan River - Sindangan</li>
              <li>Tangian River - Pres. Manuel A. Roxas</li>
            </ul>

            <h3 class="area-region">Zamboanga City</h3>
            <ul class="area-list">
              <li>Manicahan River</li>
              <li>Tumaga River</li>
              <li>Lamitan City - Basilan, ARMM</li>
              <li>Gubauan River</li>
            </ul>
          </div>

          <div class="area-column">
            <h3 class="area-region">Zamboanga Sibugay</h3>
            <h3 class="area-region">Sibuguey River</h3>
            <ul class="area-list">
              <li>Bayog</li>
              <li>Diplahan</li>
              <li>Imelda</li>
              <li>Siay</li>
              <li>Payao</li>
            </ul>

            <h3 class="area-region">Kabasalan River</h3>
            <ul class="area-list">
              <li>Kabasalan</li>
            </ul>
          </div>
        </div>

        <v-divider class="content-divider"></v-divider>

        <!-- Device Image Section -->
        <div class="image-section d-flex justify-center">
          <v-img
            src="https://rdrrmc9-alerto.com/assets/images/device/alerto_dev1.jpg"
            alt="ALeRTO Device"
            class="content-image"
            cover
          />
        </div>
        <p class="image-caption">Image of ALeRTO v1 Device</p>

        <v-divider class="content-divider"></v-divider>

        <!-- YouTube Video Section -->
        <div class="video-container">
          <div class="video-wrapper">
            <iframe
              src="https://www.youtube.com/embed/riJtws5mvFA?t=26s"
              frameborder="0"
              allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen
              class="video-iframe"
            ></iframe>
          </div>
        </div>
      </div>
    </v-sheet>
  </v-col>
</template>

<style scoped>
.threshold-container {
  padding: 0 !important;
}

.threshold-sheet {
  padding: 0;
  border: 1px solid #E0E0E0;
  background: #F8FAF0;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
}

.header-container {
  padding: 24px 24px 16px;
  position: relative;
  background: #F5F5F5;
  border-bottom: 1px solid #E0E0E0;
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
  font-size: 1.5rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 4px;
  text-align: center;
  line-height: 1.4;
}

.divider {
  margin: 0;
  border-color: rgba(0, 0, 0, 0.1) !important;
}

.content-container {
  padding: 24px;
}

.description-text {
  font-size: 1rem;
  color: #666;
  line-height: 1.6;
  margin-bottom: 24px;
}

.content-divider {
  margin: 24px 0;
  border-color: rgba(0, 0, 0, 0.1) !important;
}

.image-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin: 16px 0;
}

.content-image {
  width: 100%;
  max-width: 700px;
  height: auto;
  border-radius: 8px;
  border: 1px solid #E0E0E0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  object-fit: cover;
}

.image-caption {
  font-size: 0.875rem;
  color: #666;
  margin-top: 8px;
  text-align: center;
}

.content-heading {
  font-size: 1.25rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 16px;
}

.pilot-areas {
  display: flex;
  flex-wrap: wrap;
  gap: 24px;
}

.area-column {
  flex: 1;
  min-width: 250px;
}

.area-region {
  font-size: 1.125rem;
  font-weight: 600;
  color: #444;
  margin: 16px 0 8px;
}

.area-list {
  font-size: 0.9375rem;
  color: #666;
  line-height: 1.6;
  padding-left: 20px;
}

.video-container {
  display: flex;
  justify-content: center;
  margin: 16px 0;
}

.video-wrapper {
  position: relative;
  width: 100%;
  max-width: 560px;
  height: 400px;
}


.video-iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 8px;
  border: 1px solid #E0E0E0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>
