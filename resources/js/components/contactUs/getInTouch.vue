<template>
    <v-col cols="12" class="p-10 ml-5">
      <v-sheet
      class="pa-8 rounded-lg shadow-lg elevation-3"
      style="position: relative; background: #F8FAF0; border: 1px solid #E0E0E0;"
    >
        <!-- Top Bar -->
        <span
        style="background: var(--primary-color); position: absolute; left: 0; right: 0; top: 0; border-top-left-radius: 11px; border-top-right-radius: 11px; height: 8px;"
      ></span>
       
        
        <v-row class="mt-1 ml-5">
          <v-col cols="5">
            <v-row class="d-flex flex-column text-center mb-2">
                <v-img
                    style="height: 120px;"
                    src="https://rdrrmc9-alerto.com/assets/images/logo3.png"
                ></v-img>
                <p class="text-lg font-bold text-gray-800" style="margin-bottom: 0 !important;">ALeRTO</p>
                <p class="text-sm text-gray-600" style="margin-bottom: 0 !important;">Ateneo Center for Environment and Sustainability</p>
                <p class="text-s text-gray-600">Ateneo de Zamboanga University</p>
        </v-row>
        <v-divider :thickness="2" class="border-opacity-75 mt-1 mb-4"></v-divider>
            <v-row class="my-4">
              <v-col cols="12" class="d-flex align-center">
                <v-icon class="mr-4" style="font-size: 28px; color: #3b5998;">mdi-facebook</v-icon>
                <span class="text-lg font-bold">facebook.com/alerto</span>
              </v-col>
              <v-col cols="12" class="d-flex align-center ">
                <v-icon class="mr-4" style="font-size: 28px; color: #d32f2f;">mdi-email</v-icon>
                <span class="text-lg font-bold">alerto@ateneo.edu</span>
              </v-col>
              <v-col cols="12" class="d-flex align-center ">
                <v-icon class="mr-4" style="font-size: 28px; color: #388e3c;">mdi-phone</v-icon>
                <span class="text-lg font-bold">+63 912 345 6789</span>
              </v-col>
              <v-col cols="12" class="d-flex align-center ">
                <v-icon class="mr-4" style="font-size: 28px; color: #1976d2;">mdi-map-marker</v-icon>
                <span class="text-lg font-bold">Ateneo de Zamboanga University, Zamboanga City</span>
              </v-col>
            </v-row>

          </v-col>

          <v-col class="shadow-md bg-gray-100 border border-gray-300 rounded-lg" style="background: lightgrey; margin: 15px; margin-top: 1px; ">
            <v-row>
                <v-col cols="6">
                    <v-text-field
                        hint="Email"
                        label="Email"
                         variant="solo"
                    ></v-text-field>
                </v-col>
                <v-col cols="6">
                    <v-text-field
                        hint="Name"
                        label="Name"
                         variant="solo"
                    ></v-text-field>
                </v-col>
            </v-row>
            <v-row>
                    <v-col cols="12">
                    <v-text-field
                        hint="Phone No."
                        label="Phone No."
                         variant="solo"
                    ></v-text-field>
                </v-col>
            </v-row>
            
            <v-row>
                <v-col>
                    <v-textarea
                        v-model="message"
                        hint="Message"
                        label="Message"
                        maxlength="120"
                        counter
                        single-line
                        variant="solo"
                    ></v-textarea>
                </v-col>
            </v-row>

            <v-row class="d-flex justify-end pb-6 pr-4">
                <v-dialog max-width="400">
                <template v-slot:activator="{ props: activatorProps }">
                    <v-btn
                    v-bind="activatorProps"
                    color="blue-darken-4"
                    text="SUBMIT"
                    variant="flat"
                    class="text-white"
                    append-icon="mdi-send-outline"
                    ></v-btn>
                </template>

                <template v-slot:default="{ isActive }">
                    <v-card class="py-6 px-4 text-center rounded-lg">
                        <!-- Success Icon (Centered) -->
                        <div class="flex justify-center">
                        <v-icon size="180" class="text-blue-darken-2 mb-3">
                            mdi-check-circle-outline
                        </v-icon>
                        </div>
                    
                        <!-- Success Message -->
                       
                        <v-card-text class="text-gray-600">
                        Your message has been successfully sent.
                        </v-card-text>
                    
                        <!-- Close Button -->
                        <v-card-actions class="justify-center mt-4">
                        <v-btn 
                            color="blue-darken-2 " 
                            variant="elevated" 
                            class="text-white px-6"
                            width="100%"
                            @click="isActive.value = false"
                        >
                            Close
                        </v-btn>
                        </v-card-actions>
                    </v-card>
                </template>
                </v-dialog>

            </v-row>

          </v-col>
        </v-row>
      </v-sheet>
    </v-col>
  </template>
  <script setup>
  import { ref } from 'vue'

  const dialog = ref(true)
</script>
  