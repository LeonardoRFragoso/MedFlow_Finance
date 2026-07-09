<template>
  <div class="flex flex-col items-center justify-center py-12 px-4 bg-danger-50 dark:bg-danger-900/20 rounded-lg border border-danger-200 dark:border-danger-800">
    <div class="mb-4 text-danger-500">
      <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
    </div>
    <h3 class="text-lg font-medium text-danger-900 dark:text-danger-100 mb-2">{{ title }}</h3>
    <p class="text-danger-700 dark:text-danger-200 text-center max-w-md mb-4">{{ message }}</p>
    <slot name="action" />
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: 'Erro ao carregar dados'
  },
  message: {
    type: String,
    default: 'Ocorreu um erro ao processar sua solicitação. Tente novamente.'
  }
})
</script>
