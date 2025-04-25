<template>
  <v-col class="threshold-container">
    <v-sheet class="threshold-sheet" rounded="lg">
      <div class="header-container">
        <div class="alert-indicator"></div>
        <h1 class="section-title">
          <v-icon color="#FF5252" class="mr-2">mdi-web</v-icon>
          PAGASA WEBSITE
        </h1>
      </div>
      
      <v-divider class="divider"></v-divider>
      
      <div class="pagasa-content">
        <v-img
          src="https://rdrrmc9-alerto.com/assets/images/partners/pagasa.png"
          max-height="180"
          contain
          class="pagasa-logo mb-4"
        ></v-img>

        <p class="pagasa-description">
          Philippine Atmospheric, Geophysical and Astronomical Services Administration
        </p>

        <v-divider class="divider mb-4"></v-divider>

        <div class="pagasa-buttons">
          <v-btn
            variant="tonal"
            color="primary"
            href="https://bagong.pagasa.dost.gov.ph/index.php"
            target="_blank"
            class="pagasa-button"
          >
            Visit Bagong PAGASA
          </v-btn>
          <v-btn
            variant="tonal"
            color="primary"
            href="https://www.panahon.gov.ph/"
            target="_blank"
            class="pagasa-button"
          >
            Panahon Website
          </v-btn>
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
  background: #FFFFFF;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
}

.header-container {
  padding: 16px 24px 8px;
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
  font-size: 1.25rem;
  font-weight: 700;
  color: #333;
  margin-bottom: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: flex;
  align-items: center;
}

.divider {
  margin: 0;
  border-color: rgba(0, 0, 0, 0.1) !important;
}

.pagasa-content {
  padding: 16px 24px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.pagasa-logo {
  max-width: 80%;
  border-radius: 8px;
}

.pagasa-description {
  font-size: 0.875rem;
  color: #666;
  text-align: center;
  margin-bottom: 16px;
}

.pagasa-buttons {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 8px;
  width: 100%;
}

.pagasa-button {
  flex: 1;
  min-width: 120px;
  max-width: 200px;
}
</style>