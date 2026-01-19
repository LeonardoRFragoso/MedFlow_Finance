<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="page-title flex items-center gap-3">
          <div class="icon-circle primary">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          Relatórios
        </h1>
        <p class="page-subtitle">Gere e exporte relatórios de faturamento</p>
      </div>
      <span class="badge-neutral">{{ reports.length }} relatórios</span>
    </div>

    <!-- Formulário de geração -->
    <div class="card mb-8">
      <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
        </div>
        <div>
          <h2 class="section-title mb-0">Gerar Novo Relatório</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">Selecione o tipo e período desejado</p>
        </div>
      </div>

      <form @submit.prevent="generateReport" class="space-y-6">
        <!-- Report Type Selection -->
        <div>
          <label class="input-label">Tipo de Relatório</label>
          <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <label
              v-for="type in reportTypes"
              :key="type.value"
              class="relative flex flex-col items-center p-4 rounded-xl border-2 cursor-pointer transition-all duration-200"
              :class="reportForm.type === type.value 
                ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' 
                : 'border-gray-200 dark:border-dark-600 hover:border-gray-300 dark:hover:border-dark-500'"
            >
              <input
                type="radio"
                v-model="reportForm.type"
                :value="type.value"
                class="sr-only"
              />
              <div 
                class="w-10 h-10 rounded-xl flex items-center justify-center mb-2 transition-colors"
                :class="reportForm.type === type.value 
                  ? 'bg-primary-500 text-white' 
                  : 'bg-gray-100 dark:bg-dark-700 text-gray-500 dark:text-gray-400'"
              >
                <component :is="type.icon" class="w-5 h-5" />
              </div>
              <span 
                class="text-sm font-medium text-center transition-colors"
                :class="reportForm.type === type.value 
                  ? 'text-primary-700 dark:text-primary-400' 
                  : 'text-gray-700 dark:text-gray-300'"
              >
                {{ type.label }}
              </span>
            </label>
          </div>
        </div>

        <!-- Date Range -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="input-label">
              <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              Data Inicial
            </label>
            <input
              v-model="reportForm.periodStart"
              type="date"
              class="input-field"
              required
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
              v-model="reportForm.periodEnd"
              type="date"
              class="input-field"
              required
            />
          </div>
        </div>

        <button
          type="submit"
          :disabled="loading || !reportForm.type"
          class="btn-primary inline-flex items-center gap-2"
        >
          <svg v-if="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ loading ? 'Gerando relatório...' : 'Gerar Relatório' }}
        </button>
      </form>
    </div>

    <!-- Lista de relatórios -->
    <div class="card">
      <div class="flex items-center justify-between mb-6">
        <h2 class="section-title mb-0 flex items-center gap-2">
          <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Relatórios Gerados
        </h2>
      </div>

      <div v-if="reports.length === 0" class="empty-state">
        <div class="empty-state-icon">
          <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <p class="empty-state-text">Nenhum relatório gerado ainda</p>
        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Gere seu primeiro relatório usando o formulário acima</p>
      </div>

      <div v-else class="overflow-x-auto -mx-6">
        <table class="table-modern">
          <thead>
            <tr>
              <th class="pl-6">Tipo</th>
              <th>Período</th>
              <th>Registros</th>
              <th>Válidos</th>
              <th>Erros</th>
              <th class="pr-6">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="report in reports" :key="report.id" class="group">
              <td class="pl-6">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ getReportTypeLabel(report.report_type) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Gerado em {{ formatDate(report.created_at) }}</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-dark-700 rounded-lg">
                  <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span class="text-sm text-gray-700 dark:text-gray-300">
                    {{ formatDate(report.period_start) }} - {{ formatDate(report.period_end) }}
                  </span>
                </div>
              </td>
              <td>
                <span class="font-semibold text-gray-900 dark:text-white">{{ report.total_records }}</span>
              </td>
              <td>
                <span class="inline-flex items-center gap-1 font-semibold text-success-600 dark:text-success-400">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ report.total_valid }}
                </span>
              </td>
              <td>
                <span class="inline-flex items-center gap-1 font-semibold text-danger-600 dark:text-danger-400">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ report.total_errors }}
                </span>
              </td>
              <td class="pr-6">
                <div class="flex items-center gap-2">
                  <button
                    @click="viewReport(report)"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Ver
                  </button>
                  <button
                    @click="exportReport(report, 'csv')"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-success-600 dark:text-success-400 hover:bg-success-50 dark:hover:bg-success-900/20 rounded-lg transition-colors"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    CSV
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal de visualização -->
    <Teleport to="body">
      <div v-if="selectedReport" class="modal-overlay" @click.self="selectedReport = null">
        <div class="modal-content max-w-3xl">
          <div class="modal-header">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ getReportTypeLabel(selectedReport.report_type) }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  {{ formatDate(selectedReport.period_start) }} a {{ formatDate(selectedReport.period_end) }}
                </p>
              </div>
            </div>
            <button 
              @click="selectedReport = null" 
              class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-dark-700 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="modal-body">
            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
              <div class="p-4 bg-gray-50 dark:bg-dark-700/50 rounded-xl text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total de Registros</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ selectedReport.total_records }}</p>
              </div>
              <div class="p-4 bg-success-50 dark:bg-success-900/20 rounded-xl text-center">
                <p class="text-sm text-success-600 dark:text-success-400 mb-1">Registros Válidos</p>
                <p class="text-2xl font-bold text-success-700 dark:text-success-300">{{ selectedReport.total_valid }}</p>
              </div>
              <div class="p-4 bg-danger-50 dark:bg-danger-900/20 rounded-xl text-center">
                <p class="text-sm text-danger-600 dark:text-danger-400 mb-1">Registros com Erro</p>
                <p class="text-2xl font-bold text-danger-700 dark:text-danger-300">{{ selectedReport.total_errors }}</p>
              </div>
              <div v-if="selectedReport.total_amount" class="p-4 bg-primary-50 dark:bg-primary-900/20 rounded-xl text-center">
                <p class="text-sm text-primary-600 dark:text-primary-400 mb-1">Total Faturado</p>
                <p class="text-2xl font-bold text-primary-700 dark:text-primary-300">{{ formatCurrency(selectedReport.total_amount) }}</p>
              </div>
            </div>

            <!-- Success Rate -->
            <div class="p-4 bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl">
              <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Taxa de Sucesso</span>
                <span class="text-lg font-bold text-gray-900 dark:text-white">
                  {{ getSuccessRate(selectedReport) }}%
                </span>
              </div>
              <div class="progress-bar h-3">
                <div 
                  class="h-full rounded-full transition-all duration-500"
                  :class="getSuccessRate(selectedReport) >= 80 ? 'bg-gradient-to-r from-success-500 to-success-600' : getSuccessRate(selectedReport) >= 50 ? 'bg-gradient-to-r from-warning-500 to-warning-600' : 'bg-gradient-to-r from-danger-500 to-danger-600'"
                  :style="{ width: getSuccessRate(selectedReport) + '%' }"
                ></div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button @click="selectedReport = null" class="btn-secondary">
              Fechar
            </button>
            <button @click="exportReport(selectedReport, 'csv')" class="btn-primary inline-flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              Exportar CSV
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, onMounted, h } from 'vue'
import api from '@/services/api'

const reports = ref([])
const selectedReport = ref(null)
const loading = ref(false)
const reportForm = ref({
  type: '',
  periodStart: '',
  periodEnd: '',
})

const IconSummary = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' })
  ])
}

const IconDetailed = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M4 6h16M4 10h16M4 14h16M4 18h16' })
  ])
}

const IconErrors = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
  ])
}

const IconValidation = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' })
  ])
}

const IconFinancial = {
  render: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
    h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })
  ])
}

const reportTypes = [
  { value: 'summary', label: 'Resumo', icon: IconSummary },
  { value: 'detailed', label: 'Detalhado', icon: IconDetailed },
  { value: 'errors', label: 'Erros', icon: IconErrors },
  { value: 'validation', label: 'Validações', icon: IconValidation },
  { value: 'financial', label: 'Financeiro', icon: IconFinancial },
]

const getSuccessRate = (report) => {
  if (!report.total_records || report.total_records === 0) return 0
  return Math.round((report.total_valid / report.total_records) * 100)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(value)
}

const getReportTypeLabel = (type) => {
  const labels = {
    summary: 'Resumo',
    detailed: 'Detalhado',
    errors: 'Erros',
    validation: 'Validações',
    financial: 'Financeiro',
  }
  return labels[type] || type
}

const generateReport = async () => {
  loading.value = true
  try {
    const response = await api.post('/reports', {
      report_type: reportForm.value.type,
      period_start: reportForm.value.periodStart,
      period_end: reportForm.value.periodEnd,
    })

    reports.value.unshift(response.data.data)
    reportForm.value = { type: '', periodStart: '', periodEnd: '' }
  } catch (error) {
    console.error('Erro ao gerar relatório:', error)
  } finally {
    loading.value = false
  }
}

const viewReport = (report) => {
  selectedReport.value = report
}

const exportReport = async (report, format) => {
  try {
    const response = await api.get(`/reports/${report.id}/export/${format}`, {
      responseType: 'blob',
    })

    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `relatorio-${report.id}.${format}`)
    document.body.appendChild(link)
    link.click()
    link.parentNode.removeChild(link)
  } catch (error) {
    console.error('Erro ao exportar relatório:', error)
  }
}

const loadReports = async () => {
  try {
    const response = await api.get('/reports')
    reports.value = response.data.data
  } catch (error) {
    console.error('Erro ao carregar relatórios:', error)
  }
}

onMounted(() => {
  loadReports()
})
</script>
