<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-600 to-primary-900 flex items-center justify-center px-4">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md">
      <h1 class="text-3xl font-bold text-center text-gray-900 mb-2">MedFlow Finance</h1>
      <p class="text-center text-gray-600 mb-8">Automação de Faturamento Médico</p>

      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input
            v-model="email"
            type="email"
            required
            class="input-field"
            placeholder="seu@email.com"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
          <input
            v-model="password"
            type="password"
            required
            class="input-field"
            placeholder="••••••••"
          />
        </div>

        <div v-if="authStore.error" class="bg-danger-50 border border-danger-200 text-danger-800 px-4 py-3 rounded">
          {{ authStore.error }}
        </div>

        <button
          type="submit"
          :disabled="authStore.loading"
          class="w-full btn-primary disabled:opacity-50"
        >
          {{ authStore.loading ? 'Entrando...' : 'Entrar' }}
        </button>

        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
          <p class="text-sm font-medium text-gray-700 mb-2">Credenciais de Teste:</p>
          <p class="text-xs text-gray-600">Email: admin@medflow.local</p>
          <p class="text-xs text-gray-600">Senha: password</p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const authStore = useAuthStore()
const router = useRouter()

const handleLogin = async () => {
  const success = await authStore.login(email.value, password.value)
  if (success) {
    router.push('/')
  }
}
</script>
