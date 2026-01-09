# ESTRUTURA DO REPOSITÓRIO - MEDFLOW FINANCE

**Data:** Janeiro 2026  
**Status:** Definição completa - Pronto para implementação  
**Versão:** 1.0

---

## 1. VISÃO GERAL DA ESTRUTURA

```
medflow-finance/
├── backend/                    # Laravel 11 API
├── frontend/                   # Vue 3 SPA
├── docs/                       # Documentação
├── docker-compose.yml          # Ambiente local
├── .github/                    # GitHub Actions
└── README.md                   # Guia do projeto
```

---

## 2. ESTRUTURA DO BACKEND (Laravel 11)

```
backend/
├── app/
│   ├── Console/
│   │   └── Commands/           # Comandos customizados
│   │       ├── ProcessUploadCommand.php
│   │       └── CleanupOldFilesCommand.php
│   │
│   ├── Domains/                # Separação por domínio
│   │   ├── Auth/
│   │   │   ├── Controllers/
│   │   │   │   └── AuthController.php
│   │   │   ├── Models/
│   │   │   │   └── User.php
│   │   │   ├── Requests/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   └── RegisterRequest.php
│   │   │   ├── Services/
│   │   │   │   └── AuthService.php
│   │   │   └── Routes/
│   │   │       └── auth.php
│   │   │
│   │   ├── Clinic/
│   │   │   ├── Controllers/
│   │   │   │   └── ClinicController.php
│   │   │   ├── Models/
│   │   │   │   └── Clinic.php
│   │   │   ├── Requests/
│   │   │   │   ├── StoreClinicRequest.php
│   │   │   │   └── UpdateClinicRequest.php
│   │   │   ├── Services/
│   │   │   │   └── ClinicService.php
│   │   │   └── Routes/
│   │   │       └── clinics.php
│   │   │
│   │   ├── Upload/
│   │   │   ├── Controllers/
│   │   │   │   └── UploadController.php
│   │   │   ├── Models/
│   │   │   │   └── Upload.php
│   │   │   ├── Requests/
│   │   │   │   └── StoreUploadRequest.php
│   │   │   ├── Services/
│   │   │   │   ├── UploadService.php
│   │   │   │   └── StorageService.php
│   │   │   ├── Jobs/
│   │   │   │   └── ProcessUploadJob.php
│   │   │   └── Routes/
│   │   │       └── uploads.php
│   │   │
│   │   ├── Parser/
│   │   │   ├── Services/
│   │   │   │   ├── FileParserService.php
│   │   │   │   ├── ExcelParser.php
│   │   │   │   ├── CSVParser.php
│   │   │   │   └── XMLParser.php
│   │   │   └── Contracts/
│   │   │       └── ParserInterface.php
│   │   │
│   │   ├── Normalization/
│   │   │   ├── Services/
│   │   │   │   └── DataNormalizer.php
│   │   │   └── Normalizers/
│   │   │       ├── DateNormalizer.php
│   │   │       ├── MoneyNormalizer.php
│   │   │       ├── CPFNormalizer.php
│   │   │       └── CNPJNormalizer.php
│   │   │
│   │   ├── Record/
│   │   │   ├── Controllers/
│   │   │   │   └── RecordController.php
│   │   │   ├── Models/
│   │   │   │   └── Record.php
│   │   │   ├── Requests/
│   │   │   │   └── UpdateRecordRequest.php
│   │   │   ├── Services/
│   │   │   │   └── RecordService.php
│   │   │   └── Routes/
│   │   │       └── records.php
│   │   │
│   │   ├── Validation/
│   │   │   ├── Controllers/
│   │   │   │   └── ValidationController.php
│   │   │   ├── Models/
│   │   │   │   ├── Validation.php
│   │   │   │   └── Error.php
│   │   │   ├── Services/
│   │   │   │   ├── RulesEngine.php
│   │   │   │   ├── ValidationService.php
│   │   │   │   └── ErrorReporter.php
│   │   │   ├── Rules/
│   │   │   │   ├── Rule.php (abstrata)
│   │   │   │   ├── FieldValidationRule.php
│   │   │   │   ├── BusinessLogicRule.php
│   │   │   │   ├── ComplianceRule.php
│   │   │   │   └── GlosaDetectionRule.php
│   │   │   ├── RuleSets/
│   │   │   │   ├── RuleSet.php (abstrata)
│   │   │   │   ├── FileFormatRuleSet.php
│   │   │   │   ├── DataValidationRuleSet.php
│   │   │   │   └── BillingRuleSet.php
│   │   │   └── Routes/
│   │   │       └── validations.php
│   │   │
│   │   ├── Report/
│   │   │   ├── Controllers/
│   │   │   │   └── ReportController.php
│   │   │   ├── Models/
│   │   │   │   ├── Report.php
│   │   │   │   └── ReportExport.php
│   │   │   ├── Services/
│   │   │   │   ├── ReportService.php
│   │   │   │   ├── ExportService.php
│   │   │   │   └── DashboardService.php
│   │   │   ├── Jobs/
│   │   │   │   ├── GenerateReportJob.php
│   │   │   │   └── ExportDataJob.php
│   │   │   ├── Exports/
│   │   │   │   ├── ReportExport.php
│   │   │   │   └── PDFExport.php
│   │   │   └── Routes/
│   │   │       └── reports.php
│   │   │
│   │   ├── Audit/
│   │   │   ├── Models/
│   │   │   │   └── AuditLog.php
│   │   │   ├── Services/
│   │   │   │   └── AuditLogger.php
│   │   │   ├── Observers/
│   │   │   │   └── AuditObserver.php
│   │   │   └── Routes/
│   │   │       └── audit.php
│   │   │
│   │   └── User/
│   │       ├── Controllers/
│   │       │   └── UserController.php
│   │       ├── Models/
│   │       │   ├── Role.php
│   │       │   ├── Permission.php
│   │       │   └── User.php (relacionamentos)
│   │       ├── Requests/
│   │       │   ├── StoreUserRequest.php
│   │       │   └── UpdateUserRequest.php
│   │       ├── Services/
│   │       │   └── UserService.php
│   │       └── Routes/
│   │           └── users.php
│   │
│   ├── Exceptions/
│   │   ├── Handler.php
│   │   ├── TenantNotFoundException.php
│   │   ├── UnauthorizedException.php
│   │   └── ValidationException.php
│   │
│   ├── Http/
│   │   ├── Middleware/
│   │   │   ├── SetTenant.php
│   │   │   ├── CheckPermission.php
│   │   │   ├── RateLimiter.php
│   │   │   └── LogRequest.php
│   │   ├── Requests/
│   │   │   └── FormRequest.php (base)
│   │   └── Resources/
│   │       ├── UserResource.php
│   │       ├── ClinicResource.php
│   │       ├── UploadResource.php
│   │       ├── RecordResource.php
│   │       ├── ValidationResource.php
│   │       ├── ErrorResource.php
│   │       ├── ReportResource.php
│   │       └── AuditLogResource.php
│   │
│   ├── Models/
│   │   ├── Traits/
│   │   │   ├── HasTenant.php
│   │   │   ├── HasAudit.php
│   │   │   └── HasSoftDeletes.php
│   │   └── BaseModel.php
│   │
│   ├── Policies/
│   │   ├── TenantPolicy.php
│   │   ├── UploadPolicy.php
│   │   ├── RecordPolicy.php
│   │   ├── ReportPolicy.php
│   │   └── UserPolicy.php
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── RouteServiceProvider.php
│   │   └── DomainServiceProvider.php
│   │
│   └── Traits/
│       ├── ApiResponse.php
│       ├── HasFilters.php
│       └── HasPagination.php
│
├── bootstrap/
│   ├── app.php
│   └── cache/
│
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── queue.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── cors.php
│   ├── tenancy.php (custom)
│   └── validation-rules.php (custom)
│
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000000_create_clinics_table.php
│   │   ├── 2024_01_01_000001_create_roles_table.php
│   │   ├── 2024_01_01_000002_create_permissions_table.php
│   │   ├── 2024_01_01_000003_create_users_table.php
│   │   ├── 2024_01_01_000004_create_uploads_table.php
│   │   ├── 2024_01_01_000005_create_records_table.php
│   │   ├── 2024_01_01_000006_create_validations_table.php
│   │   ├── 2024_01_01_000007_create_errors_table.php
│   │   ├── 2024_01_01_000008_create_reports_table.php
│   │   ├── 2024_01_01_000009_create_audit_logs_table.php
│   │   └── 2024_01_01_000010_create_clinic_settings_table.php
│   │
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── RoleSeeder.php
│   │   ├── PermissionSeeder.php
│   │   ├── ClinicSeeder.php (desenvolvimento)
│   │   └── UserSeeder.php (desenvolvimento)
│   │
│   └── factories/
│       ├── ClinicFactory.php
│       ├── UserFactory.php
│       ├── UploadFactory.php
│       ├── RecordFactory.php
│       └── ValidationFactory.php
│
├── routes/
│   ├── api.php                 # Rotas da API
│   ├── web.php                 # Rotas web (se necessário)
│   └── domains/                # Rotas por domínio
│       ├── auth.php
│       ├── clinics.php
│       ├── uploads.php
│       ├── records.php
│       ├── validations.php
│       ├── reports.php
│       ├── users.php
│       └── audit.php
│
├── storage/
│   ├── app/
│   │   ├── uploads/            # Uploads locais (dev)
│   │   └── exports/            # Exportações locais
│   ├── logs/
│   └── framework/
│
├── tests/
│   ├── Feature/
│   │   ├── Auth/
│   │   │   ├── LoginTest.php
│   │   │   ├── LogoutTest.php
│   │   │   └── RefreshTokenTest.php
│   │   ├── Upload/
│   │   │   ├── UploadFileTest.php
│   │   │   └── ProcessUploadTest.php
│   │   ├── Validation/
│   │   │   ├── FieldValidationTest.php
│   │   │   ├── BusinessLogicTest.php
│   │   │   └── GlosaDetectionTest.php
│   │   ├── Report/
│   │   │   ├── GenerateReportTest.php
│   │   │   └── ExportReportTest.php
│   │   └── Security/
│   │       ├── TenantIsolationTest.php
│   │       ├── AuthorizationTest.php
│   │       └── RateLimitingTest.php
│   │
│   ├── Unit/
│   │   ├── Parsers/
│   │   │   ├── ExcelParserTest.php
│   │   │   └── CSVParserTest.php
│   │   ├── Normalization/
│   │   │   ├── DateNormalizerTest.php
│   │   │   ├── MoneyNormalizerTest.php
│   │   │   └── CPFNormalizerTest.php
│   │   ├── Validation/
│   │   │   ├── RulesEngineTest.php
│   │   │   ├── FieldValidationRuleTest.php
│   │   │   └── GlosaDetectionRuleTest.php
│   │   └── Services/
│   │       ├── AuthServiceTest.php
│   │       ├── ReportServiceTest.php
│   │       └── DashboardServiceTest.php
│   │
│   ├── TestCase.php
│   ├── CreatesApplication.php
│   └── Fixtures/
│       ├── sample-excel.xlsx
│       ├── sample-csv.csv
│       └── sample-data.json
│
├── .env.example
├── .env.testing
├── .gitignore
├── composer.json
├── composer.lock
├── artisan
├── phpunit.xml
├── php-cs-fixer.php (code style)
├── psalm.xml (static analysis)
└── README.md
```

---

## 3. ESTRUTURA DO FRONTEND (Vue 3)

```
frontend/
├── src/
│   ├── assets/
│   │   ├── styles/
│   │   │   ├── main.css        # Tailwind + customizações
│   │   │   ├── variables.css
│   │   │   └── animations.css
│   │   ├── images/
│   │   │   ├── logo.svg
│   │   │   └── icons/
│   │   └── fonts/
│   │
│   ├── components/
│   │   ├── Common/
│   │   │   ├── Navbar.vue
│   │   │   ├── Sidebar.vue
│   │   │   ├── Footer.vue
│   │   │   └── Breadcrumb.vue
│   │   │
│   │   ├── UI/
│   │   │   ├── Button.vue
│   │   │   ├── Input.vue
│   │   │   ├── Select.vue
│   │   │   ├── Checkbox.vue
│   │   │   ├── Modal.vue
│   │   │   ├── Tabs.vue
│   │   │   ├── Card.vue
│   │   │   ├── Badge.vue
│   │   │   ├── Alert.vue
│   │   │   ├── Spinner.vue
│   │   │   └── Pagination.vue
│   │   │
│   │   ├── Table/
│   │   │   ├── DataTable.vue
│   │   │   ├── TableHeader.vue
│   │   │   ├── TableRow.vue
│   │   │   └── TableActions.vue
│   │   │
│   │   ├── Form/
│   │   │   ├── FormGroup.vue
│   │   │   ├── FormField.vue
│   │   │   ├── FormError.vue
│   │   │   └── FormSubmit.vue
│   │   │
│   │   ├── Chart/
│   │   │   ├── BarChart.vue
│   │   │   ├── PieChart.vue
│   │   │   ├── LineChart.vue
│   │   │   └── ChartContainer.vue
│   │   │
│   │   └── Notification/
│   │       ├── Toast.vue
│   │       ├── ToastContainer.vue
│   │       └── Notification.vue
│   │
│   ├── pages/
│   │   ├── Auth/
│   │   │   ├── Login.vue
│   │   │   ├── Logout.vue
│   │   │   └── ForgotPassword.vue (nice-to-have)
│   │   │
│   │   ├── Dashboard/
│   │   │   ├── Dashboard.vue
│   │   │   ├── DashboardMetrics.vue
│   │   │   ├── DashboardCharts.vue
│   │   │   └── DashboardTables.vue
│   │   │
│   │   ├── Upload/
│   │   │   ├── UploadPage.vue
│   │   │   ├── UploadList.vue
│   │   │   ├── UploadDetail.vue
│   │   │   ├── UploadForm.vue
│   │   │   └── UploadProgress.vue
│   │   │
│   │   ├── Record/
│   │   │   ├── RecordList.vue
│   │   │   ├── RecordDetail.vue
│   │   │   ├── RecordEdit.vue
│   │   │   └── RecordFilters.vue
│   │   │
│   │   ├── Report/
│   │   │   ├── ReportList.vue
│   │   │   ├── ReportDetail.vue
│   │   │   ├── ReportGenerate.vue
│   │   │   └── ReportExport.vue
│   │   │
│   │   ├── User/
│   │   │   ├── UserList.vue
│   │   │   ├── UserForm.vue
│   │   │   └── UserDetail.vue
│   │   │
│   │   ├── Settings/
│   │   │   ├── ClinicSettings.vue
│   │   │   ├── ValidationSettings.vue
│   │   │   └── BillingSettings.vue
│   │   │
│   │   ├── Error/
│   │   │   ├── NotFound.vue
│   │   │   ├── Unauthorized.vue
│   │   │   └── ServerError.vue
│   │   │
│   │   └── Layout/
│   │       ├── MainLayout.vue
│   │       ├── AuthLayout.vue
│   │       └── AdminLayout.vue
│   │
│   ├── stores/
│   │   ├── auth.js             # Pinia store
│   │   ├── clinic.js
│   │   ├── upload.js
│   │   ├── record.js
│   │   ├── report.js
│   │   ├── user.js
│   │   ├── notification.js
│   │   └── ui.js
│   │
│   ├── services/
│   │   ├── api.js              # Configuração Axios
│   │   ├── authService.js
│   │   ├── clinicService.js
│   │   ├── uploadService.js
│   │   ├── recordService.js
│   │   ├── reportService.js
│   │   ├── userService.js
│   │   ├── dashboardService.js
│   │   └── storageService.js
│   │
│   ├── composables/
│   │   ├── useAuth.js
│   │   ├── useApi.js
│   │   ├── useNotification.js
│   │   ├── useForm.js
│   │   ├── usePagination.js
│   │   ├── useFilters.js
│   │   └── useLoading.js
│   │
│   ├── utils/
│   │   ├── formatters.js       # Formatação de dados
│   │   ├── validators.js       # Validação de formulários
│   │   ├── constants.js        # Constantes da app
│   │   ├── helpers.js          # Funções auxiliares
│   │   └── errorHandler.js     # Tratamento de erros
│   │
│   ├── router/
│   │   ├── index.js            # Configuração do router
│   │   ├── routes.js           # Definição de rotas
│   │   └── guards.js           # Route guards
│   │
│   ├── directives/
│   │   ├── vClickOutside.js
│   │   ├── vFocus.js
│   │   └── vPermission.js
│   │
│   ├── filters/
│   │   ├── currency.js
│   │   ├── date.js
│   │   └── truncate.js
│   │
│   ├── App.vue
│   └── main.js
│
├── tests/
│   ├── unit/
│   │   ├── components/
│   │   │   ├── Button.spec.js
│   │   │   ├── Input.spec.js
│   │   │   └── Modal.spec.js
│   │   ├── stores/
│   │   │   ├── auth.spec.js
│   │   │   ├── upload.spec.js
│   │   │   └── report.spec.js
│   │   ├── services/
│   │   │   ├── authService.spec.js
│   │   │   ├── uploadService.spec.js
│   │   │   └── reportService.spec.js
│   │   └── utils/
│   │       ├── formatters.spec.js
│   │       └── validators.spec.js
│   │
│   ├── e2e/
│   │   ├── login.spec.js
│   │   ├── upload.spec.js
│   │   ├── dashboard.spec.js
│   │   └── report.spec.js
│   │
│   ├── fixtures/
│   │   ├── users.json
│   │   ├── uploads.json
│   │   └── reports.json
│   │
│   └── setup.js
│
├── public/
│   ├── index.html
│   ├── favicon.ico
│   └── robots.txt
│
├── .env.example
├── .env.development
├── .env.production
├── .gitignore
├── vite.config.js
├── vitest.config.js
├── tailwind.config.js
├── postcss.config.js
├── package.json
├── package-lock.json
├── eslint.config.js
├── prettier.config.js
└── README.md
```

---

## 4. PADRÕES DE NOMENCLATURA

### 4.1 Backend (Laravel)

#### Classes
```
Controllers:     UserController, UploadController
Models:          User, Upload, Record
Services:        UserService, UploadService
Jobs:            ProcessUploadJob, GenerateReportJob
Requests:        StoreUserRequest, UpdateUploadRequest
Resources:       UserResource, UploadResource
Policies:        UserPolicy, UploadPolicy
Traits:          HasTenant, HasAudit
Exceptions:      TenantNotFoundException
```

#### Métodos
```
Controllers:     index, show, store, update, destroy
Services:        create, update, delete, get, list
Jobs:            handle
Models:          scopeForTenant, scopeActive
```

#### Variáveis
```
camelCase:       $userId, $uploadData, $validationRules
Constants:       UPLOAD_MAX_SIZE, RETENTION_DAYS
```

#### Arquivos
```
Migrations:      YYYY_MM_DD_HHMMSS_create_table_name.php
Seeders:         RoleSeeder, PermissionSeeder
Factories:       UserFactory, UploadFactory
Tests:           UserTest, UploadTest
```

### 4.2 Frontend (Vue 3)

#### Componentes
```
PascalCase:      Button.vue, DataTable.vue, UploadForm.vue
Páginas:         Dashboard.vue, UploadList.vue, RecordDetail.vue
Layouts:         MainLayout.vue, AuthLayout.vue
```

#### Stores (Pinia)
```
camelCase:       auth.js, upload.js, report.js
Actions:         setUser, fetchUploads, createReport
Getters:         isAuthenticated, uploadCount
State:           user, uploads, loading
```

#### Composables
```
useXxx:          useAuth, useApi, useNotification
Retorna:         { state, methods }
```

#### Serviços
```
camelCase:       authService.js, uploadService.js
Métodos:         login, fetchUploads, createReport
```

#### Variáveis
```
camelCase:       userId, uploadData, isLoading
Constants:       MAX_FILE_SIZE, API_BASE_URL
Booleanos:       isLoading, hasError, isVisible
```

#### Arquivos
```
Componentes:     Button.vue, DataTable.vue
Páginas:         Dashboard.vue, UploadList.vue
Stores:          auth.js, upload.js
Serviços:        authService.js, uploadService.js
Testes:          Button.spec.js, auth.spec.js
```

---

## 5. ORGANIZAÇÃO POR DOMÍNIO

### 5.1 Domínio: Auth

```
app/Domains/Auth/
├── Controllers/
│   └── AuthController.php
├── Models/
│   └── User.php
├── Requests/
│   ├── LoginRequest.php
│   └── RegisterRequest.php
├── Services/
│   └── AuthService.php
└── Routes/
    └── auth.php
```

**Responsabilidades:**
- Login/Logout
- Geração de tokens
- Validação de credenciais

### 5.2 Domínio: Upload

```
app/Domains/Upload/
├── Controllers/
│   └── UploadController.php
├── Models/
│   └── Upload.php
├── Requests/
│   └── StoreUploadRequest.php
├── Services/
│   ├── UploadService.php
│   └── StorageService.php
├── Jobs/
│   └── ProcessUploadJob.php
└── Routes/
    └── uploads.php
```

**Responsabilidades:**
- Receber arquivo
- Validar tipo/tamanho
- Armazenar em S3/Minio
- Disparar processamento

### 5.3 Domínio: Validation

```
app/Domains/Validation/
├── Controllers/
│   └── ValidationController.php
├── Models/
│   ├── Validation.php
│   └── Error.php
├── Services/
│   ├── RulesEngine.php
│   ├── ValidationService.php
│   └── ErrorReporter.php
├── Rules/
│   ├── Rule.php (abstrata)
│   ├── FieldValidationRule.php
│   ├── BusinessLogicRule.php
│   └── GlosaDetectionRule.php
├── RuleSets/
│   ├── RuleSet.php (abstrata)
│   ├── FileFormatRuleSet.php
│   └── BillingRuleSet.php
└── Routes/
    └── validations.php
```

**Responsabilidades:**
- Executar validações
- Aplicar regras de negócio
- Detectar glosas
- Gerar relatórios de erro

### 5.4 Domínio: Report

```
app/Domains/Report/
├── Controllers/
│   └── ReportController.php
├── Models/
│   ├── Report.php
│   └── ReportExport.php
├── Services/
│   ├── ReportService.php
│   ├── ExportService.php
│   └── DashboardService.php
├── Jobs/
│   ├── GenerateReportJob.php
│   └── ExportDataJob.php
├── Exports/
│   ├── ReportExport.php
│   └── PDFExport.php
└── Routes/
    └── reports.php
```

**Responsabilidades:**
- Gerar relatórios
- Exportar dados
- Agregações financeiras
- Dashboard

---

## 6. CONVENÇÕES DE CÓDIGO

### 6.1 Backend (Laravel)

#### Imports
```php
// Ordenar: built-in → Laravel → App
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\UserService;
```

#### Namespaces
```php
namespace App\Domains\Auth\Controllers;
namespace App\Domains\Auth\Services;
namespace App\Domains\Auth\Requests;
```

#### Métodos em Controllers
```php
public function index(Request $request)
public function show($id)
public function store(StoreRequest $request)
public function update($id, UpdateRequest $request)
public function destroy($id)
```

#### Métodos em Services
```php
public function create(array $data): Model
public function update(Model $model, array $data): Model
public function delete(Model $model): bool
public function get($id): Model
public function list(array $filters = []): Collection
```

#### Traits
```php
trait HasTenant {
    protected static function booted()
    {
        static::addGlobalScope(new TenantScope);
    }
}
```

### 6.2 Frontend (Vue 3)

#### Imports
```javascript
// Ordenar: Vue → Pinia → Services → Utils
import { ref, computed } from 'vue'
import { useStore } from '@/stores/auth'
import { authService } from '@/services/authService'
import { formatCurrency } from '@/utils/formatters'
```

#### Componentes
```vue
<template>
  <div class="component">
    <!-- Template -->
  </div>
</template>

<script setup>
// Imports
// Props
// Emits
// State
// Computed
// Methods
// Lifecycle
</script>

<style scoped>
/* Styles */
</style>
```

#### Stores (Pinia)
```javascript
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: null,
  }),
  
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  
  actions: {
    setUser(user) {
      this.user = user
    },
  },
})
```

---

## 7. CONFIGURAÇÃO DE AMBIENTE

### 7.1 Backend (.env)

```
APP_NAME=MedFlow Finance
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=medflow_finance
DB_USERNAME=postgres
DB_PASSWORD=secret

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

QUEUE_CONNECTION=redis

MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@medflow.local

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=medflow-uploads

SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:5173
```

### 7.2 Frontend (.env)

```
VITE_API_BASE_URL=http://localhost:8000/api
VITE_APP_NAME=MedFlow Finance
VITE_APP_VERSION=1.0.0
```

---

## 8. SCRIPTS & COMANDOS

### 8.1 Backend (composer.json)

```json
{
  "scripts": {
    "dev": "php artisan serve",
    "test": "php artisan test",
    "test:coverage": "php artisan test --coverage",
    "lint": "php-cs-fixer fix",
    "lint:check": "php-cs-fixer fix --dry-run",
    "analyze": "psalm",
    "migrate": "php artisan migrate",
    "seed": "php artisan db:seed",
    "queue:work": "php artisan queue:work",
    "queue:failed": "php artisan queue:failed"
  }
}
```

### 8.2 Frontend (package.json)

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview",
    "test": "vitest",
    "test:coverage": "vitest --coverage",
    "lint": "eslint src --fix",
    "lint:check": "eslint src",
    "format": "prettier --write src",
    "type-check": "vue-tsc --noEmit"
  }
}
```

---

## 9. GITIGNORE

### 9.1 Backend

```
/vendor/
/node_modules/
.env
.env.local
.env.*.local
/storage/logs/*
/storage/app/uploads/*
/bootstrap/cache/*
.DS_Store
*.swp
*.swo
.idea/
.vscode/
*.log
```

### 9.2 Frontend

```
/node_modules/
/dist/
.env.local
.env.*.local
.DS_Store
*.swp
*.swo
.idea/
.vscode/
*.log
coverage/
```

---

## 10. ESTRUTURA DE ROTAS

### 10.1 Backend (routes/api.php)

```php
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    
    // Clinics
    Route::apiResource('clinics', ClinicController::class);
    Route::get('/clinics/{clinic}/settings', [ClinicController::class, 'settings']);
    Route::put('/clinics/{clinic}/settings', [ClinicController::class, 'updateSettings']);
    
    // Uploads
    Route::apiResource('uploads', UploadController::class);
    Route::get('/uploads/{upload}/status', [UploadController::class, 'status']);
    Route::get('/uploads/{upload}/progress', [UploadController::class, 'progress']);
    
    // Records
    Route::apiResource('records', RecordController::class)->only(['index', 'show', 'update']);
    Route::get('/records/search', [RecordController::class, 'search']);
    
    // Validations
    Route::get('/validations', [ValidationController::class, 'index']);
    Route::get('/validations/by-upload/{upload}', [ValidationController::class, 'byUpload']);
    Route::get('/validations/by-record/{record}', [ValidationController::class, 'byRecord']);
    
    // Reports
    Route::apiResource('reports', ReportController::class)->only(['index', 'show', 'store']);
    Route::get('/reports/{report}/export/csv', [ReportController::class, 'exportCsv']);
    Route::get('/reports/{report}/export/pdf', [ReportController::class, 'exportPdf']);
    
    // Dashboard
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('/dashboard/metrics', [DashboardController::class, 'metrics']);
    
    // Users (Admin)
    Route::middleware('can:user.create')->group(function () {
        Route::apiResource('users', UserController::class);
    });
    
    // Audit (Admin)
    Route::middleware('can:audit.read')->group(function () {
        Route::get('/audit-logs', [AuditController::class, 'index']);
        Route::get('/audit-logs/by-user/{user}', [AuditController::class, 'byUser']);
    });
});
```

### 10.2 Frontend (router/routes.js)

```javascript
const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/pages/Auth/Login.vue'),
    meta: { layout: 'auth', requiresAuth: false }
  },
  {
    path: '/',
    name: 'Dashboard',
    component: () => import('@/pages/Dashboard/Dashboard.vue'),
    meta: { layout: 'main', requiresAuth: true }
  },
  {
    path: '/uploads',
    name: 'UploadList',
    component: () => import('@/pages/Upload/UploadList.vue'),
    meta: { layout: 'main', requiresAuth: true }
  },
  {
    path: '/uploads/:id',
    name: 'UploadDetail',
    component: () => import('@/pages/Upload/UploadDetail.vue'),
    meta: { layout: 'main', requiresAuth: true }
  },
  // ... mais rotas
]
```

---

## 11. ESTRUTURA DE TESTES

### 11.1 Backend (tests/)

```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   └── LogoutTest.php
│   ├── Upload/
│   │   ├── UploadFileTest.php
│   │   └── ProcessUploadTest.php
│   └── Security/
│       └── TenantIsolationTest.php
├── Unit/
│   ├── Parsers/
│   │   └── ExcelParserTest.php
│   ├── Validation/
│   │   └── RulesEngineTest.php
│   └── Services/
│       └── AuthServiceTest.php
└── Fixtures/
    └── sample-excel.xlsx
```

### 11.2 Frontend (tests/)

```
tests/
├── unit/
│   ├── components/
│   │   └── Button.spec.js
│   ├── stores/
│   │   └── auth.spec.js
│   └── services/
│       └── authService.spec.js
├── e2e/
│   └── login.spec.js
└── fixtures/
    └── users.json
```

---

## 12. DOCUMENTAÇÃO

### 12.1 README Backend

```markdown
# MedFlow Finance - Backend

## Setup

1. Clone o repositório
2. `cp .env.example .env`
3. `composer install`
4. `php artisan key:generate`
5. `php artisan migrate --seed`
6. `php artisan serve`

## Testes

```bash
php artisan test
php artisan test --coverage
```

## API Documentation

Swagger: http://localhost:8000/api/docs
```

### 12.2 README Frontend

```markdown
# MedFlow Finance - Frontend

## Setup

1. Clone o repositório
2. `npm install`
3. `npm run dev`

## Testes

```bash
npm run test
npm run test:coverage
```

## Build

```bash
npm run build
```
```

---

## 13. DOCKER COMPOSE

```yaml
version: '3.8'

services:
  postgres:
    image: postgres:14
    environment:
      POSTGRES_DB: medflow_finance
      POSTGRES_USER: postgres
      POSTGRES_PASSWORD: secret
    ports:
      - "5432:5432"
    volumes:
      - postgres_data:/var/lib/postgresql/data

  redis:
    image: redis:7
    ports:
      - "6379:6379"

  minio:
    image: minio/minio
    environment:
      MINIO_ROOT_USER: minioadmin
      MINIO_ROOT_PASSWORD: minioadmin
    ports:
      - "9000:9000"
      - "9001:9001"
    command: server /data --console-address ":9001"
    volumes:
      - minio_data:/data

  backend:
    build:
      context: ./backend
      dockerfile: Dockerfile
    ports:
      - "8000:8000"
    environment:
      - DB_HOST=postgres
      - REDIS_HOST=redis
    depends_on:
      - postgres
      - redis
      - minio
    volumes:
      - ./backend:/app

  frontend:
    build:
      context: ./frontend
      dockerfile: Dockerfile
    ports:
      - "5173:5173"
    depends_on:
      - backend
    volumes:
      - ./frontend:/app

volumes:
  postgres_data:
  minio_data:
```

---

## 14. PRÓXIMOS PASSOS

1. ✅ Análise do projeto
2. ✅ Arquitetura definida
3. ✅ Schema do banco definido
4. ✅ Escopo técnico definido
5. ✅ Backlog técnico detalhado
6. ✅ Estrutura do repositório definida

---

## 15. RESUMO FINAL

Todas as 6 etapas foram completadas com sucesso:

### ✅ ETAPA 1: Análise Completa
- Validação de coerência entre documentos
- Identificação de inconsistências e riscos
- Suposições explícitas documentadas

### ✅ ETAPA 2: Arquitetura Final
- Padrão de tenancy definido (Single DB + tenant_id)
- Autenticação com Sanctum
- Separação de domínios clara
- Segurança em múltiplas camadas

### ✅ ETAPA 3: Modelagem de Dados
- Schema completo do banco
- 11 tabelas principais
- Índices estratégicos
- Constraints e validações

### ✅ ETAPA 4: Escopo Técnico
- Funcionalidades MUST HAVE vs NICE TO HAVE
- Endpoints da API documentados
- Componentes do frontend definidos
- Critérios de aceite claros

### ✅ ETAPA 5: Backlog Técnico
- 11 épicos com 75 histórias
- 370 story points totais
- Timeline: 8-10 semanas
- Dependências mapeadas

### ✅ ETAPA 6: Estrutura do Repositório
- Organização por domínio
- Padrões de nomenclatura
- Convenções de código
- Estrutura de testes

---

## 📋 DOCUMENTOS GERADOS

1. `docs/analysis/01_project_analysis.md` - Análise completa
2. `docs/architecture/02_mvp_architecture.md` - Arquitetura final
3. `docs/database/03_database_schema.md` - Schema do banco
4. `docs/mvp/04_mvp_scope_technical.md` - Escopo técnico
5. `docs/backlog/05_technical_backlog.md` - Backlog detalhado
6. `docs/architecture/06_repository_structure.md` - Estrutura do repositório

---

## ✨ PRONTO PARA DESENVOLVIMENTO

O projeto está 100% preparado para iniciar o desenvolvimento sem retrabalho estrutural. Todos os documentos estão alinhados, decisões técnicas foram tomadas e o backlog está pronto para execução.
