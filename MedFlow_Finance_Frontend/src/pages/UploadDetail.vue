<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <router-link to="/uploads" class="text-primary-600 hover:text-primary-700 mb-4 inline-block">
      ← Voltar
    </router-link>

    <div v-if="loading" class="text-center py-8 text-gray-500">
      Carregando detalhes do upload...
    </div>

    <div v-else-if="upload">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">{{ upload.original_filename }}</h1>

      <!-- Informações do upload -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card">
          <p class="text-gray-600 text-sm font-medium">Status</p>
          <p class="text-2xl font-bold mt-2" :class="getStatusColorClass(upload.status)">
            {{ getStatusLabel(upload.status) }}
          </p>
        </div>

        <div class="card">
          <p class="text-gray-600 text-sm font-medium">Total de Registros</p>
          <p class="text-2xl font-bold text-gray-900 mt-2">{{ upload.total_rows }}</p>
        </div>

        <div class="card">
          <p class="text-gray-600 text-sm font-medium">Registros Válidos</p>
          <p class="text-2xl font-bold text-success-600 mt-2">{{ upload.valid_rows }}</p>
        </div>

        <div class="card">
          <p class="text-gray-600 text-sm font-medium">Registros com Erro</p>
          <p class="text-2xl font-bold text-danger-600 mt-2">{{ upload.error_rows }}</p>
        </div>
      </div>

      <!-- Progresso -->
      <div class="card mb-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Progresso do Processamento</h2>
        <div class="w-full bg-gray-200 rounded-full h-4">
          <div
            class="bg-primary-600 h-4 rounded-full transition-all"
            :style="{ width: uploadProgress + '%' }"
          ></div>
        </div>
        <p class="text-sm text-gray-600 mt-2">{{ uploadProgress }}% concluído</p>
      </div>

      <!-- Erros -->
      <div v-if="upload.error_rows > 0" class="card mb-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Erros Encontrados</h2>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-gray-200">
                <th class="text-left py-3 px-4 font-medium text-gray-700">Linha</th>
                <th class="text-left py-3 px-4 font-medium text-gray-700">Tipo</th>
                <th class="text-left py-3 px-4 font-medium text-gray-700">Mensagem</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="error in errors.slice(0, 10)" :key="error.id" class="border-b border-gray-100">
                <td class="py-3 px-4 text-sm text-gray-900">{{ error.row_number }}</td>
                <td class="py-3 px-4 text-sm text-gray-600">{{ error.error_type }}</td>
                <td class="py-3 px-4 text-sm text-gray-600">{{ error.error_message }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-if="errors.length > 10" class="text-sm text-gray-600 mt-4">
          ... e mais {{ errors.length - 10 }} erros
        </p>
      </div>

      <!-- Registros válidos -->
      <div v-if="upload.valid_rows > 0" class="card">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Registros Válidos</h2>
        <router-link
          :to="`/records?upload_id=${upload.id}`"
          class="btn-primary"
        >
          Ver {{ upload.valid_rows }} registros
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import api from '@/services/api'

const route = useRoute()
const upload = ref(null)
const errors = ref([])
const loading = ref(true)
const uploadProgress = ref(0)

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Pendente',
    processing: 'Processando',
    completed: 'Concluído',
    failed: 'Erro',
  }
  return labels[status] || status
}

const getStatusColorClass = (status) => {
  const classes = {
    pending: 'text-warning-600',
    processing: 'text-warning-600',
    completed: 'text-success-600',
    failed: 'text-danger-600',
  }
  return classes[status] || 'text-gray-900'
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
