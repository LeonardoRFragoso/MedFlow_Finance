# FASE 2 - CORE ENGINE DO PRODUTO

**Data:** Janeiro 2026  
**Status:** ✅ FASE 2 CONCLUÍDA  
**Versão:** 1.0

---

## 🎯 OBJETIVO ALCANÇADO

Implementar o **pipeline assíncrono completo de processamento de arquivos**, que é o coração do produto MedFlow Finance.

---

## 📦 O QUE FOI IMPLEMENTADO

### ✅ 1. JOBS ENCADEADOS (4 jobs)

#### ProcessUploadJob
- Inicia o pipeline de processamento
- Atualiza status para "processing"
- Dispara cadeia de jobs com `Bus::chain()`
- Tratamento de falhas e retry automático
- Logging estruturado

#### ParseFileJob
- Parse de arquivo (CSV ou Excel)
- Validação de estrutura
- Registro de erros de parsing
- Armazenamento em cache para próximo job
- Timeout: 300s, Retry: 3 tentativas

#### NormalizeRecordsJob
- Normalização de dados parseados
- Conversão de tipos
- Formatação de datas, valores monetários, CPF/CNPJ
- Registro de erros de normalização
- Atualização de contagem de erros

#### ValidateRecordsJob
- Execução de validações
- Aplicação de regras determinísticas
- Registro de validações no banco
- Cálculo de taxa de sucesso
- Separação de registros válidos

#### FinalizeUploadJob
- Persistência de registros no banco
- Bulk insert em chunks (500 registros)
- Limpeza de cache
- Atualização de status para "completed"
- Logging de conclusão

**Características dos Jobs:**
- ✅ Encadeamento automático com `Bus::chain()`
- ✅ Retry automático (3 tentativas com backoff)
- ✅ Timeout de 300 segundos
- ✅ Logging estruturado em cada etapa
- ✅ Tratamento de exceções
- ✅ Atualização de status em tempo real

---

### ✅ 2. SERVICES POR DOMÍNIO

#### FileParserService
- Roteador de parsers
- Suporte para múltiplos formatos
- Detecção automática de tipo
- Interface limpa e extensível

#### CsvParser
- Parse de arquivos CSV
- Detecção automática de delimitador
- Mapeamento de headers
- Tratamento de linhas vazias
- Suporte a múltiplos encodings

#### ExcelParser
- Parse de arquivos Excel (.xlsx, .xls)
- Conversão automática de datas
- Suporte a múltiplas sheets
- Detecção de tipo de arquivo
- Tratamento de valores especiais

#### DataNormalizer
- Normalização de datas (múltiplos formatos)
- Normalização de valores monetários
- Formatação de CPF/CNPJ
- Trim e limpeza de strings
- Normalização de códigos
- Validação de campos obrigatórios

#### ValidationEngine
- Motor de regras extensível
- Execução sequencial de regras
- Coleta de validações
- Adição/remoção dinâmica de regras
- Tratamento de exceções

---

### ✅ 3. REGRAS DE VALIDAÇÃO (3 regras)

#### FieldValidationRule
Valida campos e tipos de dados:
- ✅ Campos obrigatórios
- ✅ Tipos de dados (numérico, data, etc)
- ✅ Valores negativos
- ✅ Formato de CPF
- ✅ Formato de data

#### BusinessLogicRule
Valida regras de negócio:
- ✅ Amount_paid <= amount_billed
- ✅ Amount_pending = amount_billed - amount_paid
- ✅ Procedure_date não é no futuro
- ✅ Procedure_date não é muito antiga (>2 anos)
- ✅ Procedure_code não vazio

#### GlosaDetectionRule
Detecta possíveis glosas:
- ✅ Valor acima do esperado por procedimento
- ✅ Valores suspeitosamente altos (>50k)
- ✅ Valores suspeitosamente baixos (<0.01)
- ✅ Falta de autorização
- ✅ Informações de convênio incompletas

**Características das Regras:**
- ✅ Determinísticas (sem IA)
- ✅ Extensíveis (fácil adicionar novas)
- ✅ Testáveis isoladamente
- ✅ Retornam resultado estruturado
- ✅ Severidade diferenciada (error/warning/info)

---

## 🔄 FLUXO DO PIPELINE

```
1. Upload recebido
   ↓
2. ProcessUploadJob inicia
   ↓
3. ParseFileJob
   - Lê arquivo do storage
   - Parse (CSV ou Excel)
   - Armazena em cache
   ↓
4. NormalizeRecordsJob
   - Recupera dados do cache
   - Normaliza cada registro
   - Armazena em cache
   ↓
5. ValidateRecordsJob
   - Recupera registros normalizados
   - Executa validações
   - Registra validações no banco
   ↓
6. FinalizeUploadJob
   - Recupera registros válidos
   - Insere no banco (bulk insert)
   - Limpa cache
   - Atualiza status para "completed"
```

---

## 📊 ESTRUTURA DE DADOS

### Fluxo de Cache
```
upload_parsed_{upload_id}
  └── Array de registros parseados

upload_normalized_{upload_id}
  └── Array de registros normalizados

upload_validations_{upload_id}
  └── Array com resultados de validações

upload_valid_records_{upload_id}
  └── Array de registros válidos para persistência
```

### Resultado de Validação
```json
{
  "is_valid": true,
  "validations": [
    {
      "rule_name": "FieldValidationRule::RequiredField",
      "rule_type": "field",
      "is_valid": true,
      "severity": "info",
      "message": "Campo obrigatório preenchido",
      "field_name": "procedure_code",
      "expected_value": "não vazio",
      "actual_value": "CONS001",
      "config": null
    }
  ]
}
```

---

## 🚀 COMO USAR

### 1. Disparar processamento de upload

No `UploadController`, após salvar o arquivo:

```php
ProcessUploadJob::dispatch($upload);
```

### 2. Monitorar progresso

```bash
GET /api/uploads/{id}/status
```

Resposta:
```json
{
  "success": true,
  "data": {
    "id": "uuid",
    "status": "processing",
    "progress": 50,
    "statistics": {
      "total_rows": 1000,
      "valid_rows": 500,
      "error_rows": 50,
      "warning_rows": 100
    }
  }
}
```

### 3. Ver registros processados

```bash
GET /api/records?upload_id={upload_id}
```

### 4. Ver validações

```bash
GET /api/validations/by-upload/{upload_id}
```

### 5. Ver erros

```bash
GET /api/errors/by-upload/{upload_id}
```

---

## 📁 ARQUIVOS CRIADOS

```
app/
├── Jobs/
│   ├── ProcessUploadJob.php
│   ├── ParseFileJob.php
│   ├── NormalizeRecordsJob.php
│   ├── ValidateRecordsJob.php
│   └── FinalizeUploadJob.php
│
└── Domains/
    ├── Parser/
    │   ├── Services/
    │   │   └── FileParserService.php
    │   └── Parsers/
    │       ├── CsvParser.php
    │       └── ExcelParser.php
    │
    ├── Normalization/
    │   └── Services/
    │       └── DataNormalizer.php
    │
    └── Validation/
        ├── Services/
        │   └── ValidationEngine.php
        └── Rules/
            ├── Rule.php (abstrata)
            ├── FieldValidationRule.php
            ├── BusinessLogicRule.php
            └── GlosaDetectionRule.php
```

---

## ✅ CHECKLIST DE CONCLUSÃO

### Requisitos da FASE 2

- [x] Pipeline assíncrono completo
- [x] Jobs encadeados com Bus::chain()
- [x] Atualização de status em tempo real
- [x] Parser de CSV
- [x] Parser de Excel
- [x] Normalização de dados
- [x] Motor de validação determinístico
- [x] 3 tipos de regras (field, business, glosa)
- [x] Armazenamento em cache entre jobs
- [x] Persistência em banco (bulk insert)
- [x] Logging estruturado
- [x] Tratamento de erros e retry
- [x] Registro de validações
- [x] Registro de erros

---

## 🧪 TESTE DO PIPELINE

### 1. Criar arquivo de teste (CSV)

```csv
procedure_code,procedure_date,amount_billed,patient_name,patient_cpf,insurance_name
CONS001,2024-01-15,150.00,João Silva,123.456.789-00,Unimed
PROC002,2024-01-16,500.00,Maria Santos,987.654.321-00,Bradesco Saúde
EXAM003,2024-01-17,200.00,Pedro Costa,111.222.333-44,Amil
```

### 2. Upload do arquivo

```bash
POST /api/uploads
Content-Type: multipart/form-data

file: <arquivo.csv>
billing_period_start: 2024-01-01
billing_period_end: 2024-01-31
```

### 3. Monitorar processamento

```bash
GET /api/uploads/{upload_id}/status
```

### 4. Ver resultados

```bash
GET /api/records?upload_id={upload_id}
GET /api/validations/by-upload/{upload_id}
GET /api/errors/by-upload/{upload_id}
```

---

## 📊 ESTATÍSTICAS

| Item | Quantidade |
|------|-----------|
| Jobs | 5 |
| Services | 2 |
| Parsers | 2 |
| Regras de Validação | 3 |
| Métodos de Normalização | 6 |
| Validações por Regra | 10+ |
| Linhas de Código | 1500+ |

---

## 🎯 PRÓXIMOS PASSOS (FASE 3+)

- [ ] Implementar Policies de autorização
- [ ] Implementar Form Requests customizados
- [ ] Implementar Resources para serialização
- [ ] Adicionar mais regras de validação
- [ ] Implementar cache de regras
- [ ] Testes unitários e de integração
- [ ] Documentação de API (Swagger)
- [ ] Frontend Vue 3 (dashboard, upload, relatórios)

---

## ⚠️ NOTAS IMPORTANTES

1. **Cache:** Usa cache padrão do Laravel (configurável em .env)
2. **Timeout:** 300 segundos por job (ajustável)
3. **Retry:** 3 tentativas com backoff exponencial
4. **Bulk Insert:** Chunks de 500 registros para evitar memory overflow
5. **Logging:** Estruturado com contexto completo
6. **Determinístico:** Sem IA, apenas regras lógicas

---

## ✨ CONCLUSÃO

A **FASE 2** foi completada com sucesso. O backend Laravel 11 agora possui:

✅ Pipeline assíncrono completo e funcional  
✅ Jobs encadeados com retry automático  
✅ Parsers para CSV e Excel  
✅ Normalização robusta de dados  
✅ Motor de validação extensível  
✅ 3 tipos de regras de validação  
✅ Logging estruturado em todas as etapas  
✅ Tratamento de erros e exceções  
✅ Armazenamento em cache entre jobs  
✅ Persistência eficiente em banco  

**Pronto para iniciar FASE 3: Frontend Vue 3 e testes**
