# ESCOPO TÉCNICO DO MVP - MEDFLOW FINANCE

**Data:** Janeiro 2026  
**Status:** Definição completa - Pronto para desenvolvimento  
**Versão:** 1.0

---

## 1. VISÃO GERAL DO ESCOPO

O MVP é uma ferramenta de **faturamento e validação financeira** para clínicas médicas. Foco exclusivo em:
- ✅ Upload e processamento de arquivos
- ✅ Validação automática de dados
- ✅ Detecção de inconsistências
- ✅ Dashboard financeiro básico
- ✅ Exportação de relatórios

**Exclusões claras:**
- ❌ Prontuário eletrônico
- ❌ Telemedicina
- ❌ App para paciente
- ❌ Integração com sistemas clínicos
- ❌ IA/Machine Learning
- ❌ Integrações externas (APIs de terceiros)

---

## 2. FUNCIONALIDADES DO BACKEND

### 2.1 Autenticação & Autorização (MUST HAVE)

#### Funcionalidade: Login & Logout
- **Descrição:** Usuário faz login com email/senha
- **Endpoints:**
  - `POST /api/auth/login` - Login
  - `POST /api/auth/logout` - Logout
  - `POST /api/auth/refresh` - Renovar token
  - `GET /api/auth/me` - Dados do usuário atual
- **Critério de aceite:**
  - ✅ Login retorna token válido
  - ✅ Token expira em 24h
  - ✅ Logout revoga token
  - ✅ Refresh gera novo token
  - ✅ Endpoints protegidos retornam 401 sem token
- **Segurança:**
  - ✅ Senha com hash bcrypt
  - ✅ Rate limiting (5 tentativas/5min)
  - ✅ Log de login/logout

#### Funcionalidade: Controle de Acesso (RBAC)
- **Descrição:** Sistema de roles e permissões
- **Roles:**
  - `admin` - Acesso total
  - `financial_manager` - Uploads, relatórios, validações
  - `administrative` - Uploads, visualização
  - `viewer` - Visualização apenas
- **Endpoints:**
  - `GET /api/users` - Listar usuários (admin)
  - `POST /api/users` - Criar usuário (admin)
  - `PUT /api/users/{id}` - Atualizar usuário (admin)
  - `DELETE /api/users/{id}` - Deletar usuário (admin)
  - `POST /api/users/{id}/roles` - Atribuir role (admin)
- **Critério de aceite:**
  - ✅ Usuário sem permissão recebe 403
  - ✅ Roles são verificadas em todas as ações
  - ✅ Permissões são auditadas

#### Funcionalidade: Isolamento de Tenant
- **Descrição:** Cada clínica vê apenas seus dados
- **Implementação:**
  - ✅ Middleware injeta tenant_id
  - ✅ Todas as queries filtram por tenant_id
  - ✅ Soft deletes preservam dados
- **Critério de aceite:**
  - ✅ Usuário de clínica A não vê dados de clínica B
  - ✅ Testes de segurança passam
  - ✅ Auditoria registra acessos

---

### 2.2 Gerenciamento de Clínicas (MUST HAVE)

#### Funcionalidade: Cadastro de Clínica
- **Descrição:** Admin cria nova clínica no sistema
- **Endpoints:**
  - `POST /api/clinics` - Criar clínica
  - `GET /api/clinics/{id}` - Visualizar clínica
  - `PUT /api/clinics/{id}` - Atualizar clínica
  - `GET /api/clinics` - Listar clínicas (admin)
- **Campos obrigatórios:**
  - Nome, CNPJ, Email, Telefone, Endereço
- **Critério de aceite:**
  - ✅ CNPJ validado (formato)
  - ✅ Email validado
  - ✅ Clínica criada com dados corretos
  - ✅ Usuário admin criado automaticamente

#### Funcionalidade: Configurações de Clínica
- **Descrição:** Clínica configura suas preferências
- **Endpoints:**
  - `GET /api/clinics/{id}/settings` - Visualizar configurações
  - `PUT /api/clinics/{id}/settings` - Atualizar configurações
- **Configurações:**
  - Tipo de faturamento (privado, público, misto)
  - Moeda padrão
  - Regras de validação ativas
  - Retenção de dados
- **Critério de aceite:**
  - ✅ Configurações salvas corretamente
  - ✅ Aplicadas ao processar uploads

---

### 2.3 Upload & Armazenamento (MUST HAVE)

#### Funcionalidade: Upload de Arquivo
- **Descrição:** Usuário faz upload de arquivo de faturamento
- **Endpoints:**
  - `POST /api/uploads` - Upload de arquivo
  - `GET /api/uploads` - Listar uploads
  - `GET /api/uploads/{id}` - Detalhes do upload
  - `DELETE /api/uploads/{id}` - Deletar upload
- **Formatos suportados:**
  - Excel (.xlsx, .xls)
  - CSV (.csv)
  - XML (.xml) - Roadmap
- **Validações:**
  - ✅ Tipo de arquivo validado
  - ✅ Tamanho máximo: 100MB
  - ✅ Estrutura básica validada
  - ✅ Deduplicação por hash
- **Critério de aceite:**
  - ✅ Arquivo armazenado em S3/Minio
  - ✅ Registro criado no banco
  - ✅ Job disparado para processamento
  - ✅ Status retornado ao frontend
  - ✅ Usuário sem permissão recebe 403

#### Funcionalidade: Processamento Assíncrono
- **Descrição:** Backend processa arquivo em background
- **Fluxo:**
  1. Upload recebido
  2. Job disparado (Redis Queue)
  3. Arquivo parseado
  4. Dados normalizados
  5. Validações executadas
  6. Registros armazenados
  7. Relatório gerado
- **Critério de aceite:**
  - ✅ Job executado sem erros
  - ✅ Timeout de 300s
  - ✅ Retry automático em caso de falha
  - ✅ Status atualizado no banco

#### Funcionalidade: Monitoramento de Upload
- **Descrição:** Frontend monitora progresso do upload
- **Endpoints:**
  - `GET /api/uploads/{id}/status` - Status atual
  - `GET /api/uploads/{id}/progress` - Progresso (%)
- **Critério de aceite:**
  - ✅ Status atualizado em tempo real
  - ✅ Progresso reflete etapas de processamento
  - ✅ Erros comunicados ao frontend

---

### 2.4 Parsing & Normalização (MUST HAVE)

#### Funcionalidade: Parser de Excel
- **Descrição:** Extrai dados de arquivo Excel
- **Suporte:**
  - ✅ Múltiplas abas
  - ✅ Headers customizados
  - ✅ Tipos de dados variados
- **Critério de aceite:**
  - ✅ Dados extraídos corretamente
  - ✅ Tipos de dados preservados
  - ✅ Erros de parse registrados
  - ✅ Testes com arquivos reais

#### Funcionalidade: Parser de CSV
- **Descrição:** Extrai dados de arquivo CSV
- **Suporte:**
  - ✅ Delimitadores (vírgula, ponto-e-vírgula, tab)
  - ✅ Encoding (UTF-8, Latin-1)
  - ✅ Quoted fields
- **Critério de aceite:**
  - ✅ Dados extraídos corretamente
  - ✅ Delimitadores detectados automaticamente
  - ✅ Erros de parse registrados

#### Funcionalidade: Normalização de Dados
- **Descrição:** Padroniza dados extraídos
- **Transformações:**
  - ✅ Trim de espaços
  - ✅ Conversão de tipos
  - ✅ Formatação de datas (DD/MM/YYYY → YYYY-MM-DD)
  - ✅ Formatação de valores monetários
  - ✅ Normalização de CPF/CNPJ
- **Critério de aceite:**
  - ✅ Dados normalizados corretamente
  - ✅ Valores inválidos marcados como erro
  - ✅ Histórico preservado em raw_data

---

### 2.5 Validações & Regras (MUST HAVE)

#### Funcionalidade: Validação de Campos
- **Descrição:** Valida campos obrigatórios e tipos
- **Validações:**
  - ✅ Campos obrigatórios presentes
  - ✅ Tipos de dados corretos
  - ✅ Ranges válidos
  - ✅ Formatos válidos (CPF, CNPJ, data, etc)
- **Critério de aceite:**
  - ✅ Erros registrados para campos inválidos
  - ✅ Relatório gerado com detalhes
  - ✅ Severidade diferenciada (error vs warning)

#### Funcionalidade: Validação de Negócio
- **Descrição:** Valida regras de faturamento
- **Validações:**
  - ✅ Código de procedimento válido (TUSS)
  - ✅ Valor dentro de range esperado
  - ✅ Data de procedimento válida
  - ✅ Duplicatas detectadas
  - ✅ Referências válidas (paciente, prestador)
- **Critério de aceite:**
  - ✅ Regras aplicadas corretamente
  - ✅ Erros e warnings diferenciados
  - ✅ Configuráveis por clínica

#### Funcionalidade: Detecção de Glosas
- **Descrição:** Identifica possíveis glosas
- **Detecção:**
  - ✅ Procedimentos fora de cobertura
  - ✅ Valores acima da tabela
  - ✅ Inconsistências de autorização
  - ✅ Duplicatas de procedimento
- **Critério de aceite:**
  - ✅ Glosas detectadas com acurácia
  - ✅ Mensagens claras para usuário
  - ✅ Sugestões de correção quando possível

#### Funcionalidade: Motor de Regras Extensível
- **Descrição:** Sistema que permite adicionar regras facilmente
- **Arquitetura:**
  - ✅ Rule abstrata com interface padrão
  - ✅ RuleSet para agrupar regras
  - ✅ Executor que aplica regras em sequência
  - ✅ Configuração por clínica
- **Critério de aceite:**
  - ✅ Novas regras adicionáveis sem modificar core
  - ✅ Regras testáveis isoladamente
  - ✅ Performance aceitável (< 5s para 1000 registros)

---

### 2.6 Armazenamento de Dados (MUST HAVE)

#### Funcionalidade: Armazenamento de Records
- **Descrição:** Persiste registros normalizados no banco
- **Campos armazenados:**
  - ✅ Dados do paciente (nome, CPF)
  - ✅ Dados do procedimento (código, data, valor)
  - ✅ Dados do prestador (nome, ID)
  - ✅ Dados do convênio (nome, ID)
  - ✅ Status (pending, approved, rejected)
  - ✅ Dados brutos (JSON)
- **Critério de aceite:**
  - ✅ Records armazenados corretamente
  - ✅ Relacionamento com upload mantido
  - ✅ Soft deletes funcionando
  - ✅ Auditoria registrando mudanças

#### Funcionalidade: Armazenamento de Validações
- **Descrição:** Persiste resultado das validações
- **Informações:**
  - ✅ Regra aplicada
  - ✅ Resultado (válido/inválido)
  - ✅ Severidade (error/warning)
  - ✅ Mensagem detalhada
  - ✅ Valores esperados vs reais
- **Critério de aceite:**
  - ✅ Validações armazenadas corretamente
  - ✅ Relacionamento com record mantido
  - ✅ Histórico preservado

#### Funcionalidade: Armazenamento de Erros
- **Descrição:** Registra erros encontrados
- **Informações:**
  - ✅ Tipo de erro (parse, validation, system)
  - ✅ Mensagem detalhada
  - ✅ Contexto (linha, campo, valor)
  - ✅ Stack trace (para debugging)
  - ✅ Status (new, acknowledged, resolved)
- **Critério de aceite:**
  - ✅ Erros armazenados com contexto
  - ✅ Usuário pode marcar como resolvido
  - ✅ Histórico preservado

---

### 2.7 Relatórios & Exportação (MUST HAVE)

#### Funcionalidade: Geração de Relatório de Validação
- **Descrição:** Cria relatório com resultado das validações
- **Conteúdo:**
  - ✅ Total de registros processados
  - ✅ Total de registros válidos
  - ✅ Total de registros com erro
  - ✅ Total de registros com warning
  - ✅ Lista de erros por tipo
  - ✅ Sugestões de correção
- **Critério de aceite:**
  - ✅ Relatório gerado automaticamente após processamento
  - ✅ Dados precisos
  - ✅ Armazenado no banco
  - ✅ Acessível via API

#### Funcionalidade: Dashboard Financeiro Básico
- **Descrição:** Visualização resumida de dados financeiros
- **Métricas:**
  - ✅ Total faturado (período)
  - ✅ Total aprovado
  - ✅ Total pendente
  - ✅ Total com erro
  - ✅ Quantidade de procedimentos
  - ✅ Valor médio por procedimento
  - ✅ Top 10 procedimentos
- **Filtros:**
  - ✅ Por período (data início/fim)
  - ✅ Por status
  - ✅ Por tipo de procedimento
- **Critério de aceite:**
  - ✅ Dados precisos
  - ✅ Performance < 2s
  - ✅ Filtros funcionando

#### Funcionalidade: Exportação de Relatório (CSV)
- **Descrição:** Exporta relatório em CSV
- **Conteúdo:**
  - ✅ Dados dos registros
  - ✅ Resultado das validações
  - ✅ Erros encontrados
- **Critério de aceite:**
  - ✅ CSV gerado corretamente
  - ✅ Encoding UTF-8
  - ✅ Delimitadores corretos
  - ✅ Link de download funciona

#### Funcionalidade: Exportação de Relatório (PDF) - NICE TO HAVE
- **Descrição:** Exporta relatório em PDF
- **Conteúdo:**
  - ✅ Resumo executivo
  - ✅ Gráficos de distribuição
  - ✅ Tabelas de dados
  - ✅ Recomendações
- **Critério de aceite:**
  - ✅ PDF gerado corretamente
  - ✅ Formatação profissional
  - ✅ Link de download funciona

---

### 2.8 Auditoria & Logs (MUST HAVE)

#### Funcionalidade: Log de Auditoria
- **Descrição:** Registra todas as ações no sistema
- **Eventos auditados:**
  - ✅ Login/Logout
  - ✅ Upload de arquivo
  - ✅ Processamento de arquivo
  - ✅ Criação/Atualização/Deleção de registros
  - ✅ Alteração de configurações
  - ✅ Mudança de permissões
- **Informações registradas:**
  - ✅ Usuário
  - ✅ Ação
  - ✅ Recurso
  - ✅ Valores antigos vs novos
  - ✅ IP e User-Agent
  - ✅ Timestamp
- **Critério de aceite:**
  - ✅ Todos os eventos auditados
  - ✅ Dados imutáveis
  - ✅ Retenção de 7 anos
  - ✅ Acessível apenas para admin

#### Funcionalidade: Logs de Erro
- **Descrição:** Registra erros de processamento
- **Informações:**
  - ✅ Tipo de erro
  - ✅ Mensagem
  - ✅ Stack trace
  - ✅ Contexto (upload, record, etc)
- **Critério de aceite:**
  - ✅ Erros registrados com contexto
  - ✅ Acessível para debugging
  - ✅ Alertas em caso de erro crítico

---

### 2.9 API REST (MUST HAVE)

#### Endpoints de Autenticação
```
POST   /api/auth/login
POST   /api/auth/logout
POST   /api/auth/refresh
GET    /api/auth/me
```

#### Endpoints de Clínicas
```
POST   /api/clinics
GET    /api/clinics
GET    /api/clinics/{id}
PUT    /api/clinics/{id}
GET    /api/clinics/{id}/settings
PUT    /api/clinics/{id}/settings
```

#### Endpoints de Usuários
```
POST   /api/users
GET    /api/users
GET    /api/users/{id}
PUT    /api/users/{id}
DELETE /api/users/{id}
POST   /api/users/{id}/roles
```

#### Endpoints de Uploads
```
POST   /api/uploads
GET    /api/uploads
GET    /api/uploads/{id}
DELETE /api/uploads/{id}
GET    /api/uploads/{id}/status
GET    /api/uploads/{id}/progress
```

#### Endpoints de Records
```
GET    /api/records
GET    /api/records/{id}
PUT    /api/records/{id}
GET    /api/records/search (filtros)
```

#### Endpoints de Validações
```
GET    /api/validations
GET    /api/validations/by-upload/{upload_id}
GET    /api/validations/by-record/{record_id}
```

#### Endpoints de Erros
```
GET    /api/errors
GET    /api/errors/by-upload/{upload_id}
PUT    /api/errors/{id}/acknowledge
PUT    /api/errors/{id}/resolve
```

#### Endpoints de Relatórios
```
POST   /api/reports
GET    /api/reports
GET    /api/reports/{id}
GET    /api/reports/{id}/export/csv
GET    /api/reports/{id}/export/pdf
```

#### Endpoints de Auditoria
```
GET    /api/audit-logs
GET    /api/audit-logs/by-user/{user_id}
GET    /api/audit-logs/by-resource/{resource_type}/{resource_id}
```

---

## 3. FUNCIONALIDADES DO FRONTEND

### 3.1 Autenticação (MUST HAVE)

#### Página: Login
- **Componentes:**
  - ✅ Input email
  - ✅ Input senha
  - ✅ Botão login
  - ✅ Link "Esqueci senha" (nice-to-have)
  - ✅ Mensagens de erro
- **Funcionalidade:**
  - ✅ Validação de email
  - ✅ Validação de senha (mínimo 8 caracteres)
  - ✅ Chamada API de login
  - ✅ Armazenamento de token
  - ✅ Redirecionamento para dashboard
- **Critério de aceite:**
  - ✅ Login bem-sucedido redireciona
  - ✅ Credenciais inválidas mostram erro
  - ✅ Token armazenado corretamente
  - ✅ Logout limpa token

#### Componente: Logout
- **Funcionalidade:**
  - ✅ Botão logout em navbar
  - ✅ Chamada API de logout
  - ✅ Limpeza de token
  - ✅ Redirecionamento para login
- **Critério de aceite:**
  - ✅ Logout funciona corretamente
  - ✅ Token revogado no servidor

---

### 3.2 Dashboard (MUST HAVE)

#### Página: Dashboard Principal
- **Componentes:**
  - ✅ Navbar com menu
  - ✅ Sidebar com navegação
  - ✅ Cards de resumo (total faturado, pendente, erros)
  - ✅ Gráficos de distribuição
  - ✅ Tabela de últimos uploads
  - ✅ Tabela de últimos erros
- **Dados exibidos:**
  - ✅ Total faturado (período)
  - ✅ Total aprovado
  - ✅ Total pendente
  - ✅ Total com erro
  - ✅ Quantidade de procedimentos
  - ✅ Últimos 5 uploads
  - ✅ Últimos 5 erros
- **Filtros:**
  - ✅ Período (data início/fim)
  - ✅ Status
- **Critério de aceite:**
  - ✅ Dados carregam corretamente
  - ✅ Gráficos renderizam
  - ✅ Filtros funcionam
  - ✅ Performance < 2s

#### Componente: Navbar
- **Elementos:**
  - ✅ Logo
  - ✅ Nome da clínica
  - ✅ Usuário logado
  - ✅ Botão logout
  - ✅ Notificações (nice-to-have)
- **Critério de aceite:**
  - ✅ Navbar visível em todas as páginas
  - ✅ Logout funciona

#### Componente: Sidebar
- **Menu:**
  - ✅ Dashboard
  - ✅ Uploads
  - ✅ Registros
  - ✅ Relatórios
  - ✅ Usuários (admin)
  - ✅ Configurações (admin)
- **Funcionalidade:**
  - ✅ Navegação entre páginas
  - ✅ Highlight de página ativa
  - ✅ Collapse/expand em mobile
- **Critério de aceite:**
  - ✅ Menu funciona corretamente
  - ✅ Permissões respeitadas

---

### 3.3 Upload (MUST HAVE)

#### Página: Upload de Arquivo
- **Componentes:**
  - ✅ Drag-and-drop area
  - ✅ Botão "Selecionar arquivo"
  - ✅ Preview do arquivo
  - ✅ Input período de faturamento
  - ✅ Input descrição (opcional)
  - ✅ Botão enviar
  - ✅ Barra de progresso
- **Validações:**
  - ✅ Tipo de arquivo válido
  - ✅ Tamanho máximo
  - ✅ Período válido
- **Critério de aceite:**
  - ✅ Arquivo selecionado corretamente
  - ✅ Upload progride
  - ✅ Sucesso redireciona para detalhes
  - ✅ Erro mostra mensagem

#### Página: Histórico de Uploads
- **Componentes:**
  - ✅ Tabela de uploads
  - ✅ Colunas: Arquivo, Data, Status, Ações
  - ✅ Paginação
  - ✅ Filtros (status, período)
  - ✅ Busca por nome
- **Ações:**
  - ✅ Visualizar detalhes
  - ✅ Deletar upload
  - ✅ Baixar relatório
- **Critério de aceite:**
  - ✅ Tabela carrega corretamente
  - ✅ Paginação funciona
  - ✅ Filtros funcionam
  - ✅ Ações funcionam

#### Página: Detalhes do Upload
- **Componentes:**
  - ✅ Informações do arquivo
  - ✅ Status de processamento
  - ✅ Barra de progresso
  - ✅ Resumo de validações
  - ✅ Tabela de erros
  - ✅ Botões de ação
- **Dados exibidos:**
  - ✅ Nome do arquivo
  - ✅ Data de upload
  - ✅ Tamanho
  - ✅ Status
  - ✅ Total de registros
  - ✅ Registros válidos
  - ✅ Registros com erro
  - ✅ Erros por tipo
- **Critério de aceite:**
  - ✅ Dados carregam corretamente
  - ✅ Status atualiza em tempo real
  - ✅ Erros exibem com detalhes

---

### 3.4 Registros (MUST HAVE)

#### Página: Lista de Registros
- **Componentes:**
  - ✅ Tabela de registros
  - ✅ Colunas: Paciente, Procedimento, Data, Valor, Status
  - ✅ Paginação
  - ✅ Filtros (status, período, procedimento)
  - ✅ Busca por paciente/CPF
  - ✅ Ordenação
- **Ações:**
  - ✅ Visualizar detalhes
  - ✅ Editar status
  - ✅ Ver validações
- **Critério de aceite:**
  - ✅ Tabela carrega corretamente
  - ✅ Paginação funciona
  - ✅ Filtros funcionam
  - ✅ Busca funciona
  - ✅ Performance < 2s

#### Página: Detalhes do Registro
- **Componentes:**
  - ✅ Informações do paciente
  - ✅ Informações do procedimento
  - ✅ Informações financeiras
  - ✅ Status e histórico
  - ✅ Validações aplicadas
  - ✅ Botões de ação
- **Dados exibidos:**
  - ✅ Nome, CPF, ID do paciente
  - ✅ Código, nome, data do procedimento
  - ✅ Valor faturado, pago, pendente
  - ✅ Prestador, convênio
  - ✅ Autorização
  - ✅ Status atual
  - ✅ Histórico de mudanças
  - ✅ Validações e erros
- **Critério de aceite:**
  - ✅ Dados carregam corretamente
  - ✅ Histórico exibido
  - ✅ Validações exibidas

---

### 3.5 Relatórios (MUST HAVE)

#### Página: Lista de Relatórios
- **Componentes:**
  - ✅ Tabela de relatórios
  - ✅ Colunas: Período, Tipo, Data, Ações
  - ✅ Paginação
  - ✅ Filtros (tipo, período)
- **Ações:**
  - ✅ Visualizar relatório
  - ✅ Exportar CSV
  - ✅ Exportar PDF (nice-to-have)
  - ✅ Deletar relatório
- **Critério de aceite:**
  - ✅ Tabela carrega corretamente
  - ✅ Ações funcionam

#### Página: Visualizar Relatório
- **Componentes:**
  - ✅ Resumo executivo
  - ✅ Gráficos de distribuição
  - ✅ Tabelas de dados
  - ✅ Recomendações
  - ✅ Botões de exportação
- **Dados exibidos:**
  - ✅ Período do relatório
  - ✅ Total de registros
  - ✅ Registros válidos
  - ✅ Registros com erro
  - ✅ Total faturado
  - ✅ Distribuição por procedimento
  - ✅ Distribuição por status
  - ✅ Erros mais comuns
- **Critério de aceite:**
  - ✅ Dados carregam corretamente
  - ✅ Gráficos renderizam
  - ✅ Exportação funciona

#### Funcionalidade: Geração de Relatório
- **Fluxo:**
  1. Usuário clica "Gerar Relatório"
  2. Seleciona período
  3. Seleciona tipo (summary, detailed, errors)
  4. Clica "Gerar"
  5. Relatório gerado em background
  6. Notificação quando pronto
  7. Usuário visualiza/exporta
- **Critério de aceite:**
  - ✅ Relatório gerado corretamente
  - ✅ Notificação enviada
  - ✅ Dados precisos

---

### 3.6 Usuários (MUST HAVE - Admin)

#### Página: Gerenciar Usuários
- **Componentes:**
  - ✅ Tabela de usuários
  - ✅ Colunas: Nome, Email, Role, Status, Ações
  - ✅ Paginação
  - ✅ Filtros (role, status)
  - ✅ Busca por nome/email
  - ✅ Botão "Novo usuário"
- **Ações:**
  - ✅ Visualizar detalhes
  - ✅ Editar usuário
  - ✅ Alterar role
  - ✅ Ativar/desativar
  - ✅ Deletar usuário
- **Critério de aceite:**
  - ✅ Tabela carrega corretamente
  - ✅ Ações funcionam
  - ✅ Permissões respeitadas

#### Modal: Criar/Editar Usuário
- **Campos:**
  - ✅ Nome
  - ✅ Email
  - ✅ Role
  - ✅ Status
  - ✅ Senha (criar) / Resetar senha (editar)
- **Validações:**
  - ✅ Email válido
  - ✅ Senha forte (criar)
  - ✅ Campos obrigatórios
- **Critério de aceite:**
  - ✅ Usuário criado/editado corretamente
  - ✅ Email de confirmação enviado (nice-to-have)

---

### 3.7 Configurações (MUST HAVE - Admin)

#### Página: Configurações da Clínica
- **Componentes:**
  - ✅ Formulário de configurações
  - ✅ Seções: Geral, Faturamento, Validação, Retenção
  - ✅ Botão salvar
  - ✅ Mensagem de sucesso/erro
- **Campos:**
  - ✅ Nome da clínica
  - ✅ CNPJ
  - ✅ Email
  - ✅ Telefone
  - ✅ Tipo de faturamento
  - ✅ Moeda padrão
  - ✅ Regras de validação ativas
  - ✅ Dias de retenção
- **Critério de aceite:**
  - ✅ Configurações carregam corretamente
  - ✅ Alterações salvas
  - ✅ Validações aplicadas

---

### 3.8 Componentes Reutilizáveis (MUST HAVE)

#### Componente: Tabela Genérica
- **Funcionalidades:**
  - ✅ Renderização de dados
  - ✅ Paginação
  - ✅ Ordenação
  - ✅ Filtros
  - ✅ Busca
  - ✅ Ações customizáveis
- **Critério de aceite:**
  - ✅ Reutilizável em múltiplas páginas
  - ✅ Performance com 1000+ linhas

#### Componente: Modal Genérico
- **Funcionalidades:**
  - ✅ Abertura/fechamento
  - ✅ Conteúdo customizável
  - ✅ Botões de ação
  - ✅ Validação de formulário
- **Critério de aceite:**
  - ✅ Reutilizável em múltiplas páginas

#### Componente: Notificação
- **Tipos:**
  - ✅ Sucesso
  - ✅ Erro
  - ✅ Aviso
  - ✅ Informação
- **Funcionalidades:**
  - ✅ Auto-dismiss após 5s
  - ✅ Botão fechar
  - ✅ Stack de múltiplas notificações
- **Critério de aceite:**
  - ✅ Notificações exibem corretamente

#### Componente: Gráfico
- **Tipos:**
  - ✅ Gráfico de barras
  - ✅ Gráfico de pizza
  - ✅ Gráfico de linha
- **Funcionalidades:**
  - ✅ Renderização responsiva
  - ✅ Legenda
  - ✅ Tooltip
- **Critério de aceite:**
  - ✅ Gráficos renderizam corretamente

---

### 3.9 Responsividade (MUST HAVE)

#### Suporte a Dispositivos
- ✅ Desktop (1920px+)
- ✅ Tablet (768px - 1024px)
- ✅ Mobile (< 768px)

#### Ajustes por Dispositivo
- ✅ Sidebar colapsável em mobile
- ✅ Tabelas com scroll horizontal em mobile
- ✅ Modais adaptadas para mobile
- ✅ Botões com tamanho adequado

#### Critério de aceite
- ✅ Funciona em Chrome, Firefox, Safari, Edge
- ✅ Sem scroll horizontal em mobile
- ✅ Toque funciona em mobile

---

## 4. MATRIZ DE PRIORIZAÇÃO

### 4.1 MUST HAVE (Crítico para MVP)

**Backend:**
- Autenticação & Autorização
- Isolamento de Tenant
- Upload & Armazenamento
- Parsing & Normalização
- Validações & Regras
- Armazenamento de Dados
- Relatórios & Exportação (CSV)
- Auditoria & Logs
- API REST

**Frontend:**
- Login & Logout
- Dashboard Principal
- Upload de Arquivo
- Histórico de Uploads
- Detalhes do Upload
- Lista de Registros
- Detalhes do Registro
- Lista de Relatórios
- Visualizar Relatório
- Gerenciar Usuários (Admin)
- Configurações (Admin)
- Componentes Reutilizáveis
- Responsividade

### 4.2 NICE TO HAVE (Desejável, não crítico)

**Backend:**
- Parser de XML
- Exportação de Relatório (PDF)
- Notificações por email
- Webhook para integrações
- 2FA (TOTP)

**Frontend:**
- Exportação de Relatório (PDF)
- Notificações em tempo real
- Temas (light/dark)
- Internacionalização (i18n)
- Busca avançada com filtros complexos

### 4.3 FORA DO ESCOPO (Post-MVP)

- Integrações com sistemas clínicos
- IA/Machine Learning
- App mobile
- Telemedicina
- Prontuário eletrônico
- White-label
- Multi-unidades
- Benchmark com outras clínicas
- Consultoria financeira

---

## 5. CRITÉRIOS DE ACEITE GLOBAIS

### 5.1 Funcionalidade
- ✅ Todas as funcionalidades MUST HAVE implementadas
- ✅ Funcionalidades testadas com dados reais
- ✅ Casos de erro tratados
- ✅ Mensagens de erro claras

### 5.2 Performance
- ✅ API responde em < 2s (95º percentil)
- ✅ Dashboard carrega em < 2s
- ✅ Upload processa 1000 registros em < 5s
- ✅ Exportação gera em < 10s

### 5.3 Segurança
- ✅ Isolamento de tenant validado
- ✅ Autenticação funcionando
- ✅ Autorização respeitada
- ✅ Dados sensíveis não expostos
- ✅ HTTPS em produção

### 5.4 Qualidade
- ✅ Testes unitários (80%+ cobertura)
- ✅ Testes de integração (casos críticos)
- ✅ Sem erros em console (frontend)
- ✅ Sem warnings de linting
- ✅ Código documentado

### 5.5 Usabilidade
- ✅ Interface intuitiva
- ✅ Mensagens claras
- ✅ Fluxos lógicos
- ✅ Responsivo em todos os dispositivos
- ✅ Acessibilidade básica (WCAG 2.0 AA)

---

## 6. EXCLUSÕES EXPLÍCITAS

### 6.1 Funcionalidades Fora do Escopo

- ❌ **Prontuário Eletrônico:** Não armazenamos dados clínicos
- ❌ **Telemedicina:** Não temos consultas online
- ❌ **App para Paciente:** Apenas para clínica
- ❌ **Integração com Sistemas Clínicos:** Manual apenas
- ❌ **IA/ML:** Apenas regras determinísticas
- ❌ **Integrações Externas:** Sem APIs de terceiros
- ❌ **White-label:** Branding padrão apenas
- ❌ **Multi-unidades:** Uma clínica por tenant
- ❌ **Consultoria Financeira:** Apenas dados
- ❌ **Benchmark:** Sem comparação com outras clínicas

### 6.2 Tecnologias Fora do Escopo

- ❌ Mobile app (iOS/Android)
- ❌ Desktop app (Electron)
- ❌ GraphQL (REST apenas)
- ❌ WebSocket (polling apenas)
- ❌ Real-time collaboration
- ❌ Offline mode

---

## 7. PRÓXIMOS PASSOS

1. ✅ Análise do projeto
2. ✅ Arquitetura definida
3. ✅ Schema do banco definido
4. ✅ Escopo técnico definido
5. ⏳ **ETAPA 5:** Backlog técnico detalhado
6. ⏳ **ETAPA 6:** Estrutura do repositório
