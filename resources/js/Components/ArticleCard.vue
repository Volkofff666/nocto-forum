<template>
  <article class="article-card" :class="{ 'article-card--pinned': article.is_pinned }">
    <!-- Pinned indicator -->
    <div v-if="article.is_pinned" class="article-card__pin-badge">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
        <path d="M16 2v2l-1 1v5l2 2v2h-5v6l-1 1-1-1v-6H5v-2l2-2V5l-1-1V2h10z"/>
      </svg>
      Закреплено
    </div>

    <!-- Cover image -->
    <Link v-if="article.cover_url" :href="`/articles/${article.slug}`" class="article-card__cover-wrap">
      <img
        :src="article.cover_url"
        :alt="article.title"
        class="article-card__cover"
        loading="lazy"
        @error="e => e.target.parentElement.style.display='none'"
      />
    </Link>

    <div class="article-card__body">
      <!-- Meta -->
      <div class="article-card__meta">
        <div class="article-card__author-wrap">
          <div class="avatar avatar--24">
            <img v-if="article.user.avatar_url" :src="article.user.avatar_url" :alt="article.user.name" />
            <template v-else>{{ article.user.avatar }}</template>
          </div>
          <Link :href="`/profile/${article.user.username}`" class="article-card__author">{{ article.user.name }}</Link>
        </div>
        <span class="article-card__sep">·</span>
        <span class="article-card__time">{{ timeAgo(article.created_at) }}</span>
        <span class="article-card__sep">·</span>
        <span class="article-card__time">{{ readTime(article.body) }} мин.</span>
        <span :class="`badge badge-${article.category}`">{{ catLabel(article.category) }}</span>
      </div>

      <!-- Title -->
      <h2 class="article-card__title">
        <Link :href="`/articles/${article.slug}`">{{ article.title }}</Link>
      </h2>

      <!-- Excerpt -->
      <p class="article-card__excerpt">{{ article.excerpt }}</p>

      <!-- Tags -->
      <div v-if="article.tags && article.tags.length" class="article-card__tags">
        <a
          v-for="tag in article.tags"
          :key="tag"
          :href="`/?tag=${encodeURIComponent(tag)}`"
          class="article-tag"
          @click.prevent="router.get('/', { tag })"
        >#{{ tag }}</a>
      </div>

      <!-- Footer -->
      <div class="article-card__footer">
        <button
          class="card-vote-btn"
          :class="{
            'card-vote-btn--pos': article.votes_count > 0,
            'card-vote-btn--neg': article.votes_count < 0,
          }"
          title="Рейтинг"
        >
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="18 15 12 9 6 15"/>
          </svg>
          {{ article.votes_count > 0 ? '+' : '' }}{{ article.votes_count }}
        </button>

        <Link :href="`/articles/${article.slug}#comments`" class="stat-btn" title="Комментарии">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
          {{ article.comments_count ?? 0 }}
        </Link>

        <span class="stat-btn" title="Просмотры">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
          {{ article.views_count }}
        </span>

        <span class="article-card__spacer"></span>

        <BookmarkButton :articleId="article.id" :isBookmarked="isBookmarked" />
      </div>
    </div>
  </article>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import BookmarkButton from '@/Components/BookmarkButton.vue'
import { useCategoryLabel } from '@/composables/useCategories'

const props = defineProps({
  article:      Object,
  isBookmarked: { type: Boolean, default: false },
})

function catLabel(c) { return useCategoryLabel(c) }

function readTime(body) {
  const text = body?.trimStart().startsWith('<') ? body.replace(/<[^>]+>/g, ' ') : (body || '')
  return Math.max(1, Math.ceil(text.trim().split(/\s+/).length / 200))
}

function timeAgo(d) {
  const s = Math.floor((Date.now() - new Date(d)) / 1000)
  if (s < 60)       return 'только что'
  if (s < 3600)     return `${Math.floor(s/60)} мин. назад`
  if (s < 86400)    return `${Math.floor(s/3600)} ч. назад`
  if (s < 2592000)  return `${Math.floor(s/86400)} дн. назад`
  if (s < 31536000) return `${Math.floor(s/2592000)} мес. назад`
  return `${Math.floor(s/31536000)} г. назад`
}
</script>
