# MedFlow Finance - Backend

SaaS B2B de Automação e Faturamento Médico - Backend Laravel 11

## Setup Inicial

### Pré-requisitos
- PHP 8.2+
- PostgreSQL 14+
- Redis 7+
- Composer

### Instalação

1. **Clonar o repositório**
```bash
git clone <repository-url>
cd MedFlow_Finance_Backend
```

2. **Instalar dependências**
```bash
composer install
```

3. **Configurar ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configurar banco de dados**
Editar `.env` com credenciais do PostgreSQL:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=medflow_finance
DB_USERNAME=postgres
DB_PASSWORD=secret
```

5. **Executar migrations**
```bash
php artisan migrate --seed
```

6. **Iniciar servidor**
```bash
php artisan serve
```

O servidor estará disponível em `http://localhost:8000`

## Estrutura do Projeto

```
app/
├── Models/                 # Modelos Eloquent
├── Http/
│   ├── Controllers/        # Controllers
│   ├── Middleware/         # Middlewares
│   └── Requests/           # Form Requests
├── Domains/                # Organização por domínio (futuro)
└── Providers/              # Service Providers

database/
├── migrations/             # Migrations
├── seeders/                # Seeders
└── factories/              # Factories

routes/
└── api.php                 # Rotas da API

config/
├── app.php
├── database.php
├── auth.php
├── sanctum.php
└── queue.php
```

## Autenticação

### Login
```bash
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@medflow.local",
  "password": "password"
}
```

**Resposta:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {...},
    "token": "..."
  }
}
```

### Credenciais de Desenvolvimento

**Admin:**
- Email: `admin@medflow.local`
- Senha: `password`

**Gestor Financeiro:**
- Email: `gestor@medflow.local`
- Senha: `password`

**Administrativo:**
- Email: `admin.clinica@medflow.local`
- Senha: `password`

## Multi-Tenancy

O sistema implementa multi-tenancy com single database + tenant_id.

### Isolamento de Tenant

Todas as queries são automaticamente filtradas por `clinic_id` do usuário autenticado através do trait `HasTenant` e global scope `TenantScope`.

```php
// Automaticamente filtra por clinic_id do usuário
$records = Record::all();

// Sem filtro (apenas admin)
$records = Record::withoutTenantScope()->all();
```

## RBAC (Roles & Permissions)

### Roles Disponíveis
- `admin` - Acesso total
- `financial_manager` - Gestão de uploads, registros e relatórios
- `administrative` - Upload e visualização de registros
- `viewer` - Visualização apenas (read-only)

### Verificar Permissões

```php
$user->hasPermission('upload.create');
$user->hasRole('admin');
$user->hasAnyRole(['admin', 'financial_manager']);
```

## Migrations

### Executar
```bash
php artisan migrate
```

### Reverter
```bash
php artisan migrate:rollback
```

### Seed
```bash
php artisan db:seed
```

## Testes

```bash
php artisan test
php artisan test --coverage
```

## Fila (Queue)

### Processar Jobs
```bash
php artisan queue:work
```

### Monitorar Jobs Falhados
```bash
php artisan queue:failed
```

## Auditoria

Todas as ações (POST, PUT, PATCH, DELETE) são automaticamente auditadas através do middleware `AuditLogMiddleware`.

Acessar logs:
```
GET /api/audit-logs
```

## Configuração de Armazenamento

### Local (Desenvolvimento)
```
FILESYSTEM_DISK=local
```

### S3/Minio (Produção)
```
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_BUCKET=medflow-uploads
```

## Próximas Etapas

- [ ] Implementar Controllers de autenticação
- [ ] Implementar Controllers de recursos (Upload, Record, Report, etc)
- [ ] Implementar Services de negócio
- [ ] Implementar Validações e Regras
- [ ] Implementar Jobs de processamento
- [ ] Testes unitários e de integração
- [ ] Documentação de API (Swagger)

## Suporte

Para dúvidas ou problemas, consulte a documentação em `docs/`
