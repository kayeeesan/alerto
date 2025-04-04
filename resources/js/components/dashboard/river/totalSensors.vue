<script setup>
import { ref, onMounted, watch, computed } from 'vue';
import useSensorsUnderAlerto from '../../../composables/sensorsUnderAlerto';

const { sensors_under_alerto, getSensorsUnderAlerto} = useSensorsUnderAlerto();

const getCountByType = type =>
  computed(() =>
    Array.isArray(sensors_under_alerto.value)
      ? sensors_under_alerto.value.filter(sensor => sensor.sensor_type === type).length
      : 0
  );

const argCount = getCountByType("ARG");
const wlmsCount = getCountByType("WLMS");
const tandemCount = getCountByType("TANDEM");

onMounted(() => {
    getSensorsUnderAlerto();
})
</script>
<template>
            <v-col cols="12" style="padding: 0 !important;" class="">
                <v-sheet class="pa-4 elevation-3" rounded="lg"  style="position: relative; background: #F8FAF0; border: 1px solid #E0E0E0;">
                    <span style="background: var(--primary-color); position: absolute; left: 0; right: 0; top: 0; border-top-left-radius: 11px; border-top-right-radius: 11px; height: 11px;">.</span>
                    <div>
                        <p style="font-size: 20px;"> TOTAL SENSORS</p>
                    </div>
                    <hr style="border: 2px solid var(--primary-color); margin: 10px 0;">
                    <v-row style=" margin: 10px 5px;" class="d-flex justify-center align-center">
                        <v-col class="d-flex justify-center align-center " cols="4" style="background: #C99D34; height: 40px; border-top-left-radius: 10px; border-bottom-left-radius: 10px; color: white;">{{ argCount }}</v-col>
                        <v-col style=" height: 40px; border-top-right-radius: 10px; border-bottom-right-radius: 10px; border: 1px solid #C99D34; background: lightyellow; " class="d-flex justify-center align-center"  cols="8">ARG</v-col>
                    </v-row>
                    <v-row style=" margin: 10px 5px;" class="d-flex justify-center align-center">
                        <v-col class="d-flex justify-center align-center " cols="4" style="background: #C99D34; height: 40px; border-top-left-radius: 10px; border-bottom-left-radius: 10px;  color: white;">{{ wlmsCount }}</v-col>
                        <v-col style=" height: 40px; border-top-right-radius: 10px; border-bottom-right-radius: 10px; border: 1px solid #C99D34; background: lightyellow;" class="d-flex justify-center align-center"  cols="8">WLMS</v-col>
                    </v-row>
                    <v-row style=" margin: 10px 5px;" class="d-flex justify-center align-center">
                        <v-col class="d-flex justify-center align-center " cols="4" style="background: #C99D34; height: 40px; border-top-left-radius: 10px; border-bottom-left-radius: 10px;  color: white;">{{ tandemCount }}</v-col>
                        <v-col style=" height: 40px; border-top-right-radius: 10px; border-bottom-right-radius: 10px; border: 1px solid #C99D34; background: lightyellow;" class="d-flex justify-center align-center"  cols="8">TANDEM</v-col>
                    </v-row>
                </v-sheet>
            </v-col>
</template>



