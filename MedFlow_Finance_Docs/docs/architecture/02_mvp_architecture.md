# ARQUITETURA FINAL DO MVP - MEDFLOW FINANCE

**Data:** Janeiro 2026  
**Status:** Definição completa - Pronto para implementação  
**Versão:** 1.0

---

## 1. VISÃO GERAL DA ARQUITETURA

```
┌─────────────────────────────────────────────────────────────────┐
│                        FRONTEND (Vue 3 SPA)                      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐           │
│  │  Dashboard   │  │   Upload     │  │   Reports    │           │
│  │  Financeiro  │  │   Manager    │  │   Export     │           │
│  └──────────────┘  └──────────────┘  └──────────────┘           │
└─────────────────────────────────────────────────────────────────┘
                            ↓ (Axios)
┌─────────────────────────────────────────────────────────────────┐
│                   API REST (Laravel 11)                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐           │
│  │   Auth       │  │   Uploads    │  │  Validations │           │
│  │  (Sanctum)   │  │   (Storage)  │  │   (Rules)    │           │
│  └──────────────┘  └──────────────┘  └──────────────┘           │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐           │
│  │   Reports    │  │   Audit      │  │   Tenancy    │           │
│  │   (Export)   │  │   (Logs)     │  │   (Middleware)           │
│  └──────────────┘  └──────────────┘  └──────────────┘           │
└─────────────────────────────────────────────────────────────────┘
         ↓ (Jobs)              ↓ (Queries)       ↓ (Storage)
┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐
│  Redis Queue     │  │  PostgreSQL      │  │  S3 / Minio      │
│  (Async Jobs)    │  │  (Multi-tenant)  │  │  (File Storage)  │
└──────────────────┘  └──────────────────┘  └──────────────────┘
```

---

## 2. PADRÃO DE TENANCY

### 2.1 Decisão: Single Database + Tenant ID

**Modelo escolhido:** Single Database com isolamento via `tenant_id`

**Justificativa:**
- ✅ Simples de implementar e manter
- ✅ Reduz complexidade operacional
- ✅ Facilita backups e migrations
- ✅ Suficiente para MVP (clínicas pequenas/médias)
- ✅ Escalável até ~100 clientes sem problemas

**Alternativas rejeitadas:**
- ❌ Multi-database: Complexo demais para MVP, overhead operacional
- ❌ Hybrid: Prematura, adiciona complexidade

### 2.2 Implementação de Tenancy

#### Middleware de Tenant
```
Todas as requisições HTTP passam por middleware que:
1. Extrai tenant_id do token JWT (Sanctum)
2. Valida se usuário pertence ao tenant
3. Injeta tenant_id no contexto da requisição
4. Valida tenant_id em todas as queries (proteção dupla)
```

#### Escopo de Tenant
```
Tabelas com tenant_id (isolamento obrigatório):
- users
- uploads
- records
- validations
- errors
- reports
- audit_logs

Tabelas globais (sem tenant_id):
- clinics (dados da clínica)
- roles (definições de role)
- permissions (definições de permissão)
```

#### Proteção em Camadas
```
Camada 1: Middleware HTTP
  → Valida token e extrai tenant_id

Camada 2: Model Scopes
  → Todos os Models têm scope 'forTenant()'
  → Queries automáticas filtram por tenant_id

Camada 3: Policy (Laravel)
  → Autorização granular por recurso
  → Verifica tenant_id antes de retornar dados

Camada 4: Auditoria
  → Todos os acessos são logados
  → Facilita detecção de vazamentos
```

---

## 3. ESTRATÉGIA DE AUTENTICAÇÃO

### 3.1 Fluxo de Autenticação

```
1. Login (Email + Senha)
   ↓
2. Validar credenciais no banco
   ↓
3. Gerar token Sanctum (API Token)
   ↓
4. Retornar token + dados do usuário
   ↓
5. Frontend armazena token em localStorage/sessionStorage
   ↓
6. Todas as requisições incluem token no header: Authorization: Bearer {token}
   ↓
7. Middleware valida token e injeta usuário no contexto
```

### 3.2 Componentes

#### Autenticação
- **Método:** Email + Senha (hash bcrypt)
- **Token:** Laravel Sanctum (stateless, JWT-like)
- **Expiração:** 24 horas (renovável)
- **Refresh:** Token refresh endpoint
- **Logout:** Revogação de token no banco

#### Segurança
- ✅ HTTPS obrigatório (produção)
- ✅ CORS configurado para domínio específico
- ✅ Rate limiting em endpoints de auth
- ✅ Proteção contra CSRF (SPA, sem cookies)
- ✅ Senhas com hash bcrypt (min 12 caracteres)

#### 2FA (Post-MVP)
- Não incluído no MVP
- Roadmap: TOTP (Google Authenticator)

### 3.3 Endpoints de Autenticação

```
POST /api/auth/login
  Body: { email, password }
  Response: { token, user, clinic }

POST /api/auth/logout
  Headers: { Authorization: Bearer {token} }
  Response: { success: true }

POST /api/auth/refresh
  Headers: { Authorization: Bearer {token} }
  Response: { token }

GET /api/auth/me
  Headers: { Authorization: Bearer {token} }
  Response: { user, clinic, permissions }
```

---

## 4. ARQUITETURA DE PROCESSAMENTO

### 4.1 Fluxo de Upload e Processamento

```
1. Frontend envia arquivo (Excel/CSV)
   ↓
2. Backend valida tipo e tamanho
   ↓
3. Armazena arquivo em S3/Minio
   ↓
4. Cria registro de Upload no banco
   ↓
5. Dispara Job assíncrono (Redis Queue)
   ↓
6. Job processa arquivo:
   a) Parse do arquivo
   b) Normalização de dados
   c) Validações automáticas
   d) Armazenamento de records
   e) Geração de relatório de erros
   ↓
7. Frontend monitora status via polling/WebSocket
   ↓
8. Exibe resultados quando pronto
```

### 4.2 Filas e Jobs

#### Redis Queue
```
Fila: default
Jobs:
  - ProcessUploadJob (prioridade: alta)
  - GenerateReportJob (prioridade: média)
  - ExportDataJob (prioridade: média)
  - CleanupOldFilesJob (prioridade: baixa, agendado)

Configuração:
  - Timeout: 300 segundos (uploads grandes)
  - Retry: 3 tentativas
  - Backoff: exponencial
```

#### Processamento Assíncrono
```
Vantagens:
- Upload retorna imediatamente (melhor UX)
- Processamento não bloqueia API
- Pode processar múltiplos uploads em paralelo
- Facilita retry automático

Monitoramento:
- Status do job em tempo real
- Logs detalhados de cada etapa
- Alertas em caso de falha
```

### 4.3 Armazenamento de Arquivos

#### S3 / Minio
```
Estrutura de pastas:
  /uploads/
    /{clinic_id}/
      /{year}/
        /{month}/
          {upload_id}_{filename}

Políticas:
- Acesso privado (sem URL pública)
- Retenção: conforme política de dados
- Backup: incluído em backup diário
- Criptografia: em repouso (produção)
```

#### Banco de Dados
```
Tabela: uploads
  - id (UUID)
  - clinic_id (FK)
  - user_id (FK)
  - filename
  - file_path (S3)
  - file_size
  - file_type (excel, csv, xml)
  - status (pending, processing, completed, failed)
  - total_records
  - valid_records
  - error_records
  - created_at
  - updated_at
  - deleted_at (soft delete)

Índices:
  - (clinic_id, created_at) DESC
  - (status, created_at)
```

---

## 5. ARQUITETURA DE VALIDAÇÕES

### 5.1 Camadas de Validação

```
Camada 1: Validação de Arquivo
  - Tipo de arquivo (Excel, CSV, XML)
  - Tamanho máximo (100MB)
  - Estrutura básica (headers, colunas)

Camada 2: Validação de Dados
  - Campos obrigatórios
  - Tipos de dados (string, number, date)
  - Ranges e formatos
  - Referências (IDs válidos)

Camada 3: Validação de Negócio
  - Regras de faturamento (TUSS, CFM)
  - Glosas conhecidas
  - Inconsistências lógicas
  - Duplicatas

Camada 4: Validação de Compliance
  - LGPD (dados sensíveis)
  - Retenção de dados
  - Auditoria
```

### 5.2 Motor de Regras

#### Arquitetura
```
RulesEngine
  ├── Rule (abstrata)
  │   ├── FieldValidationRule
  │   ├── BusinessLogicRule
  │   ├── ComplianceRule
  │   └── GlosaDetectionRule
  │
  ├── RuleSet (coleção de rules)
  │   ├── FileFormatRuleSet
  │   ├── DataValidationRuleSet
  │   └── BillingRuleSet
  │
  └── Executor
      ├── Executa rules em sequência
      ├── Coleta erros
      └── Retorna relatório
```

#### Exemplo de Rule
```php
class ValidateProcedureCodeRule extends Rule {
    public function validate($record): ValidationResult {
        // Verifica se código de procedimento é válido
        // Consulta tabela de códigos TUSS
        // Retorna erro se inválido
    }
}
```

### 5.3 Relatório de Erros

```
Estrutura:
{
  "upload_id": "uuid",
  "total_records": 1000,
  "valid_records": 950,
  "error_records": 50,
  "errors": [
    {
      "row": 5,
      "field": "procedure_code",
      "value": "INVALID",
      "rule": "ValidateProcedureCodeRule",
      "message": "Código de procedimento inválido",
      "severity": "error"
    },
    ...
  ],
  "warnings": [
    {
      "row": 10,
      "field": "amount",
      "value": 50000,
      "rule": "UnusualAmountWarning",
      "message": "Valor acima da média para este procedimento",
      "severity": "warning"
    },
    ...
  ]
}
```

---

## 6. SEPARAÇÃO DE DOMÍNIOS

### 6.1 Domínios Identificados

#### Domínio: Autenticação & Autorização
```
Responsabilidades:
- Login/Logout
- Geração de tokens
- Validação de permissões
- RBAC

Componentes:
- AuthController
- AuthService
- Policy (Laravel)
- Middleware
```

#### Domínio: Upload & Armazenamento
```
Responsabilidades:
- Receber arquivo
- Validar tipo/tamanho
- Armazenar em S3/Minio
- Rastrear status

Componentes:
- UploadController
- UploadService
- StorageService
- UploadJob
```

#### Domínio: Processamento & Parsing
```
Responsabilidades:
- Parse de Excel/CSV/XML
- Normalização de dados
- Transformação de formatos

Componentes:
- FileParserService
- ExcelParser
- CSVParser
- XMLParser (roadmap)
- DataNormalizer
```

#### Domínio: Validações & Regras
```
Responsabilidades:
- Executar validações
- Aplicar regras de negócio
- Detectar glosas
- Gerar relatórios de erro

Componentes:
- RulesEngine
- ValidationService
- Rule (abstrata)
- RuleSet
- ErrorReporter
```

#### Domínio: Relatórios & Exportação
```
Responsabilidades:
- Gerar relatórios
- Exportar dados (CSV, PDF)
- Agregações financeiras
- Dashboard

Componentes:
- ReportService
- ExportService
- DashboardService
- ReportGenerator
```

#### Domínio: Auditoria & Logs
```
Responsabilidades:
- Registrar todas as ações
- Rastrear mudanças
- Logs de segurança
- Compliance

Componentes:
- AuditLogger
- AuditLog (Model)
- AuditObserver
```

### 6.2 Fluxo Entre Domínios

```
Upload (Domínio: Upload)
  ↓
Parsing (Domínio: Processamento)
  ↓
Validação (Domínio: Validações)
  ↓
Armazenamento (Domínio: Processamento)
  ↓
Relatório (Domínio: Relatórios)
  ↓
Auditoria (Domínio: Auditoria)
```

---

## 7. SEGURANÇA

### 7.1 Checklist de Segurança

- ✅ **Autenticação:** Sanctum + Email/Senha
- ✅ **Autorização:** RBAC + Policies
- ✅ **Isolamento de Tenant:** Middleware + Scopes
- ✅ **Proteção de Dados:** Soft deletes + Auditoria
- ✅ **Validação de Input:** Sanitização + Validação
- ✅ **HTTPS:** Obrigatório em produção
- ✅ **CORS:** Configurado para domínio específico
- ✅ **Rate Limiting:** Em endpoints críticos
- ✅ **Logs de Segurança:** Todas as ações auditadas
- ✅ **Backup:** Diário com retenção

### 7.2 Proteção Contra Ataques Comuns

| Ataque | Proteção |
|--------|----------|
| SQL Injection | Eloquent ORM + Prepared Statements |
| XSS | Vue 3 auto-escape + CSP headers |
| CSRF | SPA sem cookies, token em header |
| Brute Force | Rate limiting + Account lockout |
| Privilege Escalation | RBAC + Policies + Auditoria |
| Data Leakage | Tenant isolation + Soft deletes |
| Man-in-the-Middle | HTTPS + HSTS headers |

---

## 8. ESCALABILIDADE

### 8.1 Estratégia de Escala

#### Fase MVP (0-100 clientes)
```
- Single VPS (4 CPU, 8GB RAM)
- PostgreSQL em mesmo servidor
- Redis em mesmo servidor
- S3/Minio local
- Sem cache distribuído
```

#### Fase Pós-MVP (100-500 clientes)
```
- Load balancer (2 servidores API)
- PostgreSQL separado (replicação)
- Redis separado
- S3 AWS
- Cache distribuído (Redis)
```

#### Fase Crescimento (500+ clientes)
```
- Kubernetes (auto-scaling)
- PostgreSQL managed (RDS)
- Redis managed (ElastiCache)
- S3 AWS
- CDN para assets
- Monitoring + Alerting
```

### 8.2 Otimizações Iniciais

```
- Índices no banco (tenant_id, created_at)
- Paginação em todas as listas
- Lazy loading no frontend
- Compressão de assets
- Cache de dados estáticos
- Processamento assíncrono de uploads
```

---

## 9. MONITORAMENTO & OBSERVABILIDADE

### 9.1 Logs

```
Níveis:
- ERROR: Falhas críticas (alertar)
- WARNING: Situações anormais (monitorar)
- INFO: Eventos importantes (auditoria)
- DEBUG: Detalhes técnicos (desenvolvimento)

Canais:
- single: Arquivo local
- stack: Arquivo + Sentry (produção)
- syslog: Syslog (produção)
```

### 9.2 Métricas

```
Aplicação:
- Tempo de resposta por endpoint
- Taxa de erro
- Uploads processados
- Validações executadas

Infraestrutura:
- CPU, Memória, Disco
- Conexões de banco
- Fila de jobs
- Espaço em S3
```

### 9.3 Alertas

```
Críticos:
- Taxa de erro > 5%
- Tempo de resposta > 5s
- Fila de jobs > 1000
- Espaço em disco < 10%

Avisos:
- Taxa de erro > 1%
- Tempo de resposta > 2s
- Fila de jobs > 500
```

---

## 10. DEPLOYMENT

### 10.1 Ambientes

```
Desenvolvimento:
- Máquina local
- SQLite ou PostgreSQL local
- Redis local
- Minio local

Staging:
- VPS de teste
- PostgreSQL
- Redis
- Minio
- Dados de teste

Produção:
- VPS principal
- PostgreSQL
- Redis
- AWS S3
- Backup diário
```

### 10.2 CI/CD Pipeline

```
1. Push para main
   ↓
2. Testes (Unit + Integration)
   ↓
3. Análise de código (SonarQube)
   ↓
4. Build (Docker)
   ↓
5. Deploy em Staging
   ↓
6. Testes E2E
   ↓
7. Aprovação manual
   ↓
8. Deploy em Produção
```

---

## 11. DECISÕES ARQUITETURAIS DOCUMENTADAS

| Decisão | Escolha | Justificativa | Alternativas |
|---------|---------|---------------|--------------|
| Tenancy | Single DB + tenant_id | Simplicidade MVP | Multi-DB (complexo) |
| Auth | Sanctum | Stateless, simples | OAuth2 (overkill) |
| Fila | Redis | Integrado Laravel | Database (lento) |
| Storage | S3/Minio | Escalável | Filesystem (limitado) |
| Banco | PostgreSQL | JSON, compliance | MySQL (menos features) |
| Frontend | Vue 3 SPA | Moderno, reativo | Server-side (monolítico) |
| Validações | Rule Engine | Extensível | Hardcoded (inflexível) |

---

## 12. PRÓXIMOS PASSOS

1. ✅ Arquitetura definida
2. ⏳ **ETAPA 3:** Modelagem de dados real (banco)
3. ⏳ **ETAPA 4:** Escopo técnico do MVP
4. ⏳ **ETAPA 5:** Backlog técnico detalhado
5. ⏳ **ETAPA 6:** Estrutura do repositório

---

## 13. APÊNDICE: SUPOSIÇÕES CONFIRMADAS

- ✅ Tenancy: Single Database + tenant_id
- ✅ Autenticação: Sanctum + Email/Senha
- ✅ Fila: Redis com Laravel Queue
- ✅ Storage: S3/Minio
- ✅ Banco: PostgreSQL
- ✅ Soft deletes: Sim, em tabelas críticas
- ✅ Auditoria: Tabela audit_logs com todas as mudanças
