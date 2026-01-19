<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="page-title flex items-center gap-3">
          <div class="icon-circle primary">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
            </svg>
          </div>
          Dashboard
        </h1>
        <p class="page-subtitle">Visão geral do seu faturamento médico</p>
      </div>
      <router-link to="/uploads" class="btn-primary inline-flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Novo Upload
      </router-link>
    </div>

    <!-- Métricas principais -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Total Faturado -->
      <div class="card-stat group">
        <div class="flex items-start justify-between">
          <div>
            <p class="stat-label">Total Faturado</p>
            <p class="stat-value text-gray-900 dark:text-white mt-2">
              {{ formatCurrency(dashboardData.financial.total_billed) }}
            </p>
            <p class="stat-change positive" v-if="dashboardData.financial.total_billed > 0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
              </svg>
              Atualizado
            </p>
          </div>
          <div class="icon-circle primary opacity-80 group-hover:opacity-100 transition-opacity">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Registros Válidos -->
      <div class="card-stat success group">
        <div class="flex items-start justify-between">
          <div>
            <p class="stat-label">Registros Válidos</p>
            <p class="stat-value text-success-600 dark:text-success-400 mt-2">
              {{ dashboardData.records.approved }}
            </p>
            <p class="stat-change positive">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ successRate }}% de sucesso
            </p>
          </div>
          <div class="icon-circle success opacity-80 group-hover:opacity-100 transition-opacity">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Registros com Erro -->
      <div class="card-stat danger group">
        <div class="flex items-start justify-between">
          <div>
            <p class="stat-label">Registros com Erro</p>
            <p class="stat-value text-danger-600 dark:text-danger-400 mt-2">
              {{ dashboardData.records.rejected }}
            </p>
            <p class="stat-change negative" v-if="dashboardData.records.rejected > 0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              Requer atenção
            </p>
          </div>
          <div class="icon-circle danger opacity-80 group-hover:opacity-100 transition-opacity">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Alertas de Glosa -->
      <div class="card-stat warning group">
        <div class="flex items-start justify-between">
          <div>
            <p class="stat-label">Alertas de Glosa</p>
            <p class="stat-value text-warning-600 dark:text-warning-400 mt-2">
              {{ dashboardData.records.disputed }}
            </p>
            <p class="stat-change" :class="dashboardData.records.disputed > 0 ? 'negative' : 'positive'" v-if="dashboardData.records.disputed >= 0">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              {{ dashboardData.records.disputed > 0 ? 'Pendentes' : 'Nenhum alerta' }}
            </p>
          </div>
          <div class="icon-circle warning opacity-80 group-hover:opacity-100 transition-opacity">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Uploads recentes -->
    <div class="card">
      <div class="flex items-center justify-between mb-6">
        <h2 class="section-title mb-0 flex items-center gap-2">
          <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
          </svg>
          Uploads Recentes
        </h2>
        <router-link to="/uploads" class="link-primary text-sm flex items-center gap-1">
          Ver todos
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </router-link>
      </div>

      <div v-if="loading" class="empty-state">
        <div class="animate-pulse flex flex-col items-center">
          <div class="w-12 h-12 bg-gray-200 dark:bg-dark-700 rounded-full mb-4"></div>
          <div class="h-4 w-32 bg-gray-200 dark:bg-dark-700 rounded"></div>
        </div>
      </div>

      <div v-else-if="recentUploads.length === 0" class="empty-state">
        <div class="empty-state-icon">
          <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
          </svg>
        </div>
        <p class="empty-state-text mb-4">Nenhum upload realizado ainda</p>
        <router-link to="/uploads" class="btn-primary inline-flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Fazer primeiro upload
        </router-link>
      </div>

      <div v-else class="overflow-x-auto -mx-6">
        <table class="table-modern">
          <thead>
            <tr>
              <th class="pl-6">Arquivo</th>
              <th>Data</th>
              <th>Status</th>
              <th>Registros</th>
              <th class="pr-6">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="upload in recentUploads" :key="upload.id" class="group">
              <td class="pl-6">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ upload.original_filename }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ upload.description || 'Sem descrição' }}</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="text-gray-900 dark:text-white">{{ formatDate(upload.created_at) }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ formatTime(upload.created_at) }}</div>
              </td>
              <td>
                <span :class="getStatusBadgeClass(upload.status)">
                  {{ getStatusLabel(upload.status) }}
                </span>
              </td>
              <td>
                <div class="flex items-center gap-2">
                  <div class="flex-1 max-w-[100px]">
                    <div class="progress-bar h-2">
                      <div class="progress-bar-fill" :style="{ width: getProgressPercent(upload) + '%' }"></div>
                    </div>
                  </div>
                  <span class="text-gray-600 dark:text-gray-300 font-medium">
                    {{ upload.valid_rows }}/{{ upload.total_rows }}
                  </span>
                </div>
              </td>
              <td class="pr-6">
                <router-link
                  :to="`/uploads/${upload.id}`"
                  class="inline-flex items-center gap-1 text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium transition-colors"
                >
                  Ver detalhes
                  <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/services/api'

const dashboardData = ref({
  financial: { total_billed: 0, total_paid: 0, total_pending: 0 },
  records: { total: 0, approved: 0, pending: 0, rejected: 0, disputed: 0 },
  uploads: { total: 0, completed: 0, pending: 0, processing: 0, failed: 0 },
  errors: { total: 0, new: 0, acknowledged: 0, resolved: 0 },
  success_rate: 0,
})

const recentUploads = ref([])
const loading = ref(true)

const successRate = computed(() => Math.round(dashboardData.value.success_rate))

const formatCurrency = (value) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(value)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

const formatTime = (date) => {
  return new Date(date).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })
}

const getProgressPercent = (upload) => {
  if (!upload.total_rows || upload.total_rows === 0) return 0
  return Math.round((upload.valid_rows / upload.total_rows) * 100)
}

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
    processing: 'badge-warning',
    completed: 'badge-success',
    failed: 'badge-danger',
  }
  return classes[status] || 'badge-warning'
}

const loadDashboard = async () => {
  try {
    const response = await api.get('/dashboard/summary')
    dashboardData.value = response.data.data
  } catch (error) {
    console.error('Erro ao carregar dashboard:', error)
  }
}

const loadRecentUploads = async () => {
  try {
    const response = await api.get('/uploads?per_page=5')
    recentUploads.value = response.data.data
  } catch (error) {
    console.error('Erro ao carregar uploads:', error)
  }
}

onMounted(async () => {
  loading.value = true
  await Promise.all([loadDashboard(), loadRecentUploads()])
  loading.value = false
})
</script>
