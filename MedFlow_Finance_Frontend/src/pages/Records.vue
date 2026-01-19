<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="page-title flex items-center gap-3">
          <div class="icon-circle primary">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          Registros
        </h1>
        <p class="page-subtitle">Gerencie os registros de faturamento médico</p>
      </div>
      <div class="flex items-center gap-2">
        <span class="badge-neutral">{{ records.length }} registros</span>
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
          <p class="text-sm text-gray-500 dark:text-gray-400">Refine sua busca por registros</p>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div>
          <label class="input-label">Status</label>
          <select v-model="filters.status" class="input-field select-field">
            <option value="">Todos os status</option>
            <option value="pending">Pendente</option>
            <option value="approved">Aprovado</option>
            <option value="rejected">Rejeitado</option>
            <option value="disputed">Disputado</option>
          </select>
        </div>
        <div>
          <label class="input-label">Buscar</label>
          <div class="relative">
            <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Paciente, CPF, código..."
              class="input-field pl-10"
            />
          </div>
        </div>
        <div>
          <label class="input-label">Data Inicial</label>
          <input v-model="filters.dateFrom" type="date" class="input-field" />
        </div>
        <div>
          <label class="input-label">Data Final</label>
          <input v-model="filters.dateTo" type="date" class="input-field" />
        </div>
      </div>

      <div class="flex flex-wrap gap-3">
        <button @click="applyFilters" class="btn-primary inline-flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          Aplicar Filtros
        </button>
        <button @click="clearFilters" class="btn-secondary inline-flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
          Limpar
        </button>
      </div>
    </div>

    <!-- Tabela de registros -->
    <div class="card">
      <div class="flex items-center justify-between mb-6">
        <h2 class="section-title mb-0 flex items-center gap-2">
          <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
          </svg>
          Lista de Registros
        </h2>
      </div>

      <div v-if="loading" class="empty-state">
        <div class="animate-pulse flex flex-col items-center">
          <div class="w-12 h-12 bg-gray-200 dark:bg-dark-700 rounded-full mb-4"></div>
          <div class="h-4 w-40 bg-gray-200 dark:bg-dark-700 rounded"></div>
        </div>
      </div>

      <div v-else-if="records.length === 0" class="empty-state">
        <div class="empty-state-icon">
          <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <p class="empty-state-text">Nenhum registro encontrado</p>
        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Tente ajustar os filtros ou faça um novo upload</p>
      </div>

      <div v-else class="overflow-x-auto -mx-6">
        <table class="table-modern">
          <thead>
            <tr>
              <th class="pl-6">Paciente</th>
              <th>Procedimento</th>
              <th>Data</th>
              <th>Valor</th>
              <th>Status</th>
              <th class="pr-6">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="record in records" :key="record.id" class="group">
              <td class="pl-6">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-dark-600 dark:to-dark-700 flex items-center justify-center text-gray-600 dark:text-gray-300 font-semibold text-sm">
                    {{ getPatientInitials(record.patient_name) }}
                  </div>
                  <div>
                    <p class="font-medium text-gray-900 dark:text-white">{{ record.patient_name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ record.patient_cpf || 'CPF não informado' }}</p>
                  </div>
                </div>
              </td>
              <td>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 dark:bg-dark-700 rounded-lg">
                  <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                  </svg>
                  <span class="font-mono text-sm text-gray-700 dark:text-gray-300">{{ record.procedure_code }}</span>
                </div>
              </td>
              <td>
                <div class="text-gray-900 dark:text-white">{{ formatDate(record.procedure_date) }}</div>
              </td>
              <td>
                <span class="font-semibold text-gray-900 dark:text-white">{{ formatCurrency(record.amount_billed) }}</span>
              </td>
              <td>
                <span :class="getStatusBadgeClass(record.status)">
                  {{ getStatusLabel(record.status) }}
                </span>
              </td>
              <td class="pr-6">
                <button
                  @click="viewRecord(record)"
                  class="inline-flex items-center gap-1 text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                  Ver
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal de detalhes -->
    <Teleport to="body">
      <div v-if="selectedRecord" class="modal-overlay" @click.self="selectedRecord = null">
        <div class="modal-content max-w-2xl">
          <div class="modal-header">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Detalhes do Registro</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Informações completas do procedimento</p>
              </div>
            </div>
            <button 
              @click="selectedRecord = null" 
              class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-dark-700 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="modal-body">
            <!-- Patient Info Card -->
            <div class="bg-gray-50 dark:bg-dark-700/50 rounded-xl p-4 mb-6">
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-lg">
                  {{ getPatientInitials(selectedRecord.patient_name) }}
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900 dark:text-white text-lg">{{ selectedRecord.patient_name }}</h3>
                  <p class="text-gray-500 dark:text-gray-400">CPF: {{ selectedRecord.patient_cpf || 'Não informado' }}</p>
                </div>
                <div class="ml-auto">
                  <span :class="getStatusBadgeClass(selectedRecord.status)" class="text-sm">
                    {{ getStatusLabel(selectedRecord.status) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-2 gap-4 mb-6">
              <div class="p-4 bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl">
                <div class="flex items-center gap-2 mb-2">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                  </svg>
                  <span class="text-sm text-gray-500 dark:text-gray-400">Procedimento</span>
                </div>
                <p class="font-mono font-semibold text-gray-900 dark:text-white">{{ selectedRecord.procedure_code }}</p>
              </div>
              <div class="p-4 bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl">
                <div class="flex items-center gap-2 mb-2">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span class="text-sm text-gray-500 dark:text-gray-400">Data</span>
                </div>
                <p class="font-semibold text-gray-900 dark:text-white">{{ formatDate(selectedRecord.procedure_date) }}</p>
              </div>
              <div class="p-4 bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl">
                <div class="flex items-center gap-2 mb-2">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span class="text-sm text-gray-500 dark:text-gray-400">Valor Faturado</span>
                </div>
                <p class="font-bold text-xl text-success-600 dark:text-success-400">{{ formatCurrency(selectedRecord.amount_billed) }}</p>
              </div>
              <div class="p-4 bg-white dark:bg-dark-800 border border-gray-200 dark:border-dark-600 rounded-xl">
                <div class="flex items-center gap-2 mb-2">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                </div>
                <p class="font-semibold text-gray-900 dark:text-white">{{ getStatusLabel(selectedRecord.status) }}</p>
              </div>
            </div>

            <!-- Validations -->
            <div v-if="selectedRecord.validations && selectedRecord.validations.length > 0">
              <h3 class="font-bold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Validações
              </h3>
              <div class="space-y-2">
                <div
                  v-for="validation in selectedRecord.validations"
                  :key="validation.id"
                  class="flex items-start gap-3 p-3 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-xl"
                >
                  <div class="w-8 h-8 rounded-lg bg-warning-100 dark:bg-warning-900/50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-warning-600 dark:text-warning-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-warning-800 dark:text-warning-200">{{ validation.message }}</p>
                    <p class="text-sm text-warning-600 dark:text-warning-400">{{ validation.rule_name }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button @click="selectedRecord = null" class="btn-secondary">
              Fechar
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const records = ref([])
const loading = ref(false)
const selectedRecord = ref(null)
const filters = ref({
  status: '',
  search: '',
  dateFrom: '',
  dateTo: '',
})

const formatCurrency = (value) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
  }).format(value)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('pt-BR')
}

const getPatientInitials = (name) => {
  if (!name) return '?'
  return name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase()
}

const clearFilters = () => {
  filters.value = {
    status: '',
    search: '',
    dateFrom: '',
    dateTo: '',
  }
  applyFilters()
}

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Pendente',
    approved: 'Aprovado',
    rejected: 'Rejeitado',
    disputed: 'Disputado',
  }
  return labels[status] || status
}

const getStatusBadgeClass = (status) => {
  const classes = {
    pending: 'badge-warning',
    approved: 'badge-success',
    rejected: 'badge-danger',
    disputed: 'badge-warning',
  }
  return classes[status] || 'badge-warning'
}

const applyFilters = async () => {
  loading.value = true
  try {
    const params = {}
    if (filters.value.status) params.status = filters.value.status
    if (filters.value.search) params.search = filters.value.search
    if (filters.value.dateFrom) params.date_from = filters.value.dateFrom
    if (filters.value.dateTo) params.date_to = filters.value.dateTo

    const response = await api.get('/records', { params })
    records.value = response.data.data
  } catch (error) {
    console.error('Erro ao carregar registros:', error)
  } finally {
    loading.value = false
  }
}

const viewRecord = async (record) => {
  try {
    const response = await api.get(`/records/${record.id}`)
    selectedRecord.value = response.data.data
  } catch (error) {
    console.error('Erro ao carregar detalhes:', error)
  }
}

onMounted(() => {
  applyFilters()
})
</script>
