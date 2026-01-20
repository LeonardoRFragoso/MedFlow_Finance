# 🎨 Componentes UI Reutilizáveis - MedFlow Finance

Documentação completa dos componentes base implementados no frontend.

---

## 📦 Componentes Base

### BaseButton

Botão reutilizável com múltiplas variantes e estados.

**Props:**
- `variant`: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'ghost'
- `size`: 'sm' | 'md' | 'lg'
- `type`: 'button' | 'submit' | 'reset'
- `disabled`: boolean
- `loading`: boolean
- `fullWidth`: boolean

**Uso:**
```vue
<template>
  <BaseButton 
    variant="primary" 
    size="md" 
    :loading="isLoading"
    @click="handleClick"
  >
    Salvar
  </BaseButton>
</template>

<script setup>
import BaseButton from '@/components/base/BaseButton.vue'
</script>
```

---

### BaseCard

Card container com header e footer opcionais.

**Props:**
- `title`: string
- `variant`: 'default' | 'bordered' | 'elevated'
- `padding`: 'none' | 'sm' | 'md' | 'lg'
- `hoverable`: boolean

**Slots:**
- `header`: Conteúdo customizado do header
- `default`: Conteúdo principal
- `footer`: Conteúdo do footer

**Uso:**
```vue
<template>
  <BaseCard title="Estatísticas" variant="elevated" padding="md">
    <template #header>
      <div class="flex justify-between">
        <h3>Título Customizado</h3>
        <button>Ação</button>
      </div>
    </template>
    
    <p>Conteúdo do card</p>
    
    <template #footer>
      <BaseButton>Ver Mais</BaseButton>
    </template>
  </BaseCard>
</template>
```

---

### BaseInput

Input de formulário com validação e estados.

**Props:**
- `modelValue`: string | number
- `label`: string
- `type`: string (default: 'text')
- `placeholder`: string
- `error`: string
- `hint`: string
- `disabled`: boolean
- `readonly`: boolean
- `required`: boolean
- `size`: 'sm' | 'md' | 'lg'

**Slots:**
- `prefix`: Ícone ou texto antes do input
- `suffix`: Ícone ou texto depois do input

**Uso:**
```vue
<template>
  <BaseInput
    v-model="email"
    label="E-mail"
    type="email"
    placeholder="seu@email.com"
    :error="emailError"
    required
  >
    <template #prefix>
      <MailIcon />
    </template>
  </BaseInput>
</template>

<script setup>
import { ref } from 'vue'
import BaseInput from '@/components/base/BaseInput.vue'

const email = ref('')
const emailError = ref('')
</script>
```

---

### BaseModal

Modal dialog com backdrop e animações.

**Props:**
- `modelValue`: boolean
- `title`: string
- `size`: 'sm' | 'md' | 'lg' | 'xl' | 'full'
- `closable`: boolean
- `closeOnBackdrop`: boolean
- `persistent`: boolean

**Slots:**
- `header`: Conteúdo customizado do header
- `default`: Conteúdo principal
- `footer`: Ações do footer

**Uso:**
```vue
<template>
  <BaseButton @click="showModal = true">Abrir Modal</BaseButton>
  
  <BaseModal 
    v-model="showModal" 
    title="Confirmar Ação"
    size="md"
  >
    <p>Tem certeza que deseja continuar?</p>
    
    <template #footer>
      <BaseButton variant="ghost" @click="showModal = false">
        Cancelar
      </BaseButton>
      <BaseButton variant="danger" @click="confirm">
        Confirmar
      </BaseButton>
    </template>
  </BaseModal>
</template>

<script setup>
import { ref } from 'vue'
import BaseModal from '@/components/base/BaseModal.vue'

const showModal = ref(false)

const confirm = () => {
  // Ação de confirmação
  showModal.value = false
}
</script>
```

---

## 🔔 Sistema de Notificações

### ToastNotification

Sistema de notificações toast com múltiplos tipos.

**Tipos:**
- `success`: Notificação de sucesso (verde)
- `error`: Notificação de erro (vermelho)
- `warning`: Notificação de aviso (amarelo)
- `info`: Notificação informativa (azul)

**Uso via Composable:**
```vue
<script setup>
import { useToast } from '@/composables/useToast'

const toast = useToast()

const handleSuccess = () => {
  toast.success('Operação realizada com sucesso!')
}

const handleError = () => {
  toast.error('Ocorreu um erro ao processar', 'Erro', 0) // 0 = não fecha automaticamente
}

const handleWarning = () => {
  toast.warning('Atenção: verifique os dados')
}

const handleInfo = () => {
  toast.info('Processamento iniciado', 'Info', 3000) // 3 segundos
}
</script>
```

**Uso Global:**
```javascript
// Em qualquer componente
this.$toast.addToast({
  type: 'success',
  title: 'Sucesso',
  message: 'Upload concluído!',
  duration: 5000
})
```

---

## 🔌 WebSocket - Notificações em Tempo Real

### Configuração

**1. Instalar no main.js:**
```javascript
import toastPlugin from '@/plugins/toast'

app.use(toastPlugin)
```

**2. Conectar ao WebSocket:**
```vue
<script setup>
import { onMounted } from 'vue'
import { useWebSocket } from '@/composables/useWebSocket'
import { useToast } from '@/composables/useToast'
import { useAuthStore } from '@/stores/auth'

const ws = useWebSocket()
const toast = useToast()
const authStore = useAuthStore()

onMounted(() => {
  // Conectar ao WebSocket
  const wsUrl = import.meta.env.VITE_WS_URL || 'ws://localhost:6001'
  ws.connect(wsUrl, authStore.token)
  
  // Escutar eventos
  ws.on('upload.processed', (data) => {
    toast.success(`Upload ${data.filename} processado!`)
  })
  
  ws.on('record.validated', (data) => {
    if (!data.is_valid) {
      toast.warning(`Registro ${data.record_id} com erros`)
    }
  })
  
  ws.on('notification', (data) => {
    toast.info(data.message, data.title)
  })
})
</script>
```

### Eventos Disponíveis

**upload.processed:**
```javascript
{
  upload_id: 'uuid',
  filename: 'arquivo.csv',
  status: 'completed',
  total_rows: 100,
  valid_rows: 95,
  error_rows: 5,
  processed_at: '2024-01-20T10:00:00Z'
}
```

**record.validated:**
```javascript
{
  record_id: 'uuid',
  upload_id: 'uuid',
  status: 'approved',
  is_valid: true,
  error_count: 0,
  validated_at: '2024-01-20T10:00:00Z'
}
```

**notification:**
```javascript
{
  type: 'upload_completed',
  title: 'Upload Concluído',
  message: 'Seu arquivo foi processado com sucesso!',
  data: { ... }
}
```

---

## 📱 Exemplos de Uso Completo

### Formulário com Validação
```vue
<template>
  <BaseCard title="Novo Upload" variant="elevated">
    <form @submit.prevent="handleSubmit">
      <BaseInput
        v-model="form.filename"
        label="Nome do Arquivo"
        :error="errors.filename"
        required
      />
      
      <BaseInput
        v-model="form.period_start"
        label="Período Inicial"
        type="date"
        :error="errors.period_start"
        required
      />
      
      <BaseInput
        v-model="form.period_end"
        label="Período Final"
        type="date"
        :error="errors.period_end"
        required
      />
      
      <template #footer>
        <BaseButton 
          type="submit" 
          variant="primary"
          :loading="isSubmitting"
          fullWidth
        >
          Enviar
        </BaseButton>
      </template>
    </form>
  </BaseCard>
</template>

<script setup>
import { ref } from 'vue'
import { useToast } from '@/composables/useToast'

const toast = useToast()
const form = ref({
  filename: '',
  period_start: '',
  period_end: ''
})
const errors = ref({})
const isSubmitting = ref(false)

const handleSubmit = async () => {
  isSubmitting.value = true
  try {
    // Enviar dados
    await api.post('/uploads', form.value)
    toast.success('Upload enviado com sucesso!')
  } catch (error) {
    toast.error('Erro ao enviar upload')
    errors.value = error.response.data.errors
  } finally {
    isSubmitting.value = false
  }
}
</script>
```

---

## 🎨 Customização

### Cores e Temas

Os componentes usam variáveis CSS que podem ser customizadas:

```css
:root {
  --color-primary: #3b82f6;
  --color-success: #10b981;
  --color-danger: #ef4444;
  --color-warning: #f59e0b;
  --color-info: #3b82f6;
}
```

### Estilos Globais

Adicione estilos globais em `src/assets/main.css`:

```css
.base-button {
  font-family: 'Inter', sans-serif;
}

.base-card {
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
```

---

## ✅ Checklist de Implementação

- [x] BaseButton component
- [x] BaseCard component
- [x] BaseInput component
- [x] BaseModal component
- [x] ToastNotification component
- [x] useToast composable
- [x] Toast plugin
- [x] WebSocket service
- [x] useWebSocket composable
- [x] Backend events (UploadProcessed, RecordValidated)
- [x] Backend notifications (UploadCompletedNotification)

---

**Status:** ✅ COMPLETO  
**Versão:** 1.0.0  
**Data:** 2026-01-20
