<template>
    <v-col cols="12" class="p-10 ">
      <v-sheet
      class="pa-8 rounded-lg shadow-lg elevation-3"
      style="position: relative; background: #F8FAF0; border: 1px solid #E0E0E0;"
    >
        <!-- Top Bar -->
        <span
        style="background: var(--primary-color); position: absolute; left: 0; right: 0; top: 0; border-top-left-radius: 11px; border-top-right-radius: 11px; height: 8px;"
      ></span>
        
        <v-row class="mt-5 ml-5">
          <v-col cols="5">
            <v-row class="d-flex flex-column  text-center">
                <v-img
                    style="height: 120px;"
                    src="https://rdrrmc9-alerto.com/assets/images/logo3.png"
                ></v-img>
                <p class="text-lg font-bold text-gray-800">ALeRTO</p>
                <p class="text-sm text-gray-600">Ateneo Center for Environment and Sustainability</p>
                <p class="text-sm text-gray-600">Ateneo de Zamboanga University</p>
          </v-row>
            <v-divider :thickness="2" class="border-opacity-75 mt-10 mb-5"></v-divider>
            <v-row class="gap-4 justify-center">

              <v-col cols="12" md="5">
                <v-card class="pa-4 rounded-lg shadow-md bg-gray-100 hover:shadow-lg transition-all">
                  <v-row align="center">
                    <v-col cols="2" class="text-center">
                      <v-icon size="32" color="blue-darken-3">mdi-phone</v-icon>
                    </v-col>
                    <v-col>
                      <p class="text-base font-semibold text-gray-800">Phone</p>
                      <p class="text-sm text-gray-600">(63) 991-0871 local 2020-2022</p>
                    </v-col>
                  </v-row>
                </v-card>
              </v-col>

              <v-col cols="12" md="5">
                <v-card class="pa-4 rounded-lg shadow-md bg-gray-100 hover:shadow-lg transition-all">
                  <v-row align="center">
                    <v-col cols="2" class="text-center">
                      <v-icon size="32" color="blue-darken-3">mdi-email</v-icon>
                    </v-col>
                    <v-col>
                      <p class="text-base font-semibold text-gray-800">Email</p>
                      <p class="text-sm text-gray-600">alertoinnov@gmail.com</p>
                      <p class="text-sm text-gray-600">aces@adzu.edu.ph</p>
                    </v-col>
                  </v-row>
                </v-card>
              </v-col>

              <v-col cols="12" md="5">
                <v-card class="pa-4 rounded-lg shadow-md bg-gray-100 hover:shadow-lg transition-all">
                  <v-row align="center">
                    <v-col cols="2" class="text-center">
                      <v-icon size="32" color="blue-darken-3">mdi-map-marker</v-icon>
                    </v-col>
                    <v-col>
                      <p class="text-base font-semibold text-gray-800">Address</p>
                      <p class="text-sm text-gray-600">123 Main Street</p>
                    </v-col>
                  </v-row>
                </v-card>
              </v-col>

              <v-col cols="12" md="5">
                <v-card class="pa-4 rounded-lg shadow-md bg-gray-100 hover:shadow-lg transition-all">
                  <v-row align="center">
                    <v-col cols="2" class="text-center">
                      <v-icon size="32" color="blue-darken-3">mdi-facebook</v-icon>
                    </v-col>
                    <v-col>
                      <p class="text-base font-semibold text-gray-800">Facebook</p>
                      <p class="text-sm text-gray-600">@alertodost9</p>
                    </v-col>
                  </v-row>
                </v-card>
              </v-col>
              
            </v-row>

          </v-col>

          <v-col class="shadow-md bg-gray-100 border border-gray-300 rounded-lg" style="background: lightgrey; margin: 25px; margin-top: 5px;">
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
                        <v-icon size="180" class="text-green-darken-2 mb-3">
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
                            color="green-darken-2 " 
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
  