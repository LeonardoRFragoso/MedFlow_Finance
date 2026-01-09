# MedFlow Finance - Frontend Vue 3

SaaS B2B de Automação e Faturamento Médico - Frontend Vue 3 com Vite

## 🚀 Setup Inicial

### Pré-requisitos
- Node.js 18+
- npm ou yarn

### Instalação

1. **Instalar dependências**
```bash
npm install
```

2. **Configurar variáveis de ambiente**
```bash
cp .env.example .env.local
```

Editar `.env.local`:
```
VITE_API_BASE_URL=http://localhost:8000/api
```

3. **Iniciar servidor de desenvolvimento**
```bash
npm run dev
```

O frontend estará disponível em `http://localhost:5173`

## 📁 Estrutura do Projeto

```
src/
├── components/          # Componentes reutilizáveis
│   └── Navbar.vue
├── pages/               # Páginas da aplicação
│   ├── Login.vue
│   ├── Dashboard.vue
│   ├── Uploads.vue
│   ├── UploadDetail.vue
│   ├── Records.vue
│   └── Reports.vue
├── stores/              # Pinia stores (state management)
│   ├── auth.js
│   └── uploads.js
├── services/            # Serviços de API
│   └── api.js
├── router/              # Vue Router
│   └── index.js
├── App.vue              # Componente raiz
├── main.js              # Entrada da aplicação
└── style.css            # Estilos globais (Tailwind)
```

## 🔐 Autenticação

### Login
- Email: `admin@medflow.local`
- Senha: `password`

O token é armazenado em `localStorage` e automaticamente incluído em todas as requisições.

## 📊 Páginas Principais

### Dashboard
- Métricas principais (faturamento, registros válidos/inválidos)
- Uploads recentes
- Status do processamento

### Uploads
- Formulário para upload de arquivos (CSV, Excel)
- Histórico de uploads
- Status em tempo real

### Upload Detail
- Detalhes do processamento
- Progresso visual
- Lista de erros
- Link para registros válidos

### Records
- Lista de registros com filtros
- Busca por paciente, CPF, procedimento
- Visualização de detalhes
- Validações e erros por registro

### Reports
- Geração de relatórios (5 tipos)
- Visualização de relatórios
- Exportação em CSV

## 🛠️ Stack Tecnológico

- **Vue 3** - Framework reativo
- **Vite** - Build tool rápido
- **Pinia** - State management
- **Vue Router** - Roteamento
- **Axios** - Cliente HTTP
- **Tailwind CSS** - Estilos

## 📦 Build para Produção

```bash
npm run build
```

Saída em `dist/`

## 🧪 Desenvolvimento

### Estrutura de Componentes

Componentes são organizados por funcionalidade:
- `components/` - Componentes reutilizáveis
- `pages/` - Páginas completas

### Stores (Pinia)

Gerenciam estado da aplicação:
- `auth.js` - Autenticação e usuário
- `uploads.js` - Uploads e processamento

### Serviços

- `api.js` - Cliente Axios com interceptadores

## 🔄 Fluxo de Dados

```
User → Login → Token → API Calls → Pinia Store → Components
```

## 📱 Responsividade

Todas as páginas são responsivas:
- Desktop (1024px+)
- Tablet (768px-1023px)
- Mobile (< 768px)

## 🎨 Design System

### Cores
- Primary: `#0ea5e9` (azul)
- Success: `#10b981` (verde)
- Warning: `#f59e0b` (amarelo)
- Danger: `#ef4444` (vermelho)

### Componentes
- `.btn-primary` - Botão primário
- `.btn-secondary` - Botão secundário
- `.btn-danger` - Botão de perigo
- `.card` - Card com sombra
- `.input-field` - Campo de entrada
- `.badge-*` - Badges coloridas

## 🚀 Próximos Passos

- [ ] Adicionar gráficos (Chart.js)
- [ ] Implementar notificações em tempo real
- [ ] Adicionar mais filtros
- [ ] Melhorar UX mobile
- [ ] Testes unitários
- [ ] Documentação de componentes

## 📝 Notas Importantes

1. **CORS**: Configurado em `vite.config.js` para proxy `/api`
2. **Token**: Armazenado em `localStorage`, removido ao fazer logout
3. **Interceptadores**: Axios redireciona para login se token expirar (401)
4. **Paginação**: Padrão 15 itens por página
5. **Formatação**: Datas em `pt-BR`, valores monetários em BRL

## 🤝 Contribuindo

1. Criar branch para feature
2. Seguir padrão de código existente
3. Testar em desktop e mobile
4. Fazer commit com mensagem clara

## 📞 Suporte

Para dúvidas ou problemas, consulte a documentação do backend em `../MedFlow_Finance_Backend/`
