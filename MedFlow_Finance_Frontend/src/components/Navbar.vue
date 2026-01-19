<template>
  <nav class="bg-white/80 dark:bg-dark-900/80 backdrop-blur-xl border-b border-gray-200/50 dark:border-dark-700/50 sticky top-0 z-40 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-lg shadow-primary-500/25">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
          <div>
            <h1 class="text-xl font-bold bg-gradient-to-r from-primary-600 to-primary-500 dark:from-primary-400 dark:to-primary-300 bg-clip-text text-transparent">
              MedFlow Finance
            </h1>
            <p class="text-[10px] text-gray-500 dark:text-gray-400 -mt-0.5 font-medium tracking-wider uppercase">Healthcare Billing</p>
          </div>
        </div>

        <!-- Navigation Links -->
        <div class="hidden md:flex items-center">
          <div class="flex items-center bg-gray-100 dark:bg-dark-800 rounded-xl p-1">
            <router-link
              to="/app"
              class="nav-link"
              :class="{ 'nav-link-active': $route.path === '/app' }"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
              </svg>
              Dashboard
            </router-link>
            <router-link
              to="/uploads"
              class="nav-link"
              :class="{ 'nav-link-active': $route.path.startsWith('/uploads') }"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
              </svg>
              Uploads
            </router-link>
            <router-link
              to="/records"
              class="nav-link"
              :class="{ 'nav-link-active': $route.path === '/records' }"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Registros
            </router-link>
            <router-link
              to="/reports"
              class="nav-link"
              :class="{ 'nav-link-active': $route.path === '/reports' }"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              Relatórios
            </router-link>
          </div>
        </div>

        <!-- Right Section -->
        <div class="flex items-center gap-3">
          <!-- Theme Toggle -->
          <button
            @click="themeStore.toggleTheme"
            class="p-2.5 rounded-xl bg-gray-100 dark:bg-dark-800 hover:bg-gray-200 dark:hover:bg-dark-700 transition-all duration-300"
            :title="themeStore.isDark ? 'Modo claro' : 'Modo escuro'"
          >
            <svg v-if="themeStore.isDark" class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
            </svg>
            <svg v-else class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
              <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
            </svg>
          </button>

          <!-- User Info -->
          <div class="hidden sm:flex items-center gap-3 pl-3 border-l border-gray-200 dark:border-dark-700">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-semibold text-sm shadow-md shadow-primary-500/20">
              {{ userInitials }}
            </div>
            <div class="text-right">
              <p class="text-sm font-semibold text-gray-900 dark:text-white leading-tight">{{ authStore.user?.name }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 leading-tight">{{ authStore.clinic?.name }}</p>
            </div>
          </div>

          <!-- Logout Button -->
          <button
            @click="handleLogout"
            class="p-2.5 rounded-xl text-gray-500 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-all duration-300"
            title="Sair"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </button>

          <!-- Mobile Menu Button -->
          <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="md:hidden p-2.5 rounded-xl bg-gray-100 dark:bg-dark-800 hover:bg-gray-200 dark:hover:bg-dark-700 transition-all duration-300"
          >
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div v-if="mobileMenuOpen" class="md:hidden py-4 border-t border-gray-200 dark:border-dark-700 animate-slide-down">
        <div class="flex flex-col gap-2">
          <router-link
            to="/app"
            @click="mobileMenuOpen = false"
            class="mobile-nav-link"
            :class="{ 'mobile-nav-link-active': $route.path === '/app' }"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
            Dashboard
          </router-link>
          <router-link
            to="/uploads"
            @click="mobileMenuOpen = false"
            class="mobile-nav-link"
            :class="{ 'mobile-nav-link-active': $route.path.startsWith('/uploads') }"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            Uploads
          </router-link>
          <router-link
            to="/records"
            @click="mobileMenuOpen = false"
            class="mobile-nav-link"
            :class="{ 'mobile-nav-link-active': $route.path === '/records' }"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Registros
          </router-link>
          <router-link
            to="/reports"
            @click="mobileMenuOpen = false"
            class="mobile-nav-link"
            :class="{ 'mobile-nav-link-active': $route.path === '/reports' }"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Relatórios
          </router-link>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const themeStore = useThemeStore()
const router = useRouter()
const mobileMenuOpen = ref(false)

const userInitials = computed(() => {
  const name = authStore.user?.name || ''
  return name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase()
})

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.nav-link {
  @apply flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 
         hover:text-gray-900 dark:hover:text-white transition-all duration-200;
}

.nav-link-active {
  @apply bg-white dark:bg-dark-700 text-primary-600 dark:text-primary-400 shadow-sm;
}

.mobile-nav-link {
  @apply flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 dark:text-gray-400 
         hover:bg-gray-100 dark:hover:bg-dark-800 transition-all duration-200 font-medium;
}

.mobile-nav-link-active {
  @apply bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400;
}
</style>
