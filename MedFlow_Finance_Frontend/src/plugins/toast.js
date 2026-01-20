import ToastNotification from '@/components/notifications/ToastNotification.vue'
import { createApp, h } from 'vue'

export default {
  install: (app) => {
    // Create toast container
    const toastContainer = document.createElement('div')
    toastContainer.id = 'toast-container'
    document.body.appendChild(toastContainer)

    // Create toast app instance
    const toastApp = createApp({
      render() {
        return h(ToastNotification, { ref: 'toast' })
      }
    })

    const toastInstance = toastApp.mount(toastContainer)
    const toast = toastInstance.$refs.toast

    // Make toast available globally
    app.config.globalProperties.$toast = toast

    // Provide toast for composition API
    app.provide('toast', toast)
  }
}
