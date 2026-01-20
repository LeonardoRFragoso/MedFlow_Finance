import { getCurrentInstance } from 'vue'

export function useToast() {
  const instance = getCurrentInstance()
  
  if (!instance) {
    console.warn('useToast must be called within a component setup function')
    return {
      success: () => {},
      error: () => {},
      warning: () => {},
      info: () => {}
    }
  }

  const toast = instance.appContext.config.globalProperties.$toast

  return {
    success: (message, title = 'Sucesso', duration = 5000) => {
      toast?.addToast({
        type: 'success',
        title,
        message,
        duration
      })
    },
    error: (message, title = 'Erro', duration = 5000) => {
      toast?.addToast({
        type: 'error',
        title,
        message,
        duration
      })
    },
    warning: (message, title = 'Atenção', duration = 5000) => {
      toast?.addToast({
        type: 'warning',
        title,
        message,
        duration
      })
    },
    info: (message, title = 'Informação', duration = 5000) => {
      toast?.addToast({
        type: 'info',
        title,
        message,
        duration
      })
    }
  }
}
