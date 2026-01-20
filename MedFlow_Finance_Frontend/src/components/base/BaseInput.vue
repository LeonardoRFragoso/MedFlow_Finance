<template>
  <div class="base-input">
    <label v-if="label" :for="inputId" class="base-input__label">
      {{ label }}
      <span v-if="required" class="base-input__required">*</span>
    </label>
    
    <div class="base-input__wrapper">
      <span v-if="$slots.prefix" class="base-input__prefix">
        <slot name="prefix"></slot>
      </span>
      
      <input
        :id="inputId"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        :class="inputClasses"
        @input="handleInput"
        @blur="handleBlur"
        @focus="handleFocus"
      />
      
      <span v-if="$slots.suffix" class="base-input__suffix">
        <slot name="suffix"></slot>
      </span>
    </div>
    
    <p v-if="error" class="base-input__error">{{ error }}</p>
    <p v-else-if="hint" class="base-input__hint">{{ hint }}</p>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  hint: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  }
})

const emit = defineEmits(['update:modelValue', 'blur', 'focus'])

const inputId = ref(`input-${Math.random().toString(36).substr(2, 9)}`)

const inputClasses = computed(() => {
  const classes = ['base-input__field']
  
  classes.push(`base-input__field--${props.size}`)
  
  if (props.error) {
    classes.push('base-input__field--error')
  }
  
  if (props.disabled) {
    classes.push('base-input__field--disabled')
  }
  
  return classes.join(' ')
})

const handleInput = (event) => {
  emit('update:modelValue', event.target.value)
}

const handleBlur = (event) => {
  emit('blur', event)
}

const handleFocus = (event) => {
  emit('focus', event)
}
</script>

<style scoped>
.base-input {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.base-input__label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
}

.base-input__required {
  color: #ef4444;
  margin-left: 0.25rem;
}

.base-input__wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.base-input__field {
  width: 100%;
  border: 1px solid #d1d5db;
  border-radius: 0.5rem;
  background-color: white;
  transition: all 0.2s;
  outline: none;
}

.base-input__field:focus {
  border-color: #3b82f6;
  ring: 2px;
  ring-color: rgba(59, 130, 246, 0.2);
}

.base-input__field--sm {
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
}

.base-input__field--md {
  padding: 0.625rem 1rem;
  font-size: 1rem;
}

.base-input__field--lg {
  padding: 0.75rem 1.25rem;
  font-size: 1.125rem;
}

.base-input__field--error {
  border-color: #ef4444;
}

.base-input__field--error:focus {
  ring-color: rgba(239, 68, 68, 0.2);
}

.base-input__field--disabled {
  background-color: #f3f4f6;
  cursor: not-allowed;
  opacity: 0.6;
}

.base-input__prefix,
.base-input__suffix {
  position: absolute;
  display: flex;
  align-items: center;
  color: #6b7280;
}

.base-input__prefix {
  left: 0.75rem;
}

.base-input__suffix {
  right: 0.75rem;
}

.base-input__error {
  font-size: 0.875rem;
  color: #ef4444;
  margin: 0;
}

.base-input__hint {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}
</style>
