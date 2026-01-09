<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Relatórios</h1>

    <!-- Formulário de geração -->
    <div class="card mb-8">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Gerar Novo Relatório</h2>
      <form @submit.prevent="generateReport" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Relatório</label>
          <select v-model="reportForm.type" class="input-field" required>
            <option value="">Selecione um tipo</option>
            <option value="summary">Resumo</option>
            <option value="detailed">Detalhado</option>
            <option value="errors">Erros</option>
            <option value="validation">Validações</option>
            <option value="financial">Financeiro</option>
          </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Data Inicial</label>
            <input
              v-model="reportForm.periodStart"
              type="date"
              class="input-field"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Data Final</label>
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
          :disabled="loading"
          class="btn-primary disabled:opacity-50"
        >
          {{ loading ? 'Gerando...' : 'Gerar Relatório' }}
        </button>
      </form>
    </div>

    <!-- Lista de relatórios -->
    <div class="card">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Relatórios Gerados</h2>
      <div v-if="reports.length === 0" class="text-center py-8 text-gray-500">
        Nenhum relatório gerado ainda
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200">
              <th class="text-left py-3 px-4 font-medium text-gray-700">Tipo</th>
              <th class="text-left py-3 px-4 font-medium text-gray-700">Período</th>
              <th class="text-left py-3 px-4 font-medium text-gray-700">Registros</th>
              <th class="text-left py-3 px-4 font-medium text-gray-700">Válidos</th>
              <th class="text-left py-3 px-4 font-medium text-gray-700">Erros</th>
              <th class="text-left py-3 px-4 font-medium text-gray-700">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="report in reports" :key="report.id" class="border-b border-gray-100 hover:bg-gray-50">
              <td class="py-3 px-4 text-sm text-gray-900 font-medium">{{ getReportTypeLabel(report.report_type) }}</td>
              <td class="py-3 px-4 text-sm text-gray-600">
                {{ formatDate(report.period_start) }} a {{ formatDate(report.period_end) }}
              </td>
              <td class="py-3 px-4 text-sm text-gray-900">{{ report.total_records }}</td>
              <td class="py-3 px-4 text-sm text-success-600 font-medium">{{ report.total_valid }}</td>
              <td class="py-3 px-4 text-sm text-danger-600 font-medium">{{ report.total_errors }}</td>
              <td class="py-3 px-4 text-sm space-x-2">
                <button
                  @click="viewReport(report)"
                  class="text-primary-600 hover:text-primary-700 font-medium"
                >
                  Ver
                </button>
                <button
                  @click="exportReport(report, 'csv')"
                  class="text-primary-600 hover:text-primary-700 font-medium"
                >
                  CSV
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal de visualização -->
    <div v-if="selectedReport" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg max-w-4xl w-full max-h-96 overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">
              {{ getReportTypeLabel(selectedReport.report_type) }}
            </h2>
            <button @click="selectedReport = null" class="text-gray-500 hover:text-gray-700">✕</button>
          </div>

          <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
              <p class="text-sm text-gray-600">Período</p>
              <p class="font-medium text-gray-900">
                {{ formatDate(selectedReport.period_start) }} a {{ formatDate(selectedReport.period_end) }}
              </p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Total de Registros</p>
              <p class="font-medium text-gray-900">{{ selectedReport.total_records }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Registros Válidos</p>
              <p class="font-medium text-success-600">{{ selectedReport.total_valid }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Registros com Erro</p>
              <p class="font-medium text-danger-600">{{ selectedReport.total_errors }}</p>
            </div>
            <div v-if="selectedReport.total_amount">
              <p class="text-sm text-gray-600">Total Faturado</p>
              <p class="font-medium text-gray-900">{{ formatCurrency(selectedReport.total_amount) }}</p>
            </div>
          </div>

          <div class="flex gap-2">
            <button @click="exportReport(selectedReport, 'csv')" class="btn-primary">
              Exportar CSV
            </button>
            <button @click="selectedReport = null" class="btn-secondary">Fechar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const reports = ref([])
const selectedReport = ref(null)
const loading = ref(false)
const reportForm = ref({
  type: '',
  periodStart: '',
  periodEnd: '',
})

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
