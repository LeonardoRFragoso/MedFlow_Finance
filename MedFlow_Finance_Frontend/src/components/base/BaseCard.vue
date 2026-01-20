<template>
  <div :class="cardClasses">
    <div v-if="$slots.header || title" class="base-card__header">
      <slot name="header">
        <h3 class="base-card__title">{{ title }}</h3>
      </slot>
    </div>
    
    <div class="base-card__body">
      <slot></slot>
    </div>
    
    <div v-if="$slots.footer" class="base-card__footer">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    default: ''
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'bordered', 'elevated'].includes(value)
  },
  padding: {
    type: String,
    default: 'md',
    validator: (value) => ['none', 'sm', 'md', 'lg'].includes(value)
  },
  hoverable: {
    type: Boolean,
    default: false
  }
})

const cardClasses = computed(() => {
  const classes = ['base-card']
  
  classes.push(`base-card--${props.variant}`)
  classes.push(`base-card--padding-${props.padding}`)
  
  if (props.hoverable) {
    classes.push('base-card--hoverable')
  }
  
  return classes.join(' ')
})
</script>

<style scoped>
.base-card {
  background-color: white;
  border-radius: 0.75rem;
  overflow: hidden;
}

/* Variants */
.base-card--default {
  background-color: white;
}

.base-card--bordered {
  border: 1px solid #e5e7eb;
}

.base-card--elevated {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Padding */
.base-card--padding-none .base-card__body {
  padding: 0;
}

.base-card--padding-sm .base-card__body {
  padding: 0.75rem;
}

.base-card--padding-md .base-card__body {
  padding: 1.5rem;
}

.base-card--padding-lg .base-card__body {
  padding: 2rem;
}

/* Hoverable */
.base-card--hoverable {
  transition: all 0.2s;
  cursor: pointer;
}

.base-card--hoverable:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Header */
.base-card__header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  background-color: #f9fafb;
}

.base-card__title {
  font-size: 1.125rem;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

/* Footer */
.base-card__footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid #e5e7eb;
  background-color: #f9fafb;
}
</style>
