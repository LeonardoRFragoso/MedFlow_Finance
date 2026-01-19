import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

export const useThemeStore = defineStore('theme', () => {
  const isDark = ref(localStorage.getItem('theme') === 'dark' || 
    (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches))

  const toggleTheme = () => {
    isDark.value = !isDark.value
  }

  const setTheme = (dark) => {
    isDark.value = dark
  }

  watch(isDark, (newValue) => {
    localStorage.setItem('theme', newValue ? 'dark' : 'light')
    if (newValue) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
  }, { immediate: true })

  return {
    isDark,
    toggleTheme,
    setTheme,
  }
})
