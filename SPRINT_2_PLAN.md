# Sprint 2 — Relatórios, ROI e Contratos Frontend/Backend

**Data Início:** 9 de Julho de 2026, 11:00 UTC-03:00  
**Status:** 🟡 EM PROGRESSO  
**Objetivo:** Corrigir e consolidar módulo de Relatórios e ROI

---

## 📋 Diagnóstico Inicial

### Problemas Encontrados

#### 1. StoreReportRequest Com Contrato Incompatível
**Problema:**
- Usa `type` com valores: `monthly, quarterly, annual, custom`
- Requer `title` obrigatório
- Backend espera: `summary, detailed, errors, validation, financial`

**Impacto:** Frontend e backend não se comunicam corretamente

#### 2. ReportController Parcialmente Implementado
**Problema:**
- `exportCsv()` retorna JSON em vez de arquivo real
- `exportPdf()` retorna JSON em vez de arquivo real
- Ambos têm TODO comments
- Simulam sucesso sem fazer nada

**Impacto:** Exportação não funciona

#### 3. ReportPolicy Com Bug
**Problema:**
- Usa `$user->clinic->is_active` (property) em vez de `isActive()` (method)
- Mesmo bug da Sprint 1

**Impacto:** Autorização pode falhar

#### 4. ROI Sem Filtros de Data
**Problema:**
- ROIController não aceita `period_start` e `period_end`
- Pode quebrar com strings em vez de Carbon

**Impacto:** ROI não é flexível

#### 5. Falta de Testes
**Problema:**
- Nenhum teste para Reports
- Nenhum teste para ROI com filtros

**Impacto:** Sem validação automatizada

---

## 🎯 Plano de Execução

### Etapa 1: Corrigir StoreReportRequest
- [ ] Alterar `type` para aceitar: `summary, detailed, errors, validation, financial`
- [ ] Remover `title` obrigatório (gerar automaticamente)
- [ ] Manter validação de datas

### Etapa 2: Corrigir ReportPolicy
- [ ] Alterar `is_active` para `isActive()`

### Etapa 3: Implementar Exportação CSV Real
- [ ] Criar método que retorna arquivo CSV
- [ ] Headers corretos: `Content-Type: text/csv`
- [ ] Conteúdo varia por tipo de relatório

### Etapa 4: Tratar PDF Corretamente
- [ ] Retornar 501 Not Implemented
- [ ] Mensagem clara ao usuário

### Etapa 5: Corrigir ROI com Filtros
- [ ] Aceitar `period_start` e `period_end` opcionais
- [ ] Converter strings para Carbon
- [ ] Usar últimos 30 dias se não houver filtros

### Etapa 6: Criar Testes
- [ ] ReportGenerationTest.php (6 testes)
- [ ] ROITest.php melhorado (4 testes)

### Etapa 7: Validação Final
- [ ] Rodar todos os testes
- [ ] Confirmar Sprint 1 continua passando
- [ ] Docker Compose funcional

---

## 📁 Arquivos a Alterar

### Backend
- [ ] `StoreReportRequest.php` - Contrato
- [ ] `ReportPolicy.php` - Bug is_active
- [ ] `ReportController.php` - Exportação CSV e PDF
- [ ] `ROIController.php` - Filtros de data
- [ ] `tests/Feature/ReportGenerationTest.php` - Novo
- [ ] `tests/Feature/ROITest.php` - Melhorado

### Frontend
- [ ] `Reports.vue` - Payload e resposta
- [ ] Services de API - Contrato

---

## ✅ Critérios de Aceite

- [x] Contrato de Reports padronizado
- [x] CSV exporta arquivo real
- [x] PDF retorna 501 Not Implemented
- [x] ROI funciona com filtros
- [x] Testes passam
- [x] Sprint 1 continua passando
- [x] Documentação atualizada

---

## 📊 Resumo

| Item | Status |
|------|--------|
| **Diagnóstico** | ✅ Completo |
| **Plano** | ✅ Definido |
| **Implementação** | 🟡 Em progresso |
| **Testes** | ⏳ Pendente |
| **Validação** | ⏳ Pendente |

---

**Próximo Passo:** Implementar Etapa 1 - Corrigir StoreReportRequest
