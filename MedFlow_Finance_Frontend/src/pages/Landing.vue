<template>
  <div class="min-h-screen bg-white">
    <!-- Header/Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <!-- Logo -->
          <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <span class="text-xl font-bold text-gray-900">MedFlow Finance</span>
          </div>

          <!-- Navigation -->
          <nav class="hidden md:flex items-center gap-8">
            <a href="#problema" class="text-gray-600 hover:text-primary-600 transition-colors">O Problema</a>
            <a href="#solucao" class="text-gray-600 hover:text-primary-600 transition-colors">Solução</a>
            <a href="#como-funciona" class="text-gray-600 hover:text-primary-600 transition-colors">Como Funciona</a>
            <a href="#pricing" class="text-gray-600 hover:text-primary-600 transition-colors">Investimento</a>
          </nav>

          <!-- CTA Buttons -->
          <div class="flex items-center gap-3">
            <router-link to="/login" class="text-gray-700 hover:text-primary-600 font-medium transition-colors">
              Entrar
            </router-link>
            <router-link to="/register" class="bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors">
              Solicitar Piloto
            </router-link>
          </div>
        </div>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 bg-gradient-to-b from-gray-50 to-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
          <div>
            <div class="inline-flex items-center gap-2 bg-danger-100 text-danger-800 px-4 py-2 rounded-full text-sm font-medium mb-6">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
              Clínicas perdem 10-20% do faturamento todo mês
            </div>
            <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6">
              Pare de perder dinheiro com <span class="text-primary-600">glosas evitáveis</span>
            </h1>
            <p class="text-xl text-gray-600 mb-8 leading-relaxed">
              Automatize, valide e otimize seu faturamento médico. <strong>Aumente sua receita líquida</strong> sem mudar seu fluxo clínico.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
              <router-link to="/register" class="inline-flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-8 py-4 rounded-xl font-semibold text-lg transition-all transform hover:scale-105 shadow-lg shadow-primary-500/30">
                Solicitar Piloto de 30 dias
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
              </router-link>
              <a href="#como-funciona" class="inline-flex items-center justify-center gap-2 border-2 border-gray-200 hover:border-primary-600 text-gray-700 hover:text-primary-600 px-8 py-4 rounded-xl font-semibold text-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Ver Como Funciona
              </a>
            </div>
            <div class="flex items-center gap-8 mt-10 pt-10 border-t border-gray-200">
              <div>
                <div class="text-3xl font-bold text-gray-900">15%</div>
                <div class="text-sm text-gray-500">Recuperação média</div>
              </div>
              <div>
                <div class="text-3xl font-bold text-gray-900">30 dias</div>
                <div class="text-sm text-gray-500">ROI mensurável</div>
              </div>
              <div>
                <div class="text-3xl font-bold text-gray-900">83h</div>
                <div class="text-sm text-gray-500">Economizadas/mês</div>
              </div>
            </div>
          </div>
          <div class="relative">
            <div class="bg-gradient-to-br from-primary-100 to-primary-50 rounded-3xl p-8">
              <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=600&h=400&fit=crop" alt="Dashboard MedFlow Finance" class="rounded-2xl shadow-2xl" />
            </div>
            <!-- Floating Cards -->
            <div class="absolute -left-8 top-1/4 bg-white rounded-xl shadow-xl p-4 animate-bounce-slow">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-warning-100 rounded-lg flex items-center justify-center">
                  <span class="text-lg">⚠️</span>
                </div>
                <div>
                  <div class="text-sm font-semibold text-gray-900">Risco de Glosa</div>
                  <div class="text-xs text-gray-500">R$ 22.500 em risco</div>
                </div>
              </div>
            </div>
            <div class="absolute -right-4 bottom-1/4 bg-white rounded-xl shadow-xl p-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-success-100 rounded-lg flex items-center justify-center">
                  <span class="text-lg">💰</span>
                </div>
                <div>
                  <div class="text-sm font-semibold text-gray-900">Recuperável</div>
                  <div class="text-xs text-gray-500">R$ 3.375 este mês</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Problema Section -->
    <section id="problema" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">O problema que você enfrenta todo mês</h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Clínicas médicas perdem dinheiro mensalmente por problemas que podem ser evitados
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-danger-50 border border-danger-100 rounded-2xl p-6 text-center">
            <div class="w-16 h-16 bg-danger-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-3xl">❌</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Erros de Digitação</h3>
            <p class="text-gray-600 text-sm">Dados inseridos manualmente geram erros que custam caro</p>
          </div>

          <div class="bg-warning-100 border border-warning-100 rounded-2xl p-6 text-center">
            <div class="w-16 h-16 bg-warning-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-3xl">⚠️</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Glosas Evitáveis</h3>
            <p class="text-gray-600 text-sm">Procedimentos faturados incorretamente são rejeitados</p>
          </div>

          <div class="bg-gray-100 border border-gray-200 rounded-2xl p-6 text-center">
            <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-3xl">📋</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Processos Manuais</h3>
            <p class="text-gray-600 text-sm">Planilhas e retrabalho consomem tempo e recursos</p>
          </div>

          <div class="bg-primary-50 border border-primary-100 rounded-2xl p-6 text-center">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-3xl">👁️</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Falta de Visibilidade</h3>
            <p class="text-gray-600 text-sm">Sem dados em tempo real para tomar decisões</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Solução Section -->
    <section id="solucao" class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">O que o MedFlow Finance faz por você</h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Foco <strong>exclusivo</strong> em faturamento e compliance. Nada mais, nada menos.
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
          <!-- Feature 1 -->
          <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-14 h-14 bg-primary-100 rounded-xl flex items-center justify-center mb-6">
              <span class="text-2xl">📤</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Upload Simples</h3>
            <p class="text-gray-600">Envie arquivos Excel, CSV ou XML do seu sistema. Sem configurações complicadas.</p>
          </div>

          <!-- Feature 2 -->
          <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-14 h-14 bg-success-100 rounded-xl flex items-center justify-center mb-6">
              <span class="text-2xl">✅</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Validação Automática</h3>
            <p class="text-gray-600">Detectamos erros, inconsistências e possíveis glosas antes de você enviar.</p>
          </div>

          <!-- Feature 3 -->
          <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-14 h-14 bg-warning-100 rounded-xl flex items-center justify-center mb-6">
              <span class="text-2xl">📊</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Dashboard Financeiro</h3>
            <p class="text-gray-600">Veja em tempo real: faturado, aprovado, em risco e potencial de recuperação.</p>
          </div>

          <!-- Feature 4 -->
          <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-shadow">
            <div class="w-14 h-14 bg-danger-100 rounded-xl flex items-center justify-center mb-6">
              <span class="text-2xl">📈</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Relatório de ROI</h3>
            <p class="text-gray-600">Relatórios executivos mostrando quanto você recuperou e economizou.</p>
          </div>
        </div>

        <div class="mt-12 bg-white rounded-2xl p-8 border-2 border-primary-200">
          <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
              <h3 class="text-2xl font-bold text-gray-900 mb-2">O que NÃO somos</h3>
              <p class="text-gray-600">Não somos prontuário eletrônico, telemedicina ou sistema clínico. <br/>Somos <strong>especialistas em faturamento</strong>.</p>
            </div>
            <div class="flex gap-4 text-gray-400">
              <span class="line-through">Prontuário</span>
              <span class="line-through">Telemedicina</span>
              <span class="line-through">App Paciente</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Como Funciona Section -->
    <section id="como-funciona" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Como funciona na prática</h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Em 3 passos simples você começa a recuperar dinheiro
          </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <div class="text-center">
            <div class="w-20 h-20 bg-primary-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6">1</div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Envie seu arquivo</h3>
            <p class="text-gray-600">Exporte os dados do seu sistema de faturamento (Excel, CSV ou XML) e faça upload na plataforma.</p>
          </div>

          <div class="text-center">
            <div class="w-20 h-20 bg-primary-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6">2</div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Análise automática</h3>
            <p class="text-gray-600">Enquanto você toma café, o sistema valida cada registro, detecta erros e identifica glosas.</p>
          </div>

          <div class="text-center">
            <div class="w-20 h-20 bg-primary-600 text-white rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-6">3</div>
            <h3 class="text-xl font-bold text-gray-900 mb-3">Revise e recupere</h3>
            <p class="text-gray-600">Veja exatamente o que precisa corrigir, quanto pode recuperar e gere relatórios executivos.</p>
          </div>
        </div>

        <div class="mt-16 bg-gray-900 rounded-2xl p-8 text-white">
          <div class="grid md:grid-cols-4 gap-8 text-center">
            <div>
              <div class="text-4xl font-bold text-primary-400 mb-2">R$ 150.000</div>
              <div class="text-gray-400">Total faturado (exemplo)</div>
            </div>
            <div>
              <div class="text-4xl font-bold text-success-400 mb-2">78%</div>
              <div class="text-gray-400">Taxa de aprovação</div>
            </div>
            <div>
              <div class="text-4xl font-bold text-warning-400 mb-2">R$ 22.500</div>
              <div class="text-gray-400">Valor em risco</div>
            </div>
            <div>
              <div class="text-4xl font-bold text-primary-400 mb-2">R$ 3.375</div>
              <div class="text-gray-400">Potencial recuperação</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold text-gray-900 mb-4">Investimento com retorno garantido</h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Comece com um piloto de 30 dias. Você vê o ROI antes de se comprometer.
          </p>
        </div>

        <!-- Piloto -->
        <div class="max-w-3xl mx-auto mb-12">
          <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-2xl p-8 text-white text-center">
            <div class="inline-flex items-center gap-2 bg-white/20 text-white px-4 py-2 rounded-full text-sm font-medium mb-4">
              🎯 Recomendado para começar
            </div>
            <h3 class="text-3xl font-bold mb-4">Piloto de 30 dias</h3>
            <p class="text-primary-100 mb-6 max-w-xl mx-auto">
              Você envia seus arquivos de faturamento, nós processamos e mostramos os resultados. Sem compromisso, sem risco.
            </p>
            <div class="flex items-baseline justify-center gap-2 mb-6">
              <span class="text-5xl font-bold">R$ 2.000</span>
              <span class="text-primary-200">- R$ 5.000</span>
            </div>
            <p class="text-primary-200 text-sm mb-8">Setup único + primeiro mês incluso</p>
            <router-link to="/register?plan=pilot" class="inline-flex items-center gap-2 bg-white text-primary-600 hover:bg-gray-100 px-8 py-4 rounded-xl font-bold text-lg transition-colors">
              Solicitar Piloto
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
            </router-link>
          </div>
        </div>

        <!-- Planos Mensais -->
        <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
          <!-- Plano Mensal -->
          <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
            <div class="text-center mb-8">
              <h3 class="text-lg font-semibold text-gray-500 mb-2">Mensalidade</h3>
              <div class="flex items-baseline justify-center gap-1">
                <span class="text-4xl font-bold text-gray-900">R$ 499</span>
                <span class="text-gray-500">- R$ 1.500</span>
              </div>
              <p class="text-sm text-gray-500 mt-2">por mês</p>
            </div>
            <ul class="space-y-4 mb-8">
              <li class="flex items-center gap-3">
                <svg class="w-5 h-5 text-success-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-600">Acesso completo à plataforma</span>
              </li>
              <li class="flex items-center gap-3">
                <svg class="w-5 h-5 text-success-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-600">Uploads ilimitados</span>
              </li>
              <li class="flex items-center gap-3">
                <svg class="w-5 h-5 text-success-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-600">Dashboard e relatórios</span>
              </li>
              <li class="flex items-center gap-3">
                <svg class="w-5 h-5 text-success-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-600">Suporte técnico</span>
              </li>
            </ul>
            <p class="text-xs text-gray-500 text-center">Valor varia conforme volume de faturamento</p>
          </div>

          <!-- Success Fee -->
          <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
            <div class="text-center mb-8">
              <h3 class="text-lg font-semibold text-gray-500 mb-2">Success Fee</h3>
              <div class="flex items-baseline justify-center gap-1">
                <span class="text-4xl font-bold text-gray-900">0,5%</span>
                <span class="text-gray-500">- 1%</span>
              </div>
              <p class="text-sm text-gray-500 mt-2">sobre faturamento recuperado</p>
            </div>
            <ul class="space-y-4 mb-8">
              <li class="flex items-center gap-3">
                <svg class="w-5 h-5 text-success-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-600">Você só paga pelo resultado</span>
              </li>
              <li class="flex items-center gap-3">
                <svg class="w-5 h-5 text-success-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-600">Alinhamento de interesses</span>
              </li>
              <li class="flex items-center gap-3">
                <svg class="w-5 h-5 text-success-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-600">Quanto mais recupera, mais ganha</span>
              </li>
              <li class="flex items-center gap-3">
                <svg class="w-5 h-5 text-success-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="text-gray-600">ROI sempre positivo</span>
              </li>
            </ul>
            <p class="text-xs text-gray-500 text-center">Cobrado apenas sobre valores efetivamente recuperados</p>
          </div>
        </div>

        <!-- Upsells Futuros -->
        <div class="mt-12 text-center">
          <p class="text-gray-500 mb-4">Recursos adicionais disponíveis em breve:</p>
          <div class="flex flex-wrap justify-center gap-4">
            <span class="bg-gray-100 text-gray-600 px-4 py-2 rounded-full text-sm">🤖 IA Preditiva</span>
            <span class="bg-gray-100 text-gray-600 px-4 py-2 rounded-full text-sm">📊 Benchmark de Mercado</span>
            <span class="bg-gray-100 text-gray-600 px-4 py-2 rounded-full text-sm">💼 Consultoria Financeira</span>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-primary-600 to-primary-800">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-4xl font-bold text-white mb-6">
          "Isso me fez recuperar dinheiro que eu perderia"
        </h2>
        <p class="text-xl text-primary-100 mb-4">
          Se uma clínica disser isso, o MedFlow Finance cumpriu seu papel.
        </p>
        <p class="text-primary-200 mb-10">
          Piloto de 30 dias. Você vê o ROI na primeira semana.
        </p>
        <router-link to="/register" class="inline-flex items-center gap-2 bg-white text-primary-600 hover:bg-gray-100 px-10 py-4 rounded-xl font-bold text-lg transition-all transform hover:scale-105 shadow-xl">
          Solicitar Piloto Agora
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
          </svg>
        </router-link>
      </div>
    </section>

    <!-- Público-Alvo Section -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">Para quem é o MedFlow Finance?</h2>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
          <div class="text-center p-6">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-3xl">🏥</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Clínicas Médicas</h3>
            <p class="text-gray-600">Pequenas e médias clínicas que querem reduzir perdas financeiras</p>
          </div>
          <div class="text-center p-6">
            <div class="w-16 h-16 bg-success-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-3xl">🔬</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Laboratórios</h3>
            <p class="text-gray-600">Laboratórios que processam alto volume de exames</p>
          </div>
          <div class="text-center p-6">
            <div class="w-16 h-16 bg-warning-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <span class="text-3xl">👁️</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Especialidades</h3>
            <p class="text-gray-600">Ortopedia, imagem, oftalmologia e outras especialidades</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 text-gray-400 py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-12">
          <div>
            <div class="flex items-center gap-2 mb-6">
              <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
              </div>
              <span class="text-xl font-bold text-white">MedFlow Finance</span>
            </div>
            <p class="text-sm">Automatização inteligente de faturamento médico para clínicas e hospitais.</p>
          </div>
          <div>
            <h4 class="text-white font-semibold mb-4">Produto</h4>
            <ul class="space-y-2 text-sm">
              <li><a href="#features" class="hover:text-white transition-colors">Recursos</a></li>
              <li><a href="#pricing" class="hover:text-white transition-colors">Preços</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Integrações</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Changelog</a></li>
            </ul>
          </div>
          <div>
            <h4 class="text-white font-semibold mb-4">Empresa</h4>
            <ul class="space-y-2 text-sm">
              <li><a href="#about" class="hover:text-white transition-colors">Sobre nós</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Carreiras</a></li>
              <li><a href="#contact" class="hover:text-white transition-colors">Contato</a></li>
            </ul>
          </div>
          <div>
            <h4 class="text-white font-semibold mb-4">Legal</h4>
            <ul class="space-y-2 text-sm">
              <li><a href="#" class="hover:text-white transition-colors">Termos de Uso</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Privacidade</a></li>
              <li><a href="#" class="hover:text-white transition-colors">LGPD</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Cookies</a></li>
            </ul>
          </div>
        </div>
        <div class="border-t border-gray-800 mt-12 pt-8 text-center text-sm">
          <p>© 2026 MedFlow Finance. Todos os direitos reservados.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
</script>

<style scoped>
@keyframes bounce-slow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-10px); }
}
.animate-bounce-slow {
  animation: bounce-slow 3s ease-in-out infinite;
}
</style>
