<template>
  <Teleport to="body">
    <div class="toast-container">
      <TransitionGroup name="toast">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          :class="['toast', `toast--${toast.type}`]"
        >
          <div class="toast__icon">
            <component :is="getIcon(toast.type)" />
          </div>
          <div class="toast__content">
            <h4 v-if="toast.title" class="toast__title">{{ toast.title }}</h4>
            <p class="toast__message">{{ toast.message }}</p>
          </div>
          <button class="toast__close" @click="removeToast(toast.id)">×</button>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, h } from 'vue'

const toasts = ref([])

const getIcon = (type) => {
  const icons = {
    success: () => h('svg', { class: 'toast__svg', viewBox: '0 0 20 20', fill: 'currentColor' }, [
      h('path', { 
        fillRule: 'evenodd', 
        d: 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z',
        clipRule: 'evenodd'
      })
    ]),
    error: () => h('svg', { class: 'toast__svg', viewBox: '0 0 20 20', fill: 'currentColor' }, [
      h('path', { 
        fillRule: 'evenodd', 
        d: 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z',
        clipRule: 'evenodd'
      })
    ]),
    warning: () => h('svg', { class: 'toast__svg', viewBox: '0 0 20 20', fill: 'currentColor' }, [
      h('path', { 
        fillRule: 'evenodd', 
        d: 'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z',
        clipRule: 'evenodd'
      })
    ]),
    info: () => h('svg', { class: 'toast__svg', viewBox: '0 0 20 20', fill: 'currentColor' }, [
      h('path', { 
        fillRule: 'evenodd', 
        d: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z',
        clipRule: 'evenodd'
      })
    ])
  }
  return icons[type] || icons.info
}

const addToast = (toast) => {
  const id = Date.now() + Math.random()
  const newToast = {
    id,
    type: toast.type || 'info',
    title: toast.title || '',
    message: toast.message,
    duration: toast.duration || 5000
  }
  
  toasts.value.push(newToast)
  
  if (newToast.duration > 0) {
    setTimeout(() => {
      removeToast(id)
    }, newToast.duration)
  }
}

const removeToast = (id) => {
  const index = toasts.value.findIndex(t => t.id === id)
  if (index > -1) {
    toasts.value.splice(index, 1)
  }
}

defineExpose({
  addToast,
  removeToast
})
</script>

<style scoped>
.toast-container {
  position: fixed;
  top: 1rem;
  right: 1rem;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  max-width: 400px;
}

.toast {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  padding: 1rem;
  background-color: white;
  border-radius: 0.5rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  border-left: 4px solid;
  min-width: 300px;
}

.toast--success {
  border-left-color: #10b981;
}

.toast--error {
  border-left-color: #ef4444;
}

.toast--warning {
  border-left-color: #f59e0b;
}

.toast--info {
  border-left-color: #3b82f6;
}

.toast__icon {
  flex-shrink: 0;
  width: 1.5rem;
  height: 1.5rem;
}

.toast--success .toast__icon {
  color: #10b981;
}

.toast--error .toast__icon {
  color: #ef4444;
}

.toast--warning .toast__icon {
  color: #f59e0b;
}

.toast--info .toast__icon {
  color: #3b82f6;
}

.toast__svg {
  width: 100%;
  height: 100%;
}

.toast__content {
  flex: 1;
}

.toast__title {
  font-size: 0.875rem;
  font-weight: 600;
  color: #111827;
  margin: 0 0 0.25rem 0;
}

.toast__message {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.toast__close {
  flex-shrink: 0;
  background: none;
  border: none;
  font-size: 1.5rem;
  line-height: 1;
  color: #9ca3af;
  cursor: pointer;
  padding: 0;
  width: 1.5rem;
  height: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 0.25rem;
  transition: all 0.2s;
}

.toast__close:hover {
  background-color: #f3f4f6;
  color: #6b7280;
}

/* Transitions */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%) scale(0.8);
}
</style>
