<script setup>
import lineRainChart from "../../../components/dashboard/dashboard/lineRainChart.vue"
import weatherMap from "../../../components/dashboard/weather/weatherForecast.vue";
import thresholdDetails from "../../../components/dashboard/river/thresholdDetails.vue";
import localRainfall from "../../../components/dashboard/weather/localRainfall.vue";
import localThunderStorm from "../../../components/dashboard/weather/localThunderStorm.vue";

</script>
<template>
    <div class="ml-7">
        <v-row class="ml-1 mb-2">
             <thresholdDetails/>
        </v-row>
        <v-row>
                <v-col cols="12" md="6" sm="12">
                        <weatherMap/>
                </v-col>
                <v-col>
                    <v-row>
                        <v-col cols="12" md="6">
                    <localRainfall/>
                </v-col>
                
                <v-col cols="12" md="6">
                    <localThunderStorm/>
                </v-col>
                </v-row>
            </v-col>
        </v-row>
    </div>
  
</template>