<template>
  <div class="article-card">
    <div class="article-card__meta">
      <div class="avatar" style="width:28px;height:28px;font-size:11px;">
        <img v-if="article.user.avatar_url" :src="article.user.avatar_url" :alt="article.user.name" />
        <template v-else>{{ article.user.avatar }}</template>
      </div>
      <span class="article-card__author">
        <Link :href="`/profile/${article.user.username}`">{{ article.user.name }}</Link>
      </span>
      <span class="article-card__dot">·</span>
      <span class="article-card__time">{{ timeAgo(article.created_at) }}</span>
      <span :class="`badge badge-${article.category}`">{{ categoryLabel(article.category) }}</span>
    </div>

    <h2 class="article-card__title">
      <Link :href="`/articles/${article.slug}`">{{ article.title }}</Link>
    </h2>

    <p class="article-card__excerpt">{{ article.excerpt }}</p>

    <div class="article-card__footer">
      <span class="article-card__stat">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/>
          <path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
        </svg>
        {{ article.votes_count }}
      </span>
      <span class="article-card__stat">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        {{ article.comments_count ?? 0 }}
      </span>
      <span class="article-card__stat">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
          <circle cx="12" cy="12" r="3"/>
        </svg>
        {{ article.views_count }}
      </span>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  article: Object,
})

const categoryLabels = {
  proxy:    'Прокси',
  vpn:      'VPN',
  security: 'Безопасность',
  tools:    'Инструменты',
  other:    'Другое',
}

function categoryLabel(cat) {
  return categoryLabels[cat] || cat
}

function timeAgo(dateStr) {
  const now = new Date()
  const date = new Date(dateStr)
  const diff = Math.floor((now - date) / 1000)

  if (diff < 60)       return 'только что'
  if (diff < 3600)     return `${Math.floor(diff / 60)} мин. назад`
  if (diff < 86400)    return `${Math.floor(diff / 3600)} ч. назад`
  if (diff < 2592000)  return `${Math.floor(diff / 86400)} дн. назад`
  if (diff < 31536000) return `${Math.floor(diff / 2592000)} мес. назад`
  return `${Math.floor(diff / 31536000)} г. назад`
}
</script>
