<template>
  <AppLayout>
    <div class="page-wrap page-wrap--full" style="padding-top:20px;">

      <!-- Search bar -->
      <div class="search-page-bar">
        <div class="search-page-bar__inner">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:var(--text-muted)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          <input
            ref="inputEl"
            v-model="localQ"
            type="search"
            class="search-page-bar__input"
            placeholder="Поиск статей и пользователей..."
            @keydown.enter="submit"
          />
          <button class="btn btn-primary btn-sm" @click="submit">Найти</button>
        </div>
      </div>

      <!-- No query -->
      <div v-if="!q" class="search-empty">
        <div class="empty__icon">🔍</div>
        <div class="empty__title">Введите запрос</div>
        <div class="empty__text">Ищем по заголовкам статей, описаниям и именам пользователей</div>
      </div>

      <!-- No results -->
      <div v-else-if="total === 0" class="search-empty">
        <div class="empty__icon">😶</div>
        <div class="empty__title">Ничего не найдено</div>
        <div class="empty__text">По запросу <strong>«{{ q }}»</strong> нет результатов — попробуйте другие слова</div>
      </div>

      <!-- Results -->
      <template v-else>
        <div class="search-meta">
          Найдено <strong>{{ total }}</strong> результат{{ plural(total) }} по запросу «{{ q }}»
        </div>

        <!-- Users -->
        <div v-if="users.length" class="content-card" style="margin-bottom:16px;">
          <div class="search-section-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Пользователи
          </div>
          <div class="search-users">
            <Link
              v-for="u in users" :key="u.id"
              :href="`/profile/${u.username}`"
              class="search-user-card"
            >
              <div class="avatar avatar--48">
                <img v-if="u.avatar_url" :src="u.avatar_url" :alt="u.name" />
                <template v-else>{{ initials(u.name) }}</template>
              </div>
              <div class="search-user-card__info">
                <div class="search-user-card__name">
                  {{ u.name }}
                  <span v-if="u.role !== 'user'" class="role-badge" :class="`role-badge--${u.role}`">{{ roleLabel(u.role) }}</span>
                </div>
                <div class="search-user-card__username">@{{ u.username }}</div>
                <div v-if="u.bio" class="search-user-card__bio">{{ u.bio }}</div>
              </div>
              <div class="search-user-card__stat">
                <strong>{{ u.articles_count }}</strong>
                <span>статей</span>
              </div>
            </Link>
          </div>
        </div>

        <!-- Articles -->
        <div v-if="articles.length" class="content-card">
          <div class="search-section-title">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Статьи
          </div>
          <div v-for="a in articles" :key="a.id" class="search-article">
            <div class="search-article__meta">
              <div class="avatar avatar--24">
                <img v-if="a.user?.avatar_url" :src="a.user.avatar_url" :alt="a.user.name" />
                <template v-else>{{ initials(a.user?.name || '?') }}</template>
              </div>
              <Link :href="`/profile/${a.user?.username}`" class="search-article__author">{{ a.user?.name }}</Link>
              <span style="color:var(--border-strong)">·</span>
              <span :class="`badge badge-${a.category}`">{{ catLabel(a.category) }}</span>
              <span style="color:var(--text-muted);font-size:13px;">{{ timeAgo(a.created_at) }}</span>
            </div>

            <h3 class="search-article__title">
              <Link :href="`/articles/${a.slug}`" v-html="highlight(a.title, q)"></Link>
            </h3>

            <p class="search-article__excerpt" v-html="highlight(a.excerpt, q)"></p>

            <div class="search-article__footer">
              <span class="stat-btn">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/></svg>
                {{ a.votes_count > 0 ? '+' : '' }}{{ a.votes_count }}
              </span>
              <span class="stat-btn">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                {{ a.comments_count }}
              </span>
              <span class="stat-btn">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                {{ a.views_count }}
              </span>
            </div>
          </div>
        </div>
      </template>

    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  q:        { type: String, default: '' },
  articles: { type: Array,  default: () => [] },
  users:    { type: Array,  default: () => [] },
  total:    { type: Number, default: 0 },
})

const localQ  = ref(props.q)
const inputEl = ref(null)

onMounted(() => { if (!props.q) inputEl.value?.focus() })

function submit() {
  const q = localQ.value.trim()
  if (!q) return
  router.get('/search', { q }, { preserveState: false })
}

// Highlight matching text
function highlight(text, q) {
  if (!q || !text) return text
  const escaped = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
  return text.replace(new RegExp(`(${escaped})`, 'gi'), '<mark>$1</mark>')
}

function plural(n) {
  if (n % 10 === 1 && n % 100 !== 11) return ''
  if (n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20)) return 'а'
  return 'ов'
}

function initials(name) {
  return (name || '?').split(' ').slice(0, 2).map(w => w[0]?.toUpperCase()).join('')
}

function roleLabel(r) {
  return { moderator: 'Модератор', admin: 'Администратор' }[r] || r
}

const cats = { proxy: 'Прокси', vpn: 'VPN', security: 'Безопасность', tools: 'Инструменты', other: 'Другое' }
function catLabel(c) { return cats[c] || c }

function timeAgo(d) {
  const s = Math.floor((Date.now() - new Date(d)) / 1000)
  if (s < 3600)    return `${Math.floor(s/60)} мин. назад`
  if (s < 86400)   return `${Math.floor(s/3600)} ч. назад`
  if (s < 2592000) return `${Math.floor(s/86400)} дн. назад`
  return `${Math.floor(s/2592000)} мес. назад`
}
</script>
