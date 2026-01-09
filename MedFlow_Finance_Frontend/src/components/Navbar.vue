<template>
  <nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center">
          <h1 class="text-2xl font-bold text-primary-600">MedFlow Finance</h1>
        </div>

        <div class="hidden md:flex items-center space-x-8">
          <router-link
            to="/"
            class="text-gray-700 hover:text-primary-600 transition"
            :class="{ 'text-primary-600 font-bold': $route.path === '/' }"
          >
            Dashboard
          </router-link>
          <router-link
            to="/uploads"
            class="text-gray-700 hover:text-primary-600 transition"
            :class="{ 'text-primary-600 font-bold': $route.path.startsWith('/uploads') }"
          >
            Uploads
          </router-link>
          <router-link
            to="/records"
            class="text-gray-700 hover:text-primary-600 transition"
            :class="{ 'text-primary-600 font-bold': $route.path === '/records' }"
          >
            Registros
          </router-link>
          <router-link
            to="/reports"
            class="text-gray-700 hover:text-primary-600 transition"
            :class="{ 'text-primary-600 font-bold': $route.path === '/reports' }"
          >
            Relatórios
          </router-link>
        </div>

        <div class="flex items-center space-x-4">
          <div class="text-sm">
            <p class="font-medium text-gray-900">{{ authStore.user?.name }}</p>
            <p class="text-gray-500">{{ authStore.clinic?.name }}</p>
          </div>
          <button
            @click="handleLogout"
            class="btn-secondary text-sm"
          >
            Sair
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>
