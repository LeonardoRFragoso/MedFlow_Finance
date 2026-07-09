# Como Testar Sprint 3

**Data:** 9 de Julho de 2026  
**Versão:** 1.0  
**Status:** ✅ PRONTO PARA TESTES

---

## 🚀 Pré-requisitos

### Backend
```bash
docker-compose up -d --build
docker-compose exec backend php artisan migrate:fresh --seed
docker-compose exec backend php artisan queue:work &
```

### Frontend
```bash
cd MedFlow_Finance_Frontend
npm install
npm run dev
```

---

## 🧪 Testes Backend Obrigatórios

### 1. Upload Pipeline End-to-End
```bash
docker-compose exec backend php artisan test tests/Feature/UploadPipelineEndToEndTest.php
```

**Esperado:** ✅ PASS

### 2. Upload Processing Flow
```bash
docker-compose exec backend php artisan test tests/Feature/UploadProcessingFlowTest.php
```

**Esperado:** ✅ PASS

### 3. Report Generation
```bash
docker-compose exec backend php artisan test tests/Feature/ReportGenerationTest.php
```

**Esperado:** ✅ PASS

### 4. ROI Filter
```bash
docker-compose exec backend php artisan test tests/Feature/ROIFilterTest.php
```

**Esperado:** ✅ PASS

### 5. Todos os Testes (Opcional)
```bash
docker-compose exec backend php artisan test
```

---

## 🌐 Validação Manual no Navegador

Acessar: `http://localhost:5173`

### 1. Login
- [ ] Acessar página de login
- [ ] Fazer login com credenciais válidas
- [ ] Ser redirecionado para dashboard

### 2. Dashboard
- [ ] Dashboard carrega corretamente
- [ ] Navbar exibe todos os links
- [ ] Links navegam para páginas corretas

### 3. Uploads (`/uploads`)
- [ ] Página carrega
- [ ] Lista de uploads exibida
- [ ] Status com badges coloridas (pending, processing, completed, failed)
- [ ] Botão "Atualizar" funciona
- [ ] Estado vazio quando sem uploads
- [ ] Estado loading ao carregar
- [ ] Mensagem de erro se API falhar

### 4. Records (`/records`)
- [ ] Página carrega
- [ ] Lista de registros exibida
- [ ] Status com badges coloridas (pending, approved, rejected, disputed)
- [ ] Filtros funcionam:
  - [ ] Filtro por status
  - [ ] Busca por paciente/CPF/código
  - [ ] Filtro por período
- [ ] Botão "Atualizar" funciona
- [ ] Modal de detalhes abre ao clicar em "Ver"
- [ ] Modal exibe todos os campos:
  - [ ] patient_name
  - [ ] patient_cpf
  - [ ] procedure_code
  - [ ] procedure_name
  - [ ] procedure_date (formatado em pt-BR)
  - [ ] insurance_name
  - [ ] amount_billed (formatado em BRL)
  - [ ] amount_paid (formatado em BRL)
  - [ ] amount_pending (formatado em BRL)
  - [ ] status (com StatusBadge)
  - [ ] validations (se existirem)
  - [ ] errors (se existirem)
- [ ] Estado vazio quando sem registros
- [ ] Estado loading ao carregar
- [ ] Mensagem de erro se API falhar

### 5. Reports (`/reports`)
- [ ] Página carrega
- [ ] Formulário de criação exibido
- [ ] Validação frontend:
  - [ ] Tipo obrigatório
  - [ ] Data inicial obrigatória
  - [ ] Data final obrigatória
  - [ ] Data final >= data inicial
- [ ] Mensagens de erro amigáveis para validação
- [ ] Gerar relatório:
  - [ ] Payload usa `type` (não `report_type`)
  - [ ] Mensagem de sucesso exibida
  - [ ] Relatório aparece na lista
- [ ] Lista de relatórios:
  - [ ] Exibe tipo, período, totais
  - [ ] Status com badges
  - [ ] Botão "Ver" abre modal
  - [ ] Botão "CSV" baixa arquivo
  - [ ] Botão "PDF" desabilitado com tooltip
- [ ] Modal de relatório:
  - [ ] Exibe detalhes do relatório
  - [ ] Exibe conteúdo em JSON formatado
  - [ ] Botão "Exportar CSV" funciona
- [ ] Estado vazio quando sem relatórios
- [ ] Estado loading ao carregar
- [ ] Mensagem de erro se API falhar

### 6. ROI (`/roi`)
- [ ] Página carrega
- [ ] Filtro de período:
  - [ ] Data inicial preenchida (últimos 30 dias)
  - [ ] Data final preenchida (hoje)
  - [ ] Botão "Atualizar" funciona
- [ ] Validação de período:
  - [ ] Data inicial obrigatória
  - [ ] Data final obrigatória
  - [ ] Data final >= data inicial
  - [ ] Mensagens de erro amigáveis
- [ ] Métricas exibidas:
  - [ ] Total Faturado (BRL)
  - [ ] Total Aprovado (BRL)
  - [ ] Valor em Risco (BRL)
  - [ ] Recuperação Potencial (BRL)
- [ ] Taxa de sucesso:
  - [ ] Percentual exibido
  - [ ] Barra de progresso funciona
- [ ] Análise de volume:
  - [ ] Total de registros
  - [ ] Aprovados
  - [ ] Rejeitados
  - [ ] Contestados
- [ ] Recomendações:
  - [ ] Exibidas com título, ação, descrição
  - [ ] Priority badge com cores (high=red, medium=yellow, low=green)
  - [ ] Impacto potencial exibido
- [ ] Estado vazio quando sem dados
- [ ] Estado loading ao carregar
- [ ] Mensagem de erro se API falhar

### 7. Responsividade
- [ ] Desktop (1920x1080)
  - [ ] Layout completo
  - [ ] Tabelas visíveis
  - [ ] Modais centrados
- [ ] Tablet (768x1024)
  - [ ] Layout adaptado
  - [ ] Tabelas scrolláveis
  - [ ] Modais responsivos
- [ ] Mobile (375x667)
  - [ ] Layout mobile
  - [ ] Menu responsivo
  - [ ] Tabelas scrolláveis
  - [ ] Modais full-screen

### 8. Dark Mode
- [ ] Toggle dark mode funciona
- [ ] Cores adaptadas
- [ ] Contraste mantido
- [ ] Componentes visíveis

---

## ✅ Checklist de Validação

### Componentes UI
- [ ] LoadingState funciona em todas as páginas
- [ ] EmptyState funciona em todas as páginas
- [ ] ErrorState funciona em todas as páginas
- [ ] StatusBadge exibe status corretos
- [ ] MetricCard formata valores corretamente

### Páginas
- [ ] ROI.vue funciona completamente
- [ ] Reports.vue mantém contrato com `type`
- [ ] Uploads.vue exibe status badges
- [ ] Records.vue exibe status badges e modal correto

### Funcionalidades
- [ ] CSV baixa como arquivo
- [ ] PDF exibe mensagem de indisponibilidade
- [ ] Validações funcionam
- [ ] Filtros funcionam
- [ ] Modais funcionam

### Sprint 1 e 2
- [ ] Upload pipeline continua funcionando
- [ ] Reports continua funcionando
- [ ] ROI continua funcionando
- [ ] Nenhuma regressão detectada

---

## 🐛 Troubleshooting

### Frontend não carrega
```bash
cd MedFlow_Finance_Frontend
npm install
npm run dev
```

### Backend não responde
```bash
docker-compose down -v
docker-compose up -d --build
docker-compose exec backend php artisan migrate:fresh --seed
```

### Testes falham
```bash
docker-compose exec backend php artisan test --verbose
```

### Build falha
```bash
cd MedFlow_Finance_Frontend
rm -rf node_modules dist
npm install
npm run build
```

---

## 📝 Notas

- Todos os testes devem passar
- Build frontend deve completar sem erros
- Validação manual deve cobrir todos os fluxos
- Nenhuma regressão em Sprint 1 e 2
- Responsividade deve ser verificada em múltiplos tamanhos

---

**Desenvolvido por:** Cascade AI  
**Data:** 9 de Julho de 2026  
**Status:** ✅ PRONTO PARA TESTES
