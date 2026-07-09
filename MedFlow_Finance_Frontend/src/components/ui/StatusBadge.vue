<template>
  <span
    class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium"
    :class="badgeClass"
  >
    <svg v-if="icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icon" />
    </svg>
    {{ label }}
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    required: true
  }
})

const statusConfig = {
  pending: {
    label: 'Pendente',
    class: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
    icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
  },
  processing: {
    label: 'Processando',
    class: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
    icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'
  },
  completed: {
    label: 'Concluído',
    class: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
    icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
  },
  failed: {
    label: 'Falhou',
    class: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
    icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
  },
  approved: {
    label: 'Aprovado',
    class: 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
    icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
  },
  rejected: {
    label: 'Rejeitado',
    class: 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
    icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'
  },
  disputed: {
    label: 'Contestado',
    class: 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300',
    icon: 'M12 8v4m0 4v2m0 4v2M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
  }
}

const config = computed(() => statusConfig[props.status] || statusConfig.pending)
const label = computed(() => config.value.label)
const badgeClass = computed(() => config.value.class)
const icon = computed(() => config.value.icon)
</script>
