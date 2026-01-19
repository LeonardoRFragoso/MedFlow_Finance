<template>
  <div class="min-h-screen bg-gray-50 dark:bg-dark-950 transition-colors duration-300">
    <Navbar v-if="authStore.isAuthenticated" />
    <main class="animate-fade-in">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import Navbar from '@/components/Navbar.vue'

const authStore = useAuthStore()
const themeStore = useThemeStore()

onMounted(() => {
  authStore.loadFromStorage()
})
</script>
