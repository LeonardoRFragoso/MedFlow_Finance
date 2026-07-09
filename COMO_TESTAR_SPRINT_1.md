# 🧪 Como Testar a Sprint 1 - Guia Prático

**Objetivo:** Validar o fluxo de upload funcional de ponta a ponta  
**Tempo Estimado:** 15-20 minutos  
**Pré-requisitos:** Docker, Docker Compose, Git

---

## 📋 Checklist Rápido

- [ ] Docker e Docker Compose instalados
- [ ] Repositório clonado
- [ ] Ambiente Docker subido
- [ ] Testes executados
- [ ] Frontend acessível
- [ ] Upload testado manualmente
- [ ] Registros criados no banco
- [ ] Dashboard atualizado

---

## 🚀 Passo 1: Preparar Ambiente

### 1.1 Clonar Repositório
```bash
git clone https://github.com/LeonardoRFragoso/MedFlow_Finance.git
cd MedFlow_Finance
```

### 1.2 Verificar Docker
```bash
# Verificar instalação
docker --version
docker-compose --version

# Exemplo de saída esperada:
# Docker version 24.0.0+
# Docker Compose version 2.20.0+
```

### 1.3 Subir Ambiente
```bash
# Subir todos os serviços
docker-compose up -d

# Saída esperada:
# Creating medflow_postgres ... done
# Creating medflow_redis ... done
# Creating medflow_backend ... done
# Creating medflow_queue ... done
# Creating medflow_frontend ... done
```

### 1.4 Aguardar Inicialização
```bash
# Aguardar 2-3 minutos para inicialização completa
sleep 180

# Verificar status dos serviços
docker-compose ps

# Saída esperada:
# NAME                COMMAND                  SERVICE      STATUS      PORTS
# medflow_postgres    postgres:15-alpine       postgres     Up 2 min    5432/tcp
# medflow_redis       redis:7-alpine           redis        Up 2 min    6379/tcp
# medflow_backend     php artisan serve        backend      Up 1 min    8000/tcp
# medflow_queue       php artisan queue:work   queue        Up 1 min
# medflow_frontend    npm run dev              frontend     Up 1 min    5173/tcp
```

---

## 🧪 Passo 2: Executar Testes Automatizados

### 2.1 Testes de Upload (Sprint 1)
```bash
# Executar testes de upload
docker-compose exec backend php artisan test tests/Feature/UploadProcessingFlowTest.php

# Saída esperada:
# PASS  Tests\Feature\UploadProcessingFlowTest
#   ✓ user can upload csv file
#   ✓ upload triggers processing job
#   ✓ user cannot upload without permission
#   ✓ user cannot upload if clinic inactive
#   ✓ upload validates file type
#   ✓ upload validates billing period
#   ✓ upload prevents duplicate files
#   ✓ user can view upload list
#   ✓ user can view upload details
#   ✓ user can check upload status
#   ✓ user can delete upload
#   ✓ user cannot view other clinic uploads
#   ✓ upload respects monthly limit
#   ✓ upload respects file size limit
#
# Tests: 14 passed
```

### 2.2 Testes de ROI (Existentes)
```bash
# Executar testes de ROI
docker-compose exec backend php artisan test tests/Feature/ROITest.php

# Saída esperada:
# PASS  Tests\Feature\ROITest
#   ✓ roi summary returns correct structure
#   ✓ roi calculates success rate correctly
#
# Tests: 2 passed
```

### 2.3 Todos os Testes
```bash
# Executar todos os testes
docker-compose exec backend php artisan test

# Saída esperada:
# PASS  Tests\Feature\AuthTest
# PASS  Tests\Feature\UploadTest
# PASS  Tests\Feature\UploadProcessingFlowTest
# PASS  Tests\Feature\ROITest
# PASS  Tests\Unit\ValidationEngineTest
# PASS  Tests\Unit\UploadPolicyTest
# PASS  Tests\Unit\ROICalculatorTest
#
# Tests: 20 passed
```

---

## 🌐 Passo 3: Testar Frontend Manualmente

### 3.1 Acessar Sistema
```
URL: http://localhost:5173
Email: admin@medflow.local
Senha: password
```

### 3.2 Fazer Login
1. Abrir navegador
2. Acessar http://localhost:5173
3. Inserir email: `admin@medflow.local`
4. Inserir senha: `password`
5. Clicar em "Entrar"

**Resultado esperado:** Redirecionado para Dashboard

### 3.3 Visualizar Dashboard
1. Observar 4 cards principais:
   - Total Faturado
   - Taxa de Sucesso
   - Valor em Risco
   - Potencial de Recuperação

**Resultado esperado:** Cards exibem valores (podem ser 0 se é primeira vez)

---

## 📤 Passo 4: Testar Upload

### 4.1 Preparar Arquivo CSV
```bash
# Copiar arquivo de exemplo para desktop (opcional)
cp MedFlow_Finance_Backend/tests/Fixtures/sample_billing.csv ~/Desktop/

# Ou criar um novo arquivo com este conteúdo:
# procedure_code,procedure_date,amount_billed,patient_name,patient_cpf,insurance_name,insurance_id,provider_name,authorization_number,amount_paid,amount_pending
# PROC001,2024-01-15,1500.00,João Silva,12345678901,Unimed,UNI001,Clínica Central,AUTH001,1500.00,0.00
# PROC002,2024-01-16,2500.00,Maria Santos,98765432109,Bradesco Saúde,BRAD001,Clínica Central,AUTH002,2500.00,0.00
```

### 4.2 Fazer Upload via Frontend
1. Clicar em "Uploads" no menu
2. Clicar em "Novo Upload"
3. Selecionar arquivo CSV
4. Definir período:
   - Data Início: 2024-01-01
   - Data Fim: 2024-01-31
5. Adicionar descrição (opcional): "Teste Sprint 1"
6. Clicar em "Enviar"

**Resultado esperado:**
- Upload criado com status "pending"
- Mensagem de sucesso exibida
- Redirecionado para lista de uploads

### 4.3 Observar Processamento
1. Voltar para lista de uploads
2. Observar o upload criado
3. Status deve mudar:
   - `pending` → `processing` (30 segundos)
   - `processing` → `completed` (30-60 segundos)

**Resultado esperado:** Status final = `completed`

### 4.4 Verificar Detalhes do Upload
1. Clicar no upload
2. Observar estatísticas:
   - Total de Linhas
   - Linhas Válidas
   - Linhas com Erro
   - Taxa de Sucesso

**Resultado esperado:**
- Total de Linhas = número de registros no CSV
- Linhas Válidas > 0
- Taxa de Sucesso > 0%

---

## 📊 Passo 5: Verificar Registros Criados

### 5.1 Acessar Lista de Registros
1. Clicar em "Registros" no menu
2. Observar lista de registros

**Resultado esperado:** Registros do upload aparecem na lista

### 5.2 Filtrar por Upload
1. Usar filtro de busca (se disponível)
2. Filtrar por data do upload

**Resultado esperado:** Registros do upload aparecem

### 5.3 Visualizar Detalhes de Registro
1. Clicar em um registro
2. Observar campos:
   - Código do Procedimento
   - Data do Procedimento
   - Valor Faturado
   - Nome do Paciente
   - Convênio
   - Status

**Resultado esperado:** Todos os campos preenchidos corretamente

---

## 📈 Passo 6: Verificar Dashboard Atualizado

### 6.1 Voltar para Dashboard
1. Clicar em "Dashboard" no menu
2. Observar cards atualizados

**Resultado esperado:**
- Total Faturado > 0
- Taxa de Sucesso > 0%
- Valor em Risco pode ser > 0
- Potencial de Recuperação pode ser > 0

### 6.2 Verificar Métricas
1. Observar se números mudaram em relação ao passo 3.3

**Resultado esperado:** Números atualizados com dados do novo upload

---

## 🔍 Passo 7: Verificar Banco de Dados (Opcional)

### 7.1 Conectar ao PostgreSQL
```bash
# Acessar banco de dados
docker-compose exec postgres psql -U postgres -d medflow_finance

# Listar uploads
SELECT id, original_filename, status, total_rows, valid_rows, error_rows FROM uploads ORDER BY created_at DESC LIMIT 5;

# Listar registros
SELECT id, procedure_code, amount_billed, status FROM records LIMIT 10;

# Sair
\q
```

**Resultado esperado:**
- Upload aparece com status `completed`
- Registros aparecem com status `pending`
- Contadores (total_rows, valid_rows) preenchidos

### 7.2 Verificar Validações
```bash
# Acessar banco
docker-compose exec postgres psql -U postgres -d medflow_finance

# Listar validações
SELECT id, rule_name, is_valid, severity FROM validations LIMIT 10;

# Sair
\q
```

**Resultado esperado:** Validações criadas para cada registro

---

## 📋 Passo 8: Verificar Logs

### 8.1 Logs do Backend
```bash
# Ver últimos logs
docker-compose logs -f backend | head -50

# Procurar por "Iniciando processamento"
docker-compose logs backend | grep "Iniciando processamento"

# Procurar por "Upload finalizado"
docker-compose logs backend | grep "Upload finalizado"
```

**Resultado esperado:**
- Log de início do processamento
- Log de cada job executado
- Log de finalização com sucesso

### 8.2 Logs da Fila
```bash
# Ver logs da fila
docker-compose logs -f queue | head -50

# Procurar por jobs processados
docker-compose logs queue | grep "Processing"
```

**Resultado esperado:** Jobs sendo processados pela fila

---

## ✅ Checklist de Validação

Marque cada item conforme validar:

### Testes Automatizados
- [ ] 14 testes de upload passam
- [ ] 2 testes de ROI passam
- [ ] Todos os 20 testes passam

### Frontend
- [ ] Login funciona
- [ ] Dashboard exibe métricas
- [ ] Página de uploads carrega
- [ ] Novo upload pode ser criado

### Upload
- [ ] Arquivo é aceito
- [ ] Status muda para "processing"
- [ ] Status muda para "completed"
- [ ] Estatísticas são exibidas

### Registros
- [ ] Registros aparecem na lista
- [ ] Registros têm dados corretos
- [ ] Filtros funcionam

### Dashboard
- [ ] Métricas são atualizadas
- [ ] Números fazem sentido

### Banco de Dados
- [ ] Upload aparece com status `completed`
- [ ] Registros foram criados
- [ ] Validações foram criadas

### Logs
- [ ] Logs mostram processamento
- [ ] Sem erros críticos

---

## 🐛 Troubleshooting

### Problema: "Connection refused" ao acessar frontend
**Solução:**
```bash
# Verificar se frontend está rodando
docker-compose ps frontend

# Se não estiver, reiniciar
docker-compose restart frontend

# Aguardar 30 segundos e tentar novamente
```

### Problema: Upload fica em "processing" indefinidamente
**Solução:**
```bash
# Verificar se queue está rodando
docker-compose ps queue

# Ver logs da fila
docker-compose logs -f queue

# Reiniciar fila
docker-compose restart queue
```

### Problema: Testes falham
**Solução:**
```bash
# Limpar cache
docker-compose exec backend php artisan cache:clear

# Resetar banco de testes
docker-compose exec backend php artisan migrate:fresh --seed

# Rodar testes novamente
docker-compose exec backend php artisan test
```

### Problema: Arquivo não é encontrado
**Solução:**
```bash
# Verificar permissões de storage
docker-compose exec backend chmod -R 755 storage/

# Verificar se diretório existe
docker-compose exec backend ls -la storage/app/uploads/
```

### Problema: Erro de conexão com banco
**Solução:**
```bash
# Verificar se postgres está rodando
docker-compose ps postgres

# Ver logs do postgres
docker-compose logs postgres

# Reiniciar postgres
docker-compose restart postgres

# Aguardar 30 segundos
sleep 30

# Rodar migrations
docker-compose exec backend php artisan migrate --seed
```

---

## 📞 Comandos Úteis

### Gerenciamento de Serviços
```bash
# Subir todos os serviços
docker-compose up -d

# Parar todos os serviços
docker-compose down

# Reiniciar um serviço
docker-compose restart backend

# Ver status
docker-compose ps

# Ver logs em tempo real
docker-compose logs -f backend
```

### Testes
```bash
# Rodar testes específicos
docker-compose exec backend php artisan test tests/Feature/UploadProcessingFlowTest.php

# Rodar com output detalhado
docker-compose exec backend php artisan test --verbose

# Rodar um teste específico
docker-compose exec backend php artisan test --filter=user_can_upload_csv_file
```

### Banco de Dados
```bash
# Acessar banco
docker-compose exec postgres psql -U postgres -d medflow_finance

# Resetar banco
docker-compose exec backend php artisan migrate:fresh --seed

# Ver migrations
docker-compose exec backend php artisan migrate:status
```

### Fila
```bash
# Ver jobs falhados
docker-compose exec backend php artisan queue:failed

# Reprocessar jobs falhados
docker-compose exec backend php artisan queue:retry all

# Limpar jobs falhados
docker-compose exec backend php artisan queue:flush
```

---

## 📊 Resultado Esperado Final

Após completar todos os passos, você deve ter:

✅ **14 testes passando** (UploadProcessingFlowTest)  
✅ **Upload criado** com status `completed`  
✅ **Registros criados** no banco de dados  
✅ **Validações criadas** para cada registro  
✅ **Dashboard atualizado** com métricas  
✅ **Logs mostrando** processamento bem-sucedido  

---

## 🎉 Conclusão

Se todos os itens acima foram validados com sucesso, a **Sprint 1 foi concluída com sucesso** e o fluxo de upload está **100% funcional**.

Próximo passo: **Sprint 2 - Relatórios e ROI**

---

**Última Atualização:** Janeiro 2026  
**Responsável:** Cascade AI
