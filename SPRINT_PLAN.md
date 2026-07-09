# 📋 SPRINT PLAN - MedFlow Finance MVP

**Data de Início:** Janeiro 2026  
**Objetivo:** Transformar o projeto em um MVP funcional de ponta a ponta  
**Status:** 🟡 EM PROGRESSO

---

## 📊 Diagnóstico Inicial

### Estado Geral do Projeto
- **Completude:** 85% (conforme auditoria anterior)
- **Arquitetura:** Sólida (Laravel 11 + Vue 3)
- **Pipeline:** Implementado (5 Jobs encadeados)
- **Testes:** Parcialmente implementados (6 testes existentes)

### Problemas Críticos Identificados

#### 🔴 CRÍTICO
1. **ProcessUploadJob não é disparado** (Linha 130-131 em UploadController.php)
   - Status: TODO comentado
   - Impacto: Upload não processa automaticamente
   - Solução: Descomente e importe ProcessUploadJob

2. **Clinic model usa `is_active` mas pode ter inconsistência**
   - Status: StoreUploadRequest verifica `is_active` (linha 15)
   - Impacto: Validação pode falhar
   - Solução: Verificar se campo existe no banco e no model

3. **phpoffice/phpspreadsheet não está no composer.json**
   - Status: Não listado em require
   - Impacto: Parser Excel pode falhar
   - Solução: Adicionar dependência

#### 🟡 IMPORTANTE
4. **Falta de queue worker no Docker Compose**
   - Status: Não há serviço de fila
   - Impacto: Jobs não executam
   - Solução: Adicionar serviço queue ao docker-compose.yml

5. **Ausência de arquivo CSV de exemplo**
   - Status: Não existe
   - Impacto: Dificulta testes manuais
   - Solução: Criar arquivo de exemplo

6. **Falta de teste de fluxo completo**
   - Status: Testes existem mas não cobrem fluxo ponta a ponta
   - Impacto: Impossível validar pipeline inteiro
   - Solução: Criar UploadProcessingFlowTest.php

---

## 🎯 Sprints Planejadas

### Sprint 0 — Auditoria e Estabilização (CONCLUÍDA)
- [x] Análise do repositório
- [x] Identificação de bloqueadores
- [x] Documentação de problemas
- [x] Criação deste plano

### Sprint 1 — Fluxo de Upload Funcional (EM PROGRESSO)

#### Tarefas
- [x] **1.1** Corrigir autorização do upload (StoreUploadRequest)
- [x] **1.2** Adicionar phpoffice/phpspreadsheet
- [x] **1.3** Disparar ProcessUploadJob no UploadController
- [ ] **1.4** Validar ordem do pipeline de Jobs
- [ ] **1.5** Ajustar contadores do upload
- [x] **1.6** Adicionar queue worker ao Docker Compose
- [x] **1.7** Criar arquivo CSV de exemplo
- [x] **1.8** Criar teste de fluxo completo

#### Critérios de Aceite
- [ ] Upload CSV válido gera registros no banco
- [ ] Status do upload chega em `completed`
- [ ] Validações estão vinculadas a registros reais
- [ ] Erros são registrados quando há dados inválidos
- [ ] Dashboard exibe dados derivados dos registros
- [ ] Teste de fluxo completo passa
- [ ] Projeto funciona via Docker Compose

### Sprint 2 — Relatórios e ROI (PENDENTE)
- [ ] Alinhar contrato de relatório frontend/backend
- [ ] Corrigir retorno da criação de relatório
- [ ] Implementar exportação CSV real
- [ ] Ajustar ROI com datas
- [ ] Criar testes de relatórios

### Sprint 3 — UX e Componentes (PENDENTE)
- [ ] Tela de detalhe do upload
- [ ] Polling de status
- [ ] Componentes reutilizáveis
- [ ] Melhorar feedback de erros

### Sprint 4 — Piloto Assistido (PENDENTE)
- [ ] Documentação PILOT_RUNBOOK.md
- [ ] Seed de demonstração
- [ ] Checklist técnico de pré-piloto
- [ ] Melhorias de segurança mínima
- [ ] Healthchecks básicos

### Sprint 5 — Competitividade (BACKLOG)
- [ ] Importação TISS/XML
- [ ] Motor de regras por convênio
- [ ] Workflow de recuperação
- [ ] Recomendações por IA
- [ ] Score de risco

---

## 📝 Arquivos a Serem Alterados

### Sprint 1

#### Backend
1. `MedFlow_Finance_Backend/app/Http/Controllers/UploadController.php`
   - Linha 130-131: Descomente ProcessUploadJob::dispatch()
   - Adicione import

2. `MedFlow_Finance_Backend/app/Http/Requests/StoreUploadRequest.php`
   - Linha 15: Verificar se `is_active` existe ou usar método

3. `MedFlow_Finance_Backend/composer.json`
   - Adicionar `phpoffice/phpspreadsheet` em require

4. `docker-compose.yml`
   - Adicionar serviço `queue` para Laravel queue worker

5. `MedFlow_Finance_Backend/tests/Feature/UploadProcessingFlowTest.php`
   - Novo arquivo: Teste de fluxo completo

6. `MedFlow_Finance_Backend/tests/Fixtures/sample_billing.csv`
   - Novo arquivo: Dados de exemplo para testes

#### Frontend
- Nenhuma alteração crítica nesta sprint

---

## 🔄 Ordem de Execução

1. **Corrigir StoreUploadRequest** (5 min)
2. **Adicionar phpoffice/phpspreadsheet** (2 min)
3. **Disparar ProcessUploadJob** (5 min)
4. **Adicionar queue worker ao Docker** (10 min)
5. **Criar arquivo CSV de exemplo** (10 min)
6. **Criar teste de fluxo completo** (30 min)
7. **Validar tudo localmente** (30 min)

**Tempo Total Estimado:** 1-2 horas

---

## ✅ Checklist de Conclusão

### Sprint 1 - Fluxo de Upload
- [ ] Arquivo CSV de exemplo criado
- [ ] ProcessUploadJob disparado automaticamente
- [ ] Pipeline executa sem erros
- [ ] Registros são criados no banco
- [ ] Validações são vinculadas aos registros
- [ ] Status do upload atualiza para `completed`
- [ ] Teste de fluxo completo passa
- [ ] Docker Compose sobe sem erros
- [ ] Queue worker está ativo
- [ ] Documentação atualizada

### Sprint 2 - Relatórios
- [ ] Contrato de API padronizado
- [ ] Relatórios são criados corretamente
- [ ] CSV é exportado corretamente
- [ ] ROI funciona com filtros de data
- [ ] Testes de relatórios passam

### Sprint 3 - UX
- [ ] Tela de detalhe do upload funciona
- [ ] Polling de status implementado
- [ ] Componentes reutilizáveis criados
- [ ] Feedback de erros é amigável
- [ ] Interface responsiva

### Sprint 4 - Piloto
- [ ] Documentação de piloto completa
- [ ] Seed de demo funciona
- [ ] Checklist técnico documentado
- [ ] Segurança mínima implementada
- [ ] Healthchecks funcionam

---

## 📌 Notas Importantes

1. **Docker Compose:** Será necessário adicionar serviço de queue worker
2. **Composer:** Será necessário rodar `composer install` após adicionar phpoffice
3. **Testes:** Executar com `php artisan test` após cada alteração
4. **Git:** Fazer commits pequenos e claros após cada tarefa

---

## 🚀 Como Executar Localmente

```bash
# 1. Clonar repositório
git clone https://github.com/LeonardoRFragoso/MedFlow_Finance.git
cd MedFlow_Finance

# 2. Subir Docker Compose
docker-compose up -d

# 3. Aguardar inicialização (2-3 min)
sleep 180

# 4. Verificar status
docker-compose ps

# 5. Acessar frontend
# http://localhost:5173

# 6. Fazer login
# Email: admin@medflow.local
# Senha: password

# 7. Testar upload
# - Ir para aba "Uploads"
# - Fazer upload de CSV
# - Observar processamento
# - Verificar registros criados
```

---

## 📞 Contato e Suporte

Para dúvidas durante a execução:
1. Consultar documentação em `MedFlow_Finance_Docs/`
2. Verificar logs do Docker: `docker-compose logs -f backend`
3. Verificar fila: `docker-compose exec backend php artisan queue:failed`

---

**Última Atualização:** Janeiro 2026  
**Responsável:** Cascade AI  
**Status:** 🟡 EM PROGRESSO
