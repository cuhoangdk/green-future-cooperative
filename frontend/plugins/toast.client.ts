import * as vt from 'vue-toastification'
import 'vue-toastification/dist/index.css';
import { defineNuxtPlugin } from '#app';

export default defineNuxtPlugin((nuxtApp) => {
  nuxtApp.vueApp.use(vt.default, {
    position: 'top-left',
    timeout: 5000,
    closeOnClick: true,
    pauseOnHover: true,
  })

  return {
    provide: {
      toast: vt.useToast(),
    },
  }
})