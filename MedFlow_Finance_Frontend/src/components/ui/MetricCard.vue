<template>
  <div class="p-6 bg-white dark:bg-dark-800 rounded-lg border border-gray-200 dark:border-dark-600 shadow-sm hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between mb-4">
      <div>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ label }}</p>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ formattedValue }}</p>
      </div>
      <div v-if="icon" class="w-12 h-12 rounded-lg flex items-center justify-center" :class="iconBgClass">
        <component :is="icon" class="w-6 h-6" :class="iconColorClass" />
      </div>
    </div>
    <div v-if="trend" class="flex items-center gap-2 text-sm">
      <span :class="trend > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
        {{ trend > 0 ? '+' : '' }}{{ trend }}%
      </span>
      <span class="text-gray-500 dark:text-gray-400">vs período anterior</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  label: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number],
    required: true
  },
  format: {
    type: String,
    default: 'text'
  },
  trend: {
    type: Number,
    default: null
  },
  icon: {
    type: Object,
    default: null
  },
  iconColor: {
    type: String,
    default: 'primary'
  }
})

const formattedValue = computed(() => {
  if (props.format === 'currency') {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL'
    }).format(props.value)
  }
  if (props.format === 'percentage') {
    return `${props.value}%`
  }
  if (props.format === 'number') {
    return new Intl.NumberFormat('pt-BR').format(props.value)
  }
  return props.value
})

const iconBgClass = computed(() => {
  const colors = {
    primary: 'bg-primary-100 dark:bg-primary-900/30',
    success: 'bg-green-100 dark:bg-green-900/30',
    danger: 'bg-red-100 dark:bg-red-900/30',
    warning: 'bg-yellow-100 dark:bg-yellow-900/30',
  }
  return colors[props.iconColor] || colors.primary
})

const iconColorClass = computed(() => {
  const colors = {
    primary: 'text-primary-600 dark:text-primary-400',
    success: 'text-green-600 dark:text-green-400',
    danger: 'text-red-600 dark:text-red-400',
    warning: 'text-yellow-600 dark:text-yellow-400',
  }
  return colors[props.iconColor] || colors.primary
})
</script>
