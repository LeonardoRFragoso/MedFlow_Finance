<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Registros</h1>

    <!-- Filtros -->
    <div class="card mb-8">
      <h2 class="text-lg font-bold text-gray-900 mb-4">Filtros</h2>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select v-model="filters.status" class="input-field">
            <option value="">Todos</option>
            <option value="pending">Pendente</option>
            <option value="approved">Aprovado</option>
            <option value="rejected">Rejeitado</option>
            <option value="disputed">Disputado</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Paciente, CPF, código..."
            class="input-field"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Data Inicial</label>
          <input v-model="filters.dateFrom" type="date" class="input-field" />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Data Final</label>
          <input v-model="filters.dateTo" type="date" class="input-field" />
        </div>
      </div>
      <button @click="applyFilters" class="btn-primary mt-4">Filtrar</button>
    </div>

    <!-- Tabela de registros -->
    <div class="card">
      <div v-if="loading" class="text-center py-8 text-gray-500">
        Carregando registros...
      </div>
      <div v-else-if="records.length === 0" class="text-center py-8 text-gray-500">
        Nenhum registro encontrado
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200">
              <th class="text-left py-3 px-4 font-medium text-gray-700">Paciente</th>
              <th class="text-left py-3 px-4 font-medium text-gray-700">Procedimento</th>
              <th class="text-left py-3 px-4 font-medium text-gray-700">Data</th>
              <th class="text-left py-3 px-4 font-medium text-gray-700">Valor</th>
              <th class="text-left py-3 px-4 font-medium text-gray-700">Status</th>
              <th class="text-left py-3 px-4 font-medium text-gray-700">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="record in records" :key="record.id" class="border-b border-gray-100 hover:bg-gray-50">
              <td class="py-3 px-4 text-sm text-gray-900">{{ record.patient_name }}</td>
              <td class="py-3 px-4 text-sm text-gray-600">{{ record.procedure_code }}</td>
              <td class="py-3 px-4 text-sm text-gray-600">{{ formatDate(record.procedure_date) }}</td>
              <td class="py-3 px-4 text-sm text-gray-900 font-medium">
                {{ formatCurrency(record.amount_billed) }}
              </td>
              <td class="py-3 px-4 text-sm">
                <span :class="getStatusBadgeClass(record.status)">
                  {{ getStatusLabel(record.status) }}
                </span>
              </td>
              <td class="py-3 px-4 text-sm">
                <button
                  @click="viewRecord(record)"
                  class="text-primary-600 hover:text-primary-700 font-medium"
                >
                  Ver
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal de detalhes -->
    <div v-if="selectedRecord" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg max-w-2xl w-full max-h-96 overflow-y-auto">
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Detalhes do Registro</h2>
            <button @click="selectedRecord = null" class="text-gray-500 hover:text-gray-700">✕</button>
          </div>

          <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
              <p class="text-sm text-gray-600">Paciente</p>
              <p class="font-medium text-gray-900">{{ selectedRecord.patient_name }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">CPF</p>
              <p class="font-medium text-gray-900">{{ selectedRecord.patient_cpf }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Procedimento</p>
              <p class="font-medium text-gray-900">{{ selectedRecord.procedure_code }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Data</p>
              <p class="font-medium text-gray-900">{{ formatDate(selectedRecord.procedure_date) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Valor Faturado</p>
              <p class="font-medium text-gray-900">{{ formatCurrency(selectedRecord.amount_billed) }}</p>
            </div>
            <div>
              <p class="text-sm text-gray-600">Status</p>
              <p class="font-medium text-gray-900">{{ getStatusLabel(selectedRecord.status) }}</p>
            </div>
          </div>

          <div v-if="selectedRecord.validations && selectedRecord.validations.length > 0" class="mb-6">
            <h3 class="font-bold text-gray-900 mb-2">Validações</h3>
            <div class="space-y-2">
              <div
                v-for="validation in selectedRecord.validations"
                :key="validation.id"
                class="text-sm p-2 bg-gray-50 rounded"
              >
                <p class="font-medium text-gray-900">{{ validation.message }}</p>
                <p class="text-gray-600">{{ validation.rule_name }}</p>
              </div>
            </div>
          </div>

          <button @click="selectedRecord = null" class="btn-secondary w-full">Fechar</button>
        </div>
      </div>
    </div>
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
