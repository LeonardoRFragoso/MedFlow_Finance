<template>
  <div class="min-h-screen flex">
    <!-- Left Panel - Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900 relative overflow-hidden">
      <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-success-500 rounded-full blur-3xl"></div>
      </div>
      
      <div class="relative z-10 flex flex-col justify-center px-12 text-white">
        <div class="flex items-center gap-3 mb-8">
          <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
          <span class="text-2xl font-bold">MedFlow Finance</span>
        </div>

        <h1 class="text-4xl font-bold mb-4 leading-tight">
          Comece a recuperar<br/>
          <span class="text-success-400">seu faturamento hoje</span>
        </h1>
        
        <p class="text-lg text-primary-100 mb-10 max-w-md">
          Crie sua conta gratuita e tenha acesso a 14 dias de teste sem compromisso.
        </p>

        <div class="space-y-4">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-success-500/20 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <span class="text-primary-100">14 dias grátis, sem cartão</span>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-success-500/20 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <span class="text-primary-100">Configuração em 5 minutos</span>
          </div>
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-success-500/20 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <span class="text-primary-100">Suporte técnico incluso</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Panel - Register Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50">
      <div class="w-full max-w-md">
        <!-- Mobile Logo -->
        <div class="lg:hidden flex items-center justify-center gap-3 mb-8">
          <div class="w-12 h-12 bg-primary-600 rounded-xl flex items-center justify-center">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
          <span class="text-xl font-bold text-gray-900">MedFlow Finance</span>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
          <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Crie sua conta</h2>
            <p class="text-gray-500">Preencha os dados para começar</p>
          </div>

          <form @submit.prevent="handleRegister" class="space-y-4">
            <!-- Clinic Name -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Nome da Clínica</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                </div>
                <input
                  v-model="form.clinicName"
                  type="text"
                  required
                  class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                  placeholder="Nome da sua clínica"
                />
              </div>
            </div>

            <!-- Full Name -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Seu Nome Completo</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <input
                  v-model="form.name"
                  type="text"
                  required
                  class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                  placeholder="Seu nome completo"
                />
              </div>
            </div>

            <!-- Email -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                  </svg>
                </div>
                <input
                  v-model="form.email"
                  type="email"
                  required
                  class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                  placeholder="seu@email.com"
                />
              </div>
            </div>

            <!-- Password -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">Senha</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
                <input
                  v-model="form.password"
                  type="password"
                  required
                  minlength="8"
                  class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-gray-50 focus:bg-white"
                  placeholder="Mínimo 8 caracteres"
                />
              </div>
            </div>

            <!-- Terms -->
            <div class="flex items-start gap-3">
              <input
                v-model="form.acceptTerms"
                type="checkbox"
                required
                class="mt-1 w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
              />
              <label class="text-sm text-gray-600">
                Li e aceito os <a href="#" class="text-primary-600 hover:underline">Termos de Uso</a> e a <a href="#" class="text-primary-600 hover:underline">Política de Privacidade</a>
              </label>
            </div>

            <div v-if="error" class="flex items-center gap-2 bg-danger-100 border border-danger-200 text-danger-800 px-4 py-3 rounded-xl">
              <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span class="text-sm">{{ error }}</span>
            </div>

            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-semibold py-3.5 px-4 rounded-xl transition-all duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none shadow-lg shadow-primary-500/30"
            >
              <span v-if="loading" class="flex items-center justify-center gap-2">
                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Criando conta...
              </span>
              <span v-else>Criar Conta Grátis</span>
            </button>
          </form>

          <div class="mt-6 text-center">
            <p class="text-gray-600">
              Já tem uma conta? 
              <router-link to="/login" class="text-primary-600 hover:underline font-medium">Fazer login</router-link>
            </p>
          </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-sm text-gray-500 mt-6">
          © 2026 MedFlow Finance. Todos os direitos reservados.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const loading = ref(false)
const error = ref('')

const form = reactive({
  clinicName: '',
  name: '',
  email: '',
  password: '',
  acceptTerms: false
})

const handleRegister = async () => {
  loading.value = true
  error.value = ''
  
  try {
    // Simulação - em produção conectaria à API
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    // Redireciona para login com mensagem de sucesso
    router.push('/login?registered=true')
  } catch (err) {
    error.value = 'Erro ao criar conta. Tente novamente.'
  } finally {
    loading.value = false
  }
}
</script>
