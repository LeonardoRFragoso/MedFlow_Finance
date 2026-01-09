<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Dashboard</h1>

    <!-- Métricas principais -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="card">
        <p class="text-gray-600 text-sm font-medium">Total Faturado</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">
          {{ formatCurrency(dashboardData.financial.total_billed) }}
        </p>
      </div>

      <div class="card">
        <p class="text-gray-600 text-sm font-medium">Registros Válidos</p>
        <p class="text-3xl font-bold text-success-600 mt-2">
          {{ dashboardData.records.approved }}
        </p>
        <p class="text-xs text-gray-500 mt-1">
          {{ successRate }}% de sucesso
        </p>
      </div>

      <div class="card">
        <p class="text-gray-600 text-sm font-medium">Registros com Erro</p>
        <p class="text-3xl font-bold text-danger-600 mt-2">
          {{ dashboardData.records.rejected }}
        </p>
      </div>

      <div class="card">
        <p class="text-gray-600 text-sm font-medium">Alertas de Glosa</p>
        <p class="text-3xl font-bold text-warning-600 mt-2">
          {{ dashboardData.records.disputed }}
        </p>
      </div>
    </div>

    <!-- Uploads recentes -->
    <div class="card mb-8">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Uploads Recentes</h2>
      <div v-if="recentUploads.length === 0" class="text-center py-8 text-gray-500">
        Nenhum upload realizado ainda
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200">
              <th class="text-left py-3 px-4 font-medium text-gray-700">Arquivo</th>
              <th class="text-left py-3 px-4 font-medium text-gray-700">Data</th>
              <th class="text-left py-3 px-4 font-medium text-gray-700">Status</th>
              <th class="text-left py-3 px-4 font-medium text-gray-700">Registros</th>
              <th class="text-left py-3 px-4 font-medium text-gray-700">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="upload in recentUploads" :key="upload.id" class="border-b border-gray-100 hover:bg-gray-50">
              <td class="py-3 px-4 text-sm text-gray-900">{{ upload.original_filename }}</td>
              <td class="py-3 px-4 text-sm text-gray-600">{{ formatDate(upload.created_at) }}</td>
              <td class="py-3 px-4 text-sm">
                <span :class="getStatusBadgeClass(upload.status)">
                  {{ getStatusLabel(upload.status) }}
                </span>
              </td>
              <td class="py-3 px-4 text-sm text-gray-600">
                {{ upload.valid_rows }}/{{ upload.total_rows }}
              </td>
              <td class="py-3 px-4 text-sm">
                <router-link
                  :to="`/uploads/${upload.id}`"
                  class="text-primary-600 hover:text-primary-700 font-medium"
                >
                  Ver detalhes
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Botão de novo upload -->
    <div class="text-center">
      <router-link to="/uploads" class="btn-primary">
        + Novo Upload
      </router-link>
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
