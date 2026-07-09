<template>
  <div class="flex flex-col items-center justify-center py-12 px-4">
    <div class="mb-4 text-gray-400 dark:text-gray-600">
      <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="iconPath" />
      </svg>
    </div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ title }}</h3>
    <p class="text-gray-500 dark:text-gray-400 text-center max-w-md">{{ message }}</p>
    <slot name="action" />
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: 'Nenhum dado encontrado'
  },
  message: {
    type: String,
    default: 'Não há informações para exibir no momento'
  },
  iconPath: {
    type: String,
    default: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
  }
})
</script>
