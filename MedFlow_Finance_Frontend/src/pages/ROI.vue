<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
      <div>
        <h1 class="page-title flex items-center gap-3">
          <div class="icon-circle primary">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          ROI e Métricas
        </h1>
        <p class="page-subtitle">Análise de retorno sobre investimento e performance</p>
      </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-8">
      <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
          </svg>
        </div>
        <div>
          <h2 class="section-title mb-0">Filtros</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">Selecione o período para análise</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
          <label class="input-label">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Data Inicial
          </label>
          <input
            v-model="filters.periodStart"
            type="date"
            class="input-field"
          />
        </div>
        <div>
          <label class="input-label">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Data Final
          </label>
          <input
            v-model="filters.periodEnd"
            type="date"
            class="input-field"
          />
        </div>
        <div class="flex items-end">
          <button
            @click="loadROI"
            :disabled="loading"
            class="btn-primary w-full inline-flex items-center justify-center gap-2"
          >
            <svg v-if="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loading ? 'Carregando...' : 'Atualizar' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Erro -->
    <ErrorState
      v-if="error"
      title="Erro ao carregar ROI"
      :message="error"
    >
      <template #action>
        <button @click="loadROI" class="btn-secondary mt-4">
          Tentar Novamente
        </button>
      </template>
    </ErrorState>

    <!-- Loading -->
    <LoadingState
      v-else-if="loading"
      message="Calculando métricas de ROI..."
    />

    <!-- Conteúdo -->
    <div v-else-if="roi">
      <!-- Métricas Principais -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <MetricCard
          label="Total Faturado"
          :value="roi.financial_impact?.total_billed || 0"
          format="currency"
          icon-color="primary"
        />
        <MetricCard
          label="Total Aprovado"
          :value="roi.financial_impact?.total_approved || 0"
          format="currency"
          icon-color="success"
        />
        <MetricCard
          label="Valor em Risco"
          :value="roi.financial_impact?.value_at_risk || 0"
          format="currency"
          icon-color="danger"
        />
        <MetricCard
          label="Recuperação Potencial"
          :value="roi.financial_impact?.potential_recovery || 0"
          format="currency"
          icon-color="warning"
        />
      </div>

      <!-- Taxa de Sucesso -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="card">
          <h3 class="section-title mb-4">Taxa de Sucesso</h3>
          <div class="flex items-center justify-between mb-3">
            <span class="text-gray-600 dark:text-gray-400">Aprovação</span>
            <span class="text-2xl font-bold text-gray-900 dark:text-white">
              {{ roi.volume?.success_rate || 0 }}%
            </span>
          </div>
          <div class="progress-bar h-3">
            <div
              class="h-full rounded-full bg-gradient-to-r from-green-500 to-green-600 transition-all duration-500"
              :style="{ width: (roi.volume?.success_rate || 0) + '%' }"
            ></div>
          </div>
        </div>

        <div class="card">
          <h3 class="section-title mb-4">Análise de Volume</h3>
          <div class="space-y-2">
            <div class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-gray-400">Total de Registros</span>
              <span class="font-semibold text-gray-900 dark:text-white">{{ roi.volume?.total_records || 0 }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-gray-400">Aprovados</span>
              <span class="font-semibold text-green-600 dark:text-green-400">{{ roi.volume?.approved || 0 }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-gray-400">Rejeitados</span>
              <span class="font-semibold text-red-600 dark:text-red-400">{{ roi.volume?.rejected || 0 }}</span>
            </div>
            <div class="flex justify-between text-sm">
              <span class="text-gray-600 dark:text-gray-400">Contestados</span>
              <span class="font-semibold text-yellow-600 dark:text-yellow-400">{{ roi.volume?.disputed || 0 }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Recomendações -->
      <div v-if="roi.recommendations && roi.recommendations.length > 0" class="card">
        <h3 class="section-title mb-4">Recomendações</h3>
        <div class="space-y-3">
          <div
            v-for="(rec, idx) in roi.recommendations"
            :key="idx"
            class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg"
          >
            <div class="flex items-start justify-between mb-2">
              <p class="text-sm font-medium text-blue-900 dark:text-blue-100">{{ rec.action || rec }}</p>
              <span v-if="rec.priority" class="text-xs font-semibold px-2 py-1 rounded" :class="getPriorityClass(rec.priority)">
                {{ rec.priority }}
              </span>
            </div>
            <p v-if="rec.expected_result" class="text-xs text-blue-700 dark:text-blue-200">{{ rec.expected_result }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Vazio -->
    <EmptyState
      v-else
      title="Nenhum dado de ROI"
      message="Clique em 'Atualizar' para carregar as métricas de ROI"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import LoadingState from '@/components/ui/LoadingState.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import ErrorState from '@/components/ui/ErrorState.vue'
import MetricCard from '@/components/ui/MetricCard.vue'

const roi = ref(null)
const loading = ref(false)
const error = ref(null)

const filters = ref({
  periodStart: new Date(new Date().setDate(new Date().getDate() - 30)).toISOString().split('T')[0],
  periodEnd: new Date().toISOString().split('T')[0]
})

const validatePeriod = () => {
  if (!filters.value.periodStart) {
    error.value = 'Data inicial é obrigatória'
    return false
  }
  if (!filters.value.periodEnd) {
    error.value = 'Data final é obrigatória'
    return false
  }
  if (new Date(filters.value.periodEnd) < new Date(filters.value.periodStart)) {
    error.value = 'Data final não pode ser anterior à data inicial'
    return false
  }
  return true
}

const loadROI = async () => {
  error.value = null

  if (!validatePeriod()) {
    return
  }

  loading.value = true

  try {
    const params = new URLSearchParams()
    if (filters.value.periodStart) params.append('period_start', filters.value.periodStart)
    if (filters.value.periodEnd) params.append('period_end', filters.value.periodEnd)

    const response = await api.get(`/roi/summary?${params.toString()}`)
    roi.value = response.data.data
  } catch (err) {
    error.value = err.response?.data?.message || 'Erro ao carregar ROI'
    console.error('Erro ao carregar ROI:', err)
  } finally {
    loading.value = false
  }
}

const getPriorityClass = (priority) => {
  const classes = {
    high: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
    medium: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
    low: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
  }
  return classes[priority] || classes.low
}

onMounted(() => {
  loadROI()
})
</script>
