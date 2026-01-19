<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="page-title flex items-center gap-3">
          <div class="icon-circle primary">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
          </div>
          Uploads
        </h1>
        <p class="page-subtitle">Faça upload de arquivos de faturamento médico</p>
      </div>
    </div>

    <!-- Formulário de upload -->
    <div class="card mb-8">
      <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
          </svg>
        </div>
        <div>
          <h2 class="section-title mb-0">Fazer Upload</h2>
          <p class="text-sm text-gray-500 dark:text-gray-400">Envie arquivos CSV ou Excel para processamento</p>
        </div>
      </div>

      <form @submit.prevent="handleUpload" class="space-y-6">
        <!-- File Upload Area -->
        <div>
          <label class="input-label">Arquivo</label>
          <div class="file-input-wrapper">
            <div 
              class="file-input-display"
              :class="{ 'has-file': uploadForm.file }"
            >
              <div v-if="!uploadForm.file" class="text-center">
                <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <p class="text-gray-600 dark:text-gray-300 font-medium">Clique ou arraste um arquivo aqui</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Formatos aceitos: CSV, Excel (.xlsx, .xls)</p>
              </div>
              <div v-else class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                  <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="font-semibold text-gray-900 dark:text-white">{{ uploadForm.file.name }}</p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">{{ formatFileSize(uploadForm.file.size) }}</p>
                </div>
                <button 
                  type="button" 
                  @click.stop="clearFile"
                  class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-dark-700 text-gray-500 hover:text-danger-600 transition-colors"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
            <input
              type="file"
              @change="handleFileSelect"
              accept=".csv,.xlsx,.xls"
              required
            />
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
              v-model="uploadForm.billingPeriodStart"
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
              v-model="uploadForm.billingPeriodEnd"
              type="date"
              class="input-field"
              required
            />
          </div>
        </div>

        <!-- Description -->
        <div>
          <label class="input-label">
            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
            Descrição (opcional)
          </label>
          <textarea
            v-model="uploadForm.description"
            class="input-field resize-none"
            rows="3"
            placeholder="Adicione uma descrição para identificar este upload..."
          ></textarea>
        </div>

        <!-- Error Message -->
        <div v-if="uploadsStore.error" class="flex items-center gap-3 p-4 bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-xl">
          <div class="flex-shrink-0 w-10 h-10 rounded-full bg-danger-100 dark:bg-danger-900/50 flex items-center justify-center">
            <svg class="w-5 h-5 text-danger-600 dark:text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <p class="font-medium text-danger-800 dark:text-danger-200">Erro no upload</p>
            <p class="text-sm text-danger-700 dark:text-danger-300">{{ uploadsStore.error }}</p>
          </div>
        </div>

        <!-- Progress Bar -->
        <div v-if="uploadProgress > 0 && uploadProgress < 100" class="space-y-2">
          <div class="flex justify-between text-sm">
            <span class="text-gray-600 dark:text-gray-400">Enviando arquivo...</span>
            <span class="font-medium text-primary-600 dark:text-primary-400">{{ uploadProgress }}%</span>
          </div>
          <div class="progress-bar">
            <div class="progress-bar-fill" :style="{ width: uploadProgress + '%' }"></div>
          </div>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          :disabled="uploadsStore.loading || !uploadForm.file"
          class="w-full btn-primary py-3.5 flex items-center justify-center gap-2"
        >
          <svg v-if="!uploadsStore.loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
          </svg>
          <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ uploadsStore.loading ? 'Enviando arquivo...' : 'Enviar Arquivo' }}
        </button>
      </form>
    </div>

    <!-- Lista de uploads -->
    <div class="card">
      <div class="flex items-center justify-between mb-6">
        <h2 class="section-title mb-0 flex items-center gap-2">
          <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          Histórico de Uploads
        </h2>
        <span class="badge-neutral">{{ uploadsStore.uploads.length }} arquivos</span>
      </div>

      <div v-if="uploadsStore.uploads.length === 0" class="empty-state">
        <div class="empty-state-icon">
          <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
          </svg>
        </div>
        <p class="empty-state-text">Nenhum upload realizado ainda</p>
        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Seus arquivos enviados aparecerão aqui</p>
      </div>

      <div v-else class="overflow-x-auto -mx-6">
        <table class="table-modern">
          <thead>
            <tr>
              <th class="pl-6">Arquivo</th>
              <th>Data</th>
              <th>Status</th>
              <th>Progresso</th>
              <th class="pr-6">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="upload in uploadsStore.uploads" :key="upload.id" class="group">
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
                  <span class="text-gray-600 dark:text-gray-300 font-medium text-sm">
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

const clearFile = () => {
  uploadForm.value.file = null
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
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

onMounted(() => {
  uploadsStore.fetchUploads()
})
</script>
