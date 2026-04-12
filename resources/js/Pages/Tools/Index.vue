<template>
  <AppLayout>
    <div class="tools-page">
      <!-- Hero -->
      <div class="tools-hero">
        <h1 class="tools-hero__title">Наши инструменты</h1>
        <p class="tools-hero__sub">Прокси, VPN и другие решения для приватного и безопасного интернета</p>
      </div>

      <!-- Products grid -->
      <div class="tools-grid">

        <!-- Proxy -->
        <div class="tool-card">
          <div class="tool-card__badge">Прокси</div>
          <div class="tool-card__icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <circle cx="12" cy="12" r="10"/>
              <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
            </svg>
          </div>
          <h2 class="tool-card__title">Прокси-серверы</h2>
          <p class="tool-card__desc">
            Резидентные и дата-центровые прокси для работы, парсинга и анонимности.
            Высокая скорость, стабильность и тысячи IP-адресов по всему миру.
          </p>
          <ul class="tool-card__features">
            <li>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Резидентные IP из 50+ стран
            </li>
            <li>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              HTTP / SOCKS5
            </li>
            <li>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Ротация по запросу или по времени
            </li>
            <li>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              API для интеграции
            </li>
          </ul>
          <a href="https://t.me/noctoproxy" target="_blank" rel="noopener" class="tool-card__btn btn btn-primary">
            Узнать больше
          </a>
        </div>

        <!-- VPN -->
        <div class="tool-card">
          <div class="tool-card__badge tool-card__badge--vpn">VPN</div>
          <div class="tool-card__icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
          </div>
          <h2 class="tool-card__title">VPN</h2>
          <p class="tool-card__desc">
            Надёжный VPN для защиты трафика и обхода блокировок.
            Работает на всех устройствах, без логов, с максимальной скоростью.
          </p>
          <ul class="tool-card__features">
            <li>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Без логов (no-log policy)
            </li>
            <li>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              WireGuard / OpenVPN
            </li>
            <li>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Windows, macOS, iOS, Android
            </li>
            <li>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              Серверы в 20+ странах
            </li>
          </ul>
          <a href="https://t.me/noctoproxy" target="_blank" rel="noopener" class="tool-card__btn btn btn-primary">
            Узнать больше
          </a>
        </div>

      </div>

      <!-- CTA Banner -->
      <div class="tools-cta">
        <div class="tools-cta__inner">
          <div>
            <div class="tools-cta__title">Нужна помощь с выбором?</div>
            <div class="tools-cta__sub">Напишите нам в Telegram — поможем подобрать под вашу задачу</div>
          </div>
          <a href="https://t.me/noctoproxy" target="_blank" rel="noopener" class="btn btn-tg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.04 9.607c-.148.658-.537.818-1.084.508l-3-2.21-1.447 1.393c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.48 14.607l-2.95-.924c-.641-.202-.654-.641.136-.951l11.52-4.442c.534-.194 1.001.13.376.958z"/>
            </svg>
            Написать в Telegram
          </a>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
</script>

<style scoped>
.tools-page {
  padding: 32px 0 60px;
}

.tools-hero {
  text-align: center;
  margin-bottom: 48px;
}

.tools-hero__title {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text);
  margin: 0 0 10px;
}

.tools-hero__sub {
  color: var(--text-muted);
  font-size: 1rem;
  margin: 0;
}

.tools-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 24px;
  margin-bottom: 40px;
}

.tool-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 32px;
  display: flex;
  flex-direction: column;
  gap: 16px;
  transition: box-shadow 0.2s, transform 0.2s;
}

.tool-card:hover {
  box-shadow: 0 8px 32px rgba(0,0,0,0.1);
  transform: translateY(-2px);
}

.tool-card__badge {
  display: inline-flex;
  align-items: center;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 0.72rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  background: rgba(99, 102, 241, 0.12);
  color: #6366f1;
  width: fit-content;
}

.tool-card__badge--vpn {
  background: rgba(16, 185, 129, 0.12);
  color: #10b981;
}

.tool-card__icon {
  color: var(--text-muted);
}

.tool-card__title {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text);
  margin: 0;
}

.tool-card__desc {
  color: var(--text-muted);
  font-size: 0.9rem;
  line-height: 1.6;
  margin: 0;
}

.tool-card__features {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.tool-card__features li {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.875rem;
  color: var(--text-light);
}

.tool-card__features svg {
  color: #6366f1;
  flex-shrink: 0;
}

.tool-card__btn {
  margin-top: auto;
  text-align: center;
  justify-content: center;
}

/* CTA Banner */
.tools-cta {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
  border-radius: 16px;
  padding: 40px;
  margin-top: 16px;
}

.tools-cta__inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  flex-wrap: wrap;
}

.tools-cta__title {
  font-size: 1.2rem;
  font-weight: 700;
  color: #fff;
  margin-bottom: 4px;
}

.tools-cta__sub {
  color: rgba(255,255,255,0.6);
  font-size: 0.875rem;
}

@media (max-width: 640px) {
  .tools-hero__title { font-size: 1.5rem; }
  .tool-card { padding: 24px; }
  .tools-cta { padding: 24px; }
  .tools-cta__inner { flex-direction: column; align-items: flex-start; }
}
</style>
