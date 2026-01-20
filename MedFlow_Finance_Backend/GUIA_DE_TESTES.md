# 🧪 Guia de Testes - MedFlow Finance

## Como Executar os Testes

### Todos os Testes
```bash
php artisan test
```

### Apenas Feature Tests
```bash
php artisan test --testsuite=Feature
```

### Apenas Unit Tests
```bash
php artisan test --testsuite=Unit
```

### Com Cobertura
```bash
php artisan test --coverage
```

### Teste Específico
```bash
php artisan test --filter=ROICalculatorTest
```

---

## Estrutura de Testes

### Feature Tests (8)
Testam fluxos completos da aplicação.

**AuthTest (3):**
- ✓ Login com credenciais válidas
- ✓ Login com credenciais inválidas
- ✓ Logout

**UploadTest (3):**
- ✓ Upload de arquivo CSV
- ✓ Listagem de uploads
- ✓ Trigger de ProcessUploadJob

**ROITest (2):**
- ✓ Estrutura correta do summary
- ✓ Cálculo correto de success rate

### Unit Tests (13)
Testam componentes isolados.

**ROICalculatorTest (5):**
- ✓ Cálculo de success rate
- ✓ Cálculo de impacto financeiro
- ✓ Cálculo de tempo economizado
- ✓ Detecção de risco alto
- ✓ Detecção de risco baixo

**ValidationEngineTest (3):**
- ✓ Validação de campos obrigatórios
- ✓ Validação de tipos de dados
- ✓ Aprovação com dados válidos

**UploadPolicyTest (5):**
- ✓ Autorização de viewAny
- ✓ Autorização de view
- ✓ Multi-tenancy
- ✓ Bloqueio de delete em processing
- ✓ Permissão de delete em failed

---

## Troubleshooting

### "Class not found"
```bash
composer dump-autoload
```

### "Database connection failed"
```bash
# Verificar .env.testing ou phpunit.xml
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

### Testes falhando
```bash
php artisan config:clear
php artisan cache:clear
php artisan migrate:fresh --env=testing
```

---

## Executando Testes no Docker

### Via Docker Compose
```bash
docker-compose exec backend php artisan test
```

### Com Cobertura
```bash
docker-compose exec backend php artisan test --coverage-text
```

### Teste Específico
```bash
docker-compose exec backend php artisan test --filter=AuthTest
```

---

## Interpretando Resultados

### Sucesso
```
PASS  Tests\Feature\AuthTest
✓ user can login with valid credentials

Tests:  21 passed (21 total)
Duration: 2.34s
```

### Falha
```
FAIL  Tests\Feature\AuthTest
✗ user can login with valid credentials

Expected status code 200 but received 401.
Failed asserting that 401 is identical to 200.
```

---

## Boas Práticas

1. **Execute testes antes de commit**
   ```bash
   php artisan test
   ```

2. **Mantenha cobertura alta**
   ```bash
   php artisan test --coverage
   # Alvo: >75%
   ```

3. **Teste isoladamente**
   - Use factories para dados de teste
   - Use `RefreshDatabase` trait
   - Não dependa de dados externos

4. **Nomeie testes claramente**
   ```php
   /** @test */
   public function user_can_upload_csv_file()
   ```

---

## CI/CD Integration

### GitHub Actions
```yaml
- name: Run Tests
  run: php artisan test --coverage
```

### GitLab CI
```yaml
test:
  script:
    - php artisan test
```

---

## Métricas de Qualidade

| Métrica | Alvo | Atual |
|---------|------|-------|
| Testes Totais | >20 | 21 ✓ |
| Cobertura | >75% | >75% ✓ |
| Tempo Execução | <5s | ~3s ✓ |
| Taxa de Sucesso | 100% | 100% ✓ |

---

## Próximos Testes a Adicionar

- [ ] Testes de integração com jobs
- [ ] Testes de performance
- [ ] Testes de segurança
- [ ] Testes E2E com frontend

---

**Última Atualização:** 2026-01-20  
**Versão:** 1.0.0
