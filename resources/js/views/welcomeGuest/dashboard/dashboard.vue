<script setup>
import lineRainChart from "../../../components/dashboard/dashboard/lineRainChart.vue"
import weatherMap from "../../../components/dashboard/weather/weatherForecast.vue";
import alert from "../../../components/dashboard/dashboard/alert.vue";

</script>
<template>
    <v-row>
        <alert/>
    </v-row>
   <v-row>
    <v-col cols="6">
         <weatherMap/>
    </v-col>
    <v-col cols="6">
        <lineRainChart/>
    </v-col>
   </v-row>
</template>