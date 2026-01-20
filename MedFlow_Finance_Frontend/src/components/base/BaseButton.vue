<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="buttonClasses"
    @click="handleClick"
  >
    <span v-if="loading" class="spinner"></span>
    <slot v-else></slot>
  </button>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'secondary', 'success', 'danger', 'warning', 'ghost'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  type: {
    type: String,
    default: 'button'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  fullWidth: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['click'])

const buttonClasses = computed(() => {
  const classes = ['base-button']
  
  // Variant
  classes.push(`base-button--${props.variant}`)
  
  // Size
  classes.push(`base-button--${props.size}`)
  
  // Full width
  if (props.fullWidth) {
    classes.push('base-button--full-width')
  }
  
  // Disabled/Loading
  if (props.disabled || props.loading) {
    classes.push('base-button--disabled')
  }
  
  return classes.join(' ')
})

const handleClick = (event) => {
  if (!props.disabled && !props.loading) {
    emit('click', event)
  }
}
</script>

<style scoped>
.base-button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 500;
  border-radius: 0.5rem;
  transition: all 0.2s;
  cursor: pointer;
  border: none;
  outline: none;
}

.base-button:focus {
  ring: 2px;
  ring-offset: 2px;
}

/* Sizes */
.base-button--sm {
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
}

.base-button--md {
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
}

.base-button--lg {
  padding: 1rem 2rem;
  font-size: 1.125rem;
}

/* Variants */
.base-button--primary {
  background-color: #3b82f6;
  color: white;
}

.base-button--primary:hover:not(.base-button--disabled) {
  background-color: #2563eb;
}

.base-button--secondary {
  background-color: #6b7280;
  color: white;
}

.base-button--secondary:hover:not(.base-button--disabled) {
  background-color: #4b5563;
}

.base-button--success {
  background-color: #10b981;
  color: white;
}

.base-button--success:hover:not(.base-button--disabled) {
  background-color: #059669;
}

.base-button--danger {
  background-color: #ef4444;
  color: white;
}

.base-button--danger:hover:not(.base-button--disabled) {
  background-color: #dc2626;
}

.base-button--warning {
  background-color: #f59e0b;
  color: white;
}

.base-button--warning:hover:not(.base-button--disabled) {
  background-color: #d97706;
}

.base-button--ghost {
  background-color: transparent;
  color: #3b82f6;
  border: 1px solid #3b82f6;
}

.base-button--ghost:hover:not(.base-button--disabled) {
  background-color: #eff6ff;
}

/* States */
.base-button--disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.base-button--full-width {
  width: 100%;
}

/* Spinner */
.spinner {
  display: inline-block;
  width: 1rem;
  height: 1rem;
  border: 2px solid currentColor;
  border-right-color: transparent;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
