<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button -->
    <router-link 
      to="/uploads" 
      class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-6 transition-colors group"
    >
      <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      <span class="font-medium">Voltar para Uploads</span>
    </router-link>

    <!-- Loading State -->
    <div v-if="loading" class="flex flex-col items-center justify-center py-16">
      <div class="w-16 h-16 rounded-full border-4 border-primary-200 border-t-primary-600 animate-spin mb-4"></div>
      <p class="text-gray-500 dark:text-gray-400">Carregando detalhes do upload...</p>
    </div>

    <div v-else-if="upload">
      <!-- Page Header -->
      <div class="page-header flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 mb-8">
        <div class="flex items-start gap-4">
          <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center flex-shrink-0 shadow-lg shadow-primary-500/25">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div>
            <h1 class="page-title">{{ upload.original_filename }}</h1>
            <p class="page-subtitle flex items-center gap-2 mt-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              Enviado em {{ formatDate(upload.created_at) }} às {{ formatTime(upload.created_at) }}
            </p>
          </div>
        </div>
        <span :class="getStatusBadgeClass(upload.status)" class="text-sm">
          {{ getStatusLabel(upload.status) }}
        </span>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Status Card -->
        <div class="card-stat group" :class="getStatusCardClass(upload.status)">
          <div class="flex items-start justify-between">
            <div>
              <p class="stat-label">Status</p>
              <p class="stat-value mt-2" :class="getStatusColorClass(upload.status)">
                {{ getStatusLabel(upload.status) }}
              </p>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" v-if="upload.status === 'processing'">
                Processando...
              </p>
            </div>
            <div :class="getStatusIconClass(upload.status)">
              <svg v-if="upload.status === 'completed'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <svg v-else-if="upload.status === 'failed'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <svg v-else class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Records -->
        <div class="card-stat group">
          <div class="flex items-start justify-between">
            <div>
              <p class="stat-label">Total de Registros</p>
              <p class="stat-value text-gray-900 dark:text-white mt-2">{{ upload.total_rows }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">linhas no arquivo</p>
            </div>
            <div class="icon-circle primary opacity-80 group-hover:opacity-100 transition-opacity">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Valid Records -->
        <div class="card-stat success group">
          <div class="flex items-start justify-between">
            <div>
              <p class="stat-label">Registros Válidos</p>
              <p class="stat-value text-success-600 dark:text-success-400 mt-2">{{ upload.valid_rows }}</p>
              <p class="text-xs text-success-600 dark:text-success-400 mt-1">
                {{ getSuccessRate }}% do total
              </p>
            </div>
            <div class="icon-circle success opacity-80 group-hover:opacity-100 transition-opacity">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Error Records -->
        <div class="card-stat danger group">
          <div class="flex items-start justify-between">
            <div>
              <p class="stat-label">Registros com Erro</p>
              <p class="stat-value text-danger-600 dark:text-danger-400 mt-2">{{ upload.error_rows }}</p>
              <p class="text-xs text-danger-600 dark:text-danger-400 mt-1" v-if="upload.error_rows > 0">
                requer atenção
              </p>
            </div>
            <div class="icon-circle danger opacity-80 group-hover:opacity-100 transition-opacity">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Progress Section -->
      <div class="card mb-8">
        <div class="flex items-center justify-between mb-4">
          <h2 class="section-title mb-0 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Progresso do Processamento
          </h2>
          <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ uploadProgress }}%</span>
        </div>
        <div class="progress-bar h-4">
          <div
            class="progress-bar-fill"
            :style="{ width: uploadProgress + '%' }"
          ></div>
        </div>
        <div class="flex justify-between mt-3 text-sm">
          <span class="text-gray-500 dark:text-gray-400">
            {{ upload.valid_rows + upload.error_rows }} de {{ upload.total_rows }} processados
          </span>
          <span 
            class="font-medium"
            :class="uploadProgress === 100 ? 'text-success-600 dark:text-success-400' : 'text-primary-600 dark:text-primary-400'"
          >
            {{ uploadProgress === 100 ? 'Concluído' : 'Em andamento' }}
          </span>
        </div>
      </div>

      <!-- Errors Section -->
      <div v-if="upload.error_rows > 0" class="card mb-8">
        <div class="flex items-center justify-between mb-6">
          <h2 class="section-title mb-0 flex items-center gap-2">
            <svg class="w-5 h-5 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Erros Encontrados
          </h2>
          <span class="badge-danger">{{ errors.length }} erros</span>
        </div>

        <div class="overflow-x-auto -mx-6">
          <table class="table-modern">
            <thead>
              <tr>
                <th class="pl-6">Linha</th>
                <th>Tipo</th>
                <th class="pr-6">Mensagem</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="error in errors.slice(0, 10)" :key="error.id">
                <td class="pl-6">
                  <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-danger-100 dark:bg-danger-900/30 text-danger-700 dark:text-danger-400 font-mono font-medium text-sm">
                    {{ error.row_number }}
                  </span>
                </td>
                <td>
                  <span class="badge-warning">{{ error.error_type }}</span>
                </td>
                <td class="pr-6">
                  <p class="text-gray-700 dark:text-gray-300">{{ error.error_message }}</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="errors.length > 10" class="mt-4 p-4 bg-gray-50 dark:bg-dark-700/50 rounded-xl text-center">
          <p class="text-gray-600 dark:text-gray-400">
            <span class="font-semibold">{{ errors.length - 10 }}</span> erros adicionais não exibidos
          </p>
        </div>
      </div>

      <!-- Valid Records Action -->
      <div v-if="upload.valid_rows > 0" class="card">
        <div class="flex flex-col sm:flex-row items-center gap-6">
          <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-success-500 to-success-600 flex items-center justify-center flex-shrink-0">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="flex-1 text-center sm:text-left">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Registros Prontos para Análise</h3>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
              {{ upload.valid_rows }} registros foram processados com sucesso e estão disponíveis para visualização
            </p>
          </div>
          <router-link
            :to="`/records?upload_id=${upload.id}`"
            class="btn-success inline-flex items-center gap-2 flex-shrink-0"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            Ver {{ upload.valid_rows }} registros
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

const route = useRoute()
const upload = ref(null)
const errors = ref([])
const loading = ref(true)
const uploadProgress = ref(0)

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

const formatTime = (date) => {
  return new Date(date).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })
}

const getSuccessRate = computed(() => {
  if (!upload.value || upload.value.total_rows === 0) return 0
  return Math.round((upload.value.valid_rows / upload.value.total_rows) * 100)
})

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Pendente',
    processing: 'Processando',
    completed: 'Concluído',
    failed: 'Erro',
  }
  return labels[status] || status
}

const getStatusBadgeClass = (status) => {
  const classes = {
    pending: 'badge-warning',
    processing: 'badge-info',
    completed: 'badge-success',
    failed: 'badge-danger',
  }
  return classes[status] || 'badge-neutral'
}

const getStatusColorClass = (status) => {
  const classes = {
    pending: 'text-warning-600 dark:text-warning-400',
    processing: 'text-primary-600 dark:text-primary-400',
    completed: 'text-success-600 dark:text-success-400',
    failed: 'text-danger-600 dark:text-danger-400',
  }
  return classes[status] || 'text-gray-900 dark:text-white'
}

const getStatusCardClass = (status) => {
  const classes = {
    pending: 'warning',
    processing: '',
    completed: 'success',
    failed: 'danger',
  }
  return classes[status] || ''
}

const getStatusIconClass = (status) => {
  const classes = {
    pending: 'icon-circle warning opacity-80 group-hover:opacity-100 transition-opacity',
    processing: 'icon-circle primary opacity-80 group-hover:opacity-100 transition-opacity',
    completed: 'icon-circle success opacity-80 group-hover:opacity-100 transition-opacity',
    failed: 'icon-circle danger opacity-80 group-hover:opacity-100 transition-opacity',
  }
  return classes[status] || 'icon-circle primary'
}

const calculateProgress = () => {
  if (!upload.value || upload.value.total_rows === 0) return 0
  const processed = upload.value.valid_rows + upload.value.error_rows
  return Math.min(100, Math.round((processed / upload.value.total_rows) * 100))
}

const loadUpload = async () => {
  try {
    const response = await api.get(`/uploads/${route.params.id}`)
    upload.value = response.data.data
    uploadProgress.value = calculateProgress()
  } catch (error) {
    console.error('Erro ao carregar upload:', error)
  }
}

const loadErrors = async () => {
  try {
    const response = await api.get(`/errors/by-upload/${route.params.id}`)
    errors.value = response.data.data || []
  } catch (error) {
    console.error('Erro ao carregar erros:', error)
  }
}

onMounted(async () => {
  loading.value = true
  await Promise.all([loadUpload(), loadErrors()])
  loading.value = false

  // Atualizar status a cada 2 segundos se ainda estiver processando
  const interval = setInterval(async () => {
    if (upload.value && upload.value.status === 'processing') {
      await loadUpload()
      uploadProgress.value = calculateProgress()
    } else {
      clearInterval(interval)
    }
  }, 2000)
})
</script>
