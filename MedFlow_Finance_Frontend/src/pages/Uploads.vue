<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Uploads</h1>
      <router-link to="/uploads" class="btn-primary">
        + Novo Upload
      </router-link>
    </div>

    <!-- Formulário de upload -->
    <div class="card mb-8">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Fazer Upload</h2>
      <form @submit.prevent="handleUpload" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Arquivo</label>
          <input
            type="file"
            @change="handleFileSelect"
            accept=".csv,.xlsx,.xls"
            class="input-field"
            required
          />
          <p class="text-xs text-gray-500 mt-1">Formatos aceitos: CSV, Excel</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Data Inicial</label>
            <input
              v-model="uploadForm.billingPeriodStart"
              type="date"
              class="input-field"
              required
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Data Final</label>
            <input
              v-model="uploadForm.billingPeriodEnd"
              type="date"
              class="input-field"
              required
            />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Descrição (opcional)</label>
          <textarea
            v-model="uploadForm.description"
            class="input-field"
            rows="3"
            placeholder="Adicione uma descrição para este upload"
          ></textarea>
        </div>

        <div v-if="uploadsStore.error" class="bg-danger-50 border border-danger-200 text-danger-800 px-4 py-3 rounded">
          {{ uploadsStore.error }}
        </div>

        <button
          type="submit"
          :disabled="uploadsStore.loading || !uploadForm.file"
          class="w-full btn-primary disabled:opacity-50"
        >
          {{ uploadsStore.loading ? 'Enviando...' : 'Enviar Arquivo' }}
        </button>

        <div v-if="uploadProgress > 0 && uploadProgress < 100" class="w-full bg-gray-200 rounded-full h-2">
          <div
            class="bg-primary-600 h-2 rounded-full transition-all"
            :style="{ width: uploadProgress + '%' }"
          ></div>
        </div>
      </form>
    </div>

    <!-- Lista de uploads -->
    <div class="card">
      <h2 class="text-xl font-bold text-gray-900 mb-4">Histórico de Uploads</h2>
      <div v-if="uploadsStore.uploads.length === 0" class="text-center py-8 text-gray-500">
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
            <tr v-for="upload in uploadsStore.uploads" :key="upload.id" class="border-b border-gray-100 hover:bg-gray-50">
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
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useUploadsStore } from '@/stores/uploads'

const uploadsStore = useUploadsStore()
const uploadForm = ref({
  file: null,
  billingPeriodStart: '',
  billingPeriodEnd: '',
  description: '',
})
const uploadProgress = ref(0)

const handleFileSelect = (event) => {
  uploadForm.value.file = event.target.files[0]
}

const handleUpload = async () => {
  if (!uploadForm.value.file) return

  const success = await uploadsStore.uploadFile(
    uploadForm.value.file,
    uploadForm.value.billingPeriodStart,
    uploadForm.value.billingPeriodEnd,
    uploadForm.value.description
  )

  if (success) {
    uploadForm.value = {
      file: null,
      billingPeriodStart: '',
      billingPeriodEnd: '',
      description: '',
    }
    uploadProgress.value = 0
  }
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

onMounted(() => {
  uploadsStore.fetchUploads()
})
</script>
