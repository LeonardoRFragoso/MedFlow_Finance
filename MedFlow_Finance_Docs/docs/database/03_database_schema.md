# MODELAGEM DE DADOS - SCHEMA DO BANCO

**Data:** Janeiro 2026  
**Status:** Definição completa - Pronto para migrations  
**Versão:** 1.0  
**Banco:** PostgreSQL 14+

---

## 1. VISÃO GERAL DO SCHEMA

```
Tabelas Globais (sem tenant_id):
├── clinics
├── roles
├── permissions
└── role_permissions

Tabelas de Usuários:
├── users
├── user_roles
└── user_permissions

Tabelas de Upload & Processamento:
├── uploads
├── records
├── validations
└── errors

Tabelas de Relatórios:
├── reports
└── report_exports

Tabelas de Auditoria:
└── audit_logs

Tabelas de Configuração:
└── clinic_settings
```

---

## 2. TABELAS GLOBAIS

### 2.1 Tabela: `clinics`

Armazena dados das clínicas (tenants).

```sql
CREATE TABLE clinics (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(255) NOT NULL,
    cnpj VARCHAR(18) UNIQUE NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(2),
    zip_code VARCHAR(10),
    
    -- Dados de faturamento
    billing_type ENUM('private', 'public', 'mixed') DEFAULT 'private',
    default_currency VARCHAR(3) DEFAULT 'BRL',
    
    -- Status
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    
    -- Plano
    plan_type VARCHAR(50) DEFAULT 'basic', -- basic, professional, enterprise
    plan_started_at TIMESTAMP,
    plan_expires_at TIMESTAMP,
    
    -- Limites
    max_users INTEGER DEFAULT 5,
    max_monthly_uploads INTEGER DEFAULT 100,
    max_file_size_mb INTEGER DEFAULT 100,
    
    -- Metadata
    logo_url VARCHAR(500),
    custom_domain VARCHAR(255),
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    CONSTRAINT cnpj_format CHECK (cnpj ~ '^\d{2}\.\d{3}\.\d{3}/\d{4}-\d{2}$')
);

CREATE INDEX idx_clinics_status ON clinics(status);
CREATE INDEX idx_clinics_cnpj ON clinics(cnpj);
CREATE INDEX idx_clinics_created_at ON clinics(created_at DESC);
```

### 2.2 Tabela: `roles`

Define roles disponíveis no sistema.

```sql
CREATE TABLE roles (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    
    -- Tipos de role
    role_type ENUM('system', 'custom') DEFAULT 'system',
    
    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO roles (name, description, role_type) VALUES
    ('admin', 'Administrador da clínica', 'system'),
    ('financial_manager', 'Gestor financeiro', 'system'),
    ('administrative', 'Administrativo', 'system'),
    ('viewer', 'Visualizador (read-only)', 'system');
```

### 2.3 Tabela: `permissions`

Define permissões granulares.

```sql
CREATE TABLE permissions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    resource VARCHAR(50) NOT NULL, -- 'uploads', 'reports', 'users', etc
    action VARCHAR(50) NOT NULL,   -- 'create', 'read', 'update', 'delete'
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(resource, action)
);

-- Inserções de permissões padrão (veja seção 3.1)
```

### 2.4 Tabela: `role_permissions`

Relacionamento entre roles e permissions.

```sql
CREATE TABLE role_permissions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    role_id UUID NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    permission_id UUID NOT NULL REFERENCES permissions(id) ON DELETE CASCADE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(role_id, permission_id)
);

CREATE INDEX idx_role_permissions_role_id ON role_permissions(role_id);
```

---

## 3. TABELAS DE USUÁRIOS

### 3.1 Tabela: `users`

Usuários do sistema (com tenant_id para isolamento).

```sql
CREATE TABLE users (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    clinic_id UUID NOT NULL REFERENCES clinics(id) ON DELETE CASCADE,
    
    -- Dados pessoais
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL, -- bcrypt hash
    
    -- Perfil
    phone VARCHAR(20),
    avatar_url VARCHAR(500),
    
    -- Status
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login_at TIMESTAMP NULL,
    
    -- 2FA (Post-MVP)
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    two_factor_secret VARCHAR(255) NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    CONSTRAINT email_format CHECK (email ~ '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}$')
);

CREATE INDEX idx_users_clinic_id ON users(clinic_id);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_clinic_email ON users(clinic_id, email);
CREATE INDEX idx_users_created_at ON users(clinic_id, created_at DESC);
```

### 3.2 Tabela: `user_roles`

Relacionamento entre usuários e roles (por clínica).

```sql
CREATE TABLE user_roles (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    role_id UUID NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    clinic_id UUID NOT NULL REFERENCES clinics(id) ON DELETE CASCADE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(user_id, role_id, clinic_id)
);

CREATE INDEX idx_user_roles_user_id ON user_roles(user_id);
CREATE INDEX idx_user_roles_clinic_id ON user_roles(clinic_id);
```

### 3.3 Tabela: `user_permissions`

Permissões customizadas por usuário (override).

```sql
CREATE TABLE user_permissions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    permission_id UUID NOT NULL REFERENCES permissions(id) ON DELETE CASCADE,
    clinic_id UUID NOT NULL REFERENCES clinics(id) ON DELETE CASCADE,
    
    -- Tipo de override
    grant_type ENUM('grant', 'deny') DEFAULT 'grant',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE(user_id, permission_id, clinic_id)
);

CREATE INDEX idx_user_permissions_user_id ON user_permissions(user_id);
```

---

## 4. TABELAS DE UPLOAD & PROCESSAMENTO

### 4.1 Tabela: `uploads`

Rastreia uploads de arquivos.

```sql
CREATE TABLE uploads (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    clinic_id UUID NOT NULL REFERENCES clinics(id) ON DELETE CASCADE,
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE SET NULL,
    
    -- Informações do arquivo
    original_filename VARCHAR(500) NOT NULL,
    file_path VARCHAR(1000) NOT NULL, -- S3/Minio path
    file_size_bytes BIGINT NOT NULL,
    file_type ENUM('excel', 'csv', 'xml') NOT NULL,
    file_hash VARCHAR(64), -- SHA256 para deduplicação
    
    -- Processamento
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    processing_started_at TIMESTAMP NULL,
    processing_completed_at TIMESTAMP NULL,
    processing_error_message TEXT NULL,
    
    -- Estatísticas
    total_rows INTEGER DEFAULT 0,
    valid_rows INTEGER DEFAULT 0,
    error_rows INTEGER DEFAULT 0,
    warning_rows INTEGER DEFAULT 0,
    
    -- Período de faturamento
    billing_period_start DATE,
    billing_period_end DATE,
    
    -- Metadata
    description TEXT,
    tags JSONB DEFAULT '[]'::jsonb,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    CONSTRAINT file_size_limit CHECK (file_size_bytes <= 104857600) -- 100MB
);

CREATE INDEX idx_uploads_clinic_id ON uploads(clinic_id);
CREATE INDEX idx_uploads_status ON uploads(status);
CREATE INDEX idx_uploads_clinic_status ON uploads(clinic_id, status);
CREATE INDEX idx_uploads_created_at ON uploads(clinic_id, created_at DESC);
CREATE INDEX idx_uploads_file_hash ON uploads(clinic_id, file_hash);
```

### 4.2 Tabela: `records`

Registros normalizados de faturamento (após parse).

```sql
CREATE TABLE records (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    clinic_id UUID NOT NULL REFERENCES clinics(id) ON DELETE CASCADE,
    upload_id UUID NOT NULL REFERENCES uploads(id) ON DELETE CASCADE,
    
    -- Dados do paciente
    patient_name VARCHAR(255),
    patient_cpf VARCHAR(14), -- XXX.XXX.XXX-XX
    patient_id VARCHAR(100), -- ID interno da clínica
    
    -- Dados do procedimento
    procedure_code VARCHAR(20) NOT NULL, -- Código TUSS
    procedure_name VARCHAR(255),
    procedure_date DATE NOT NULL,
    
    -- Dados financeiros
    amount_billed DECIMAL(12, 2) NOT NULL,
    amount_paid DECIMAL(12, 2) DEFAULT 0,
    amount_pending DECIMAL(12, 2) DEFAULT 0,
    
    -- Status
    status ENUM('pending', 'approved', 'rejected', 'disputed') DEFAULT 'pending',
    
    -- Dados adicionais
    provider_name VARCHAR(255),
    provider_id VARCHAR(100),
    insurance_name VARCHAR(255),
    insurance_id VARCHAR(100),
    authorization_number VARCHAR(50),
    
    -- Dados brutos (para auditoria)
    raw_data JSONB NOT NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE INDEX idx_records_clinic_id ON records(clinic_id);
CREATE INDEX idx_records_upload_id ON records(upload_id);
CREATE INDEX idx_records_procedure_code ON records(clinic_id, procedure_code);
CREATE INDEX idx_records_patient_cpf ON records(clinic_id, patient_cpf);
CREATE INDEX idx_records_procedure_date ON records(clinic_id, procedure_date DESC);
CREATE INDEX idx_records_status ON records(clinic_id, status);
CREATE INDEX idx_records_created_at ON records(clinic_id, created_at DESC);
```

### 4.3 Tabela: `validations`

Resultado das validações executadas.

```sql
CREATE TABLE validations (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    clinic_id UUID NOT NULL REFERENCES clinics(id) ON DELETE CASCADE,
    record_id UUID NOT NULL REFERENCES records(id) ON DELETE CASCADE,
    upload_id UUID NOT NULL REFERENCES uploads(id) ON DELETE CASCADE,
    
    -- Regra aplicada
    rule_name VARCHAR(255) NOT NULL,
    rule_type ENUM('field', 'business', 'compliance', 'glosa') NOT NULL,
    
    -- Resultado
    is_valid BOOLEAN NOT NULL,
    severity ENUM('error', 'warning', 'info') DEFAULT 'error',
    
    -- Detalhes
    field_name VARCHAR(100),
    expected_value TEXT,
    actual_value TEXT,
    message TEXT NOT NULL,
    
    -- Metadata
    rule_config JSONB, -- Configuração da regra aplicada
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_validations_clinic_id ON validations(clinic_id);
CREATE INDEX idx_validations_record_id ON validations(record_id);
CREATE INDEX idx_validations_upload_id ON validations(upload_id);
CREATE INDEX idx_validations_is_valid ON validations(clinic_id, is_valid);
CREATE INDEX idx_validations_severity ON validations(clinic_id, severity);
```

### 4.4 Tabela: `errors`

Erros encontrados durante processamento.

```sql
CREATE TABLE errors (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    clinic_id UUID NOT NULL REFERENCES clinics(id) ON DELETE CASCADE,
    upload_id UUID NOT NULL REFERENCES uploads(id) ON DELETE CASCADE,
    record_id UUID REFERENCES records(id) ON DELETE SET NULL,
    
    -- Erro
    error_type ENUM('parse', 'validation', 'processing', 'system') NOT NULL,
    error_code VARCHAR(50),
    error_message TEXT NOT NULL,
    
    -- Contexto
    row_number INTEGER,
    field_name VARCHAR(100),
    raw_value TEXT,
    
    -- Stack trace (para debugging)
    stack_trace TEXT,
    
    -- Status
    status ENUM('new', 'acknowledged', 'resolved') DEFAULT 'new',
    resolved_at TIMESTAMP NULL,
    resolved_by UUID REFERENCES users(id) ON DELETE SET NULL,
    resolution_notes TEXT,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_errors_clinic_id ON errors(clinic_id);
CREATE INDEX idx_errors_upload_id ON errors(upload_id);
CREATE INDEX idx_errors_status ON errors(clinic_id, status);
CREATE INDEX idx_errors_error_type ON errors(clinic_id, error_type);
CREATE INDEX idx_errors_created_at ON errors(clinic_id, created_at DESC);
```

---

## 5. TABELAS DE RELATÓRIOS

### 5.1 Tabela: `reports`

Relatórios gerados.

```sql
CREATE TABLE reports (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    clinic_id UUID NOT NULL REFERENCES clinics(id) ON DELETE CASCADE,
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE SET NULL,
    
    -- Tipo de relatório
    report_type ENUM('summary', 'detailed', 'errors', 'validation', 'financial') NOT NULL,
    
    -- Período
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    
    -- Dados
    total_records INTEGER DEFAULT 0,
    total_valid INTEGER DEFAULT 0,
    total_errors INTEGER DEFAULT 0,
    total_warnings INTEGER DEFAULT 0,
    total_amount DECIMAL(15, 2) DEFAULT 0,
    
    -- Conteúdo
    content JSONB NOT NULL, -- Dados do relatório em JSON
    
    -- Geração
    generated_at TIMESTAMP NOT NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE INDEX idx_reports_clinic_id ON reports(clinic_id);
CREATE INDEX idx_reports_report_type ON reports(clinic_id, report_type);
CREATE INDEX idx_reports_period ON reports(clinic_id, period_start, period_end);
CREATE INDEX idx_reports_created_at ON reports(clinic_id, created_at DESC);
```

### 5.2 Tabela: `report_exports`

Rastreia exportações de relatórios.

```sql
CREATE TABLE report_exports (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    clinic_id UUID NOT NULL REFERENCES clinics(id) ON DELETE CASCADE,
    report_id UUID NOT NULL REFERENCES reports(id) ON DELETE CASCADE,
    user_id UUID NOT NULL REFERENCES users(id) ON DELETE SET NULL,
    
    -- Formato
    export_format ENUM('csv', 'pdf', 'xlsx', 'json') NOT NULL,
    
    -- Arquivo
    file_path VARCHAR(1000) NOT NULL, -- S3/Minio path
    file_size_bytes BIGINT,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    downloaded_at TIMESTAMP NULL,
    expires_at TIMESTAMP NOT NULL -- Link expira em 7 dias
);

CREATE INDEX idx_report_exports_clinic_id ON report_exports(clinic_id);
CREATE INDEX idx_report_exports_report_id ON report_exports(report_id);
CREATE INDEX idx_report_exports_expires_at ON report_exports(expires_at);
```

---

## 6. TABELAS DE AUDITORIA

### 6.1 Tabela: `audit_logs`

Log de todas as ações (compliance + debugging).

```sql
CREATE TABLE audit_logs (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    clinic_id UUID NOT NULL REFERENCES clinics(id) ON DELETE CASCADE,
    user_id UUID REFERENCES users(id) ON DELETE SET NULL,
    
    -- Ação
    action VARCHAR(100) NOT NULL, -- 'create', 'update', 'delete', 'login', etc
    resource_type VARCHAR(100) NOT NULL, -- 'upload', 'record', 'user', etc
    resource_id UUID,
    
    -- Detalhes
    description TEXT,
    old_values JSONB, -- Valores anteriores (para updates)
    new_values JSONB, -- Valores novos
    
    -- Contexto
    ip_address INET,
    user_agent TEXT,
    http_method VARCHAR(10),
    http_path VARCHAR(500),
    http_status_code INTEGER,
    
    -- Resultado
    status ENUM('success', 'failure') DEFAULT 'success',
    error_message TEXT,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_audit_logs_clinic_id ON audit_logs(clinic_id);
CREATE INDEX idx_audit_logs_user_id ON audit_logs(user_id);
CREATE INDEX idx_audit_logs_resource ON audit_logs(clinic_id, resource_type, resource_id);
CREATE INDEX idx_audit_logs_action ON audit_logs(clinic_id, action);
CREATE INDEX idx_audit_logs_created_at ON audit_logs(clinic_id, created_at DESC);
```

---

## 7. TABELAS DE CONFIGURAÇÃO

### 7.1 Tabela: `clinic_settings`

Configurações por clínica.

```sql
CREATE TABLE clinic_settings (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    clinic_id UUID NOT NULL UNIQUE REFERENCES clinics(id) ON DELETE CASCADE,
    
    -- Configurações de faturamento
    default_billing_type VARCHAR(50) DEFAULT 'private',
    currency VARCHAR(3) DEFAULT 'BRL',
    
    -- Configurações de validação
    enable_glosa_detection BOOLEAN DEFAULT TRUE,
    enable_compliance_check BOOLEAN DEFAULT TRUE,
    validation_rules JSONB DEFAULT '{}'::jsonb,
    
    -- Configurações de retenção
    data_retention_days INTEGER DEFAULT 2555, -- 7 anos
    auto_delete_old_files BOOLEAN DEFAULT FALSE,
    
    -- Configurações de notificação
    notify_on_upload_complete BOOLEAN DEFAULT TRUE,
    notify_on_error BOOLEAN DEFAULT TRUE,
    notification_email VARCHAR(255),
    
    -- Configurações de integração
    webhook_url VARCHAR(500),
    webhook_secret VARCHAR(255),
    
    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_clinic_settings_clinic_id ON clinic_settings(clinic_id);
```

---

## 8. PERMISSÕES PADRÃO

Inserções de permissões padrão:

```sql
INSERT INTO permissions (name, description, resource, action) VALUES
-- Upload
('upload.create', 'Criar upload', 'uploads', 'create'),
('upload.read', 'Visualizar uploads', 'uploads', 'read'),
('upload.delete', 'Deletar uploads', 'uploads', 'delete'),

-- Records
('record.read', 'Visualizar registros', 'records', 'read'),
('record.update', 'Atualizar registros', 'records', 'update'),

-- Reports
('report.create', 'Criar relatórios', 'reports', 'create'),
('report.read', 'Visualizar relatórios', 'reports', 'read'),
('report.export', 'Exportar relatórios', 'reports', 'export'),

-- Users
('user.create', 'Criar usuários', 'users', 'create'),
('user.read', 'Visualizar usuários', 'users', 'read'),
('user.update', 'Atualizar usuários', 'users', 'update'),
('user.delete', 'Deletar usuários', 'users', 'delete'),

-- Settings
('settings.read', 'Visualizar configurações', 'settings', 'read'),
('settings.update', 'Atualizar configurações', 'settings', 'update'),

-- Audit
('audit.read', 'Visualizar logs de auditoria', 'audit', 'read');
```

---

## 9. ATRIBUIÇÕES DE ROLES

Atribuições de permissões a roles:

```sql
-- Admin: Todas as permissões
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'admin';

-- Financial Manager: Upload, Records, Reports
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'financial_manager'
AND p.resource IN ('uploads', 'records', 'reports')
AND p.action IN ('create', 'read', 'update', 'export');

-- Administrative: Upload, Records (read-only)
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'administrative'
AND p.resource IN ('uploads', 'records')
AND p.action IN ('create', 'read');

-- Viewer: Read-only
INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id FROM roles r, permissions p
WHERE r.name = 'viewer'
AND p.action = 'read';
```

---

## 10. ÍNDICES ESTRATÉGICOS

### 10.1 Índices Críticos (Performance)

```sql
-- Isolamento de tenant (CRÍTICO)
CREATE INDEX idx_users_clinic_id ON users(clinic_id);
CREATE INDEX idx_uploads_clinic_id ON uploads(clinic_id);
CREATE INDEX idx_records_clinic_id ON records(clinic_id);
CREATE INDEX idx_validations_clinic_id ON validations(clinic_id);
CREATE INDEX idx_errors_clinic_id ON errors(clinic_id);
CREATE INDEX idx_audit_logs_clinic_id ON audit_logs(clinic_id);

-- Filtros comuns
CREATE INDEX idx_uploads_status ON uploads(status);
CREATE INDEX idx_records_status ON records(status);
CREATE INDEX idx_errors_status ON errors(status);

-- Ordenação por data
CREATE INDEX idx_uploads_created_at ON uploads(clinic_id, created_at DESC);
CREATE INDEX idx_records_created_at ON records(clinic_id, created_at DESC);
CREATE INDEX idx_audit_logs_created_at ON audit_logs(clinic_id, created_at DESC);

-- Busca por período
CREATE INDEX idx_records_procedure_date ON records(clinic_id, procedure_date DESC);
CREATE INDEX idx_reports_period ON reports(clinic_id, period_start, period_end);
```

### 10.2 Índices de Busca

```sql
-- Busca por CPF
CREATE INDEX idx_records_patient_cpf ON records(clinic_id, patient_cpf);

-- Busca por código de procedimento
CREATE INDEX idx_records_procedure_code ON records(clinic_id, procedure_code);

-- Busca por email
CREATE INDEX idx_users_email ON users(email);
```

---

## 11. SOFT DELETES

Todas as tabelas com dados críticos incluem `deleted_at`:

```
Tabelas com soft delete:
- clinics
- users
- uploads
- records
- errors
- reports
- report_exports
- clinic_settings

Tabelas sem soft delete (apenas logs):
- audit_logs (imutável)
- validations (imutável)
- user_roles (referencial)
- role_permissions (referencial)
```

**Política:** Queries padrão excluem registros deletados via scope global.

---

## 12. CONSTRAINTS E VALIDAÇÕES

### 12.1 Constraints de Negócio

```sql
-- Clínica não pode ter mais usuários que o plano permite
ALTER TABLE users ADD CONSTRAINT check_user_limit
CHECK (clinic_id IN (
    SELECT id FROM clinics WHERE (
        SELECT COUNT(*) FROM users WHERE clinic_id = clinics.id AND deleted_at IS NULL
    ) <= max_users
));

-- Record não pode ter amount_paid > amount_billed
ALTER TABLE records ADD CONSTRAINT check_amount_paid
CHECK (amount_paid <= amount_billed);

-- Upload não pode ter error_rows > total_rows
ALTER TABLE uploads ADD CONSTRAINT check_error_rows
CHECK (error_rows <= total_rows);
```

### 12.2 Validações de Formato

```sql
-- CPF válido (formato)
ALTER TABLE records ADD CONSTRAINT check_cpf_format
CHECK (patient_cpf IS NULL OR patient_cpf ~ '^\d{3}\.\d{3}\.\d{3}-\d{2}$');

-- CNPJ válido (formato)
ALTER TABLE clinics ADD CONSTRAINT check_cnpj_format
CHECK (cnpj ~ '^\d{2}\.\d{3}\.\d{3}/\d{4}-\d{2}$');

-- Email válido
ALTER TABLE users ADD CONSTRAINT check_email_format
CHECK (email ~ '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,}$');
```

---

## 13. PARTICIONAMENTO (Futuro)

Para escalabilidade futura, considerar particionamento:

```sql
-- Particionar records por clinic_id (quando > 100M registros)
CREATE TABLE records_partitioned (
    ...
) PARTITION BY HASH (clinic_id);

-- Particionar audit_logs por data (quando > 1B registros)
CREATE TABLE audit_logs_partitioned (
    ...
) PARTITION BY RANGE (created_at);
```

---

## 14. VIEWS ÚTEIS

### 14.1 View: Dashboard Summary

```sql
CREATE VIEW v_dashboard_summary AS
SELECT
    c.id as clinic_id,
    c.name as clinic_name,
    COUNT(DISTINCT u.id) as total_users,
    COUNT(DISTINCT up.id) as total_uploads,
    COUNT(DISTINCT r.id) as total_records,
    SUM(CASE WHEN r.status = 'approved' THEN r.amount_billed ELSE 0 END) as total_approved,
    SUM(CASE WHEN r.status = 'pending' THEN r.amount_billed ELSE 0 END) as total_pending,
    COUNT(DISTINCT e.id) as total_errors
FROM clinics c
LEFT JOIN users u ON c.id = u.clinic_id AND u.deleted_at IS NULL
LEFT JOIN uploads up ON c.id = up.clinic_id AND up.deleted_at IS NULL
LEFT JOIN records r ON c.id = r.clinic_id AND r.deleted_at IS NULL
LEFT JOIN errors e ON c.id = e.clinic_id
WHERE c.deleted_at IS NULL
GROUP BY c.id, c.name;
```

### 14.2 View: User Permissions

```sql
CREATE VIEW v_user_permissions AS
SELECT
    u.id as user_id,
    u.clinic_id,
    p.name as permission_name,
    p.resource,
    p.action
FROM users u
LEFT JOIN user_roles ur ON u.id = ur.user_id
LEFT JOIN role_permissions rp ON ur.role_id = rp.role_id
LEFT JOIN permissions p ON rp.permission_id = p.id
WHERE u.deleted_at IS NULL
UNION
SELECT
    u.id as user_id,
    u.clinic_id,
    p.name as permission_name,
    p.resource,
    p.action
FROM users u
LEFT JOIN user_permissions up ON u.id = up.user_id
LEFT JOIN permissions p ON up.permission_id = p.id
WHERE u.deleted_at IS NULL;
```

---

## 15. MIGRAÇÃO INICIAL

Ordem de execução das migrations:

```
1. Create clinics
2. Create roles
3. Create permissions
4. Create role_permissions
5. Create users
6. Create user_roles
7. Create user_permissions
8. Create uploads
9. Create records
10. Create validations
11. Create errors
12. Create reports
13. Create report_exports
14. Create audit_logs
15. Create clinic_settings
16. Create indexes
17. Create views
18. Seed roles and permissions
```

---

## 16. PRÓXIMOS PASSOS

1. ✅ Schema definido
2. ✅ Índices estratégicos
3. ✅ Constraints e validações
4. ⏳ **ETAPA 4:** Escopo técnico do MVP
5. ⏳ **ETAPA 5:** Backlog técnico detalhado
6. ⏳ **ETAPA 6:** Estrutura do repositório
