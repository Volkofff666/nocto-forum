<template>
  <article class="article-card" :class="{ 'article-card--pinned': article.is_pinned }">

    <!-- Pin badge -->
    <div v-if="article.is_pinned" class="article-card__pin-badge">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><path d="M16 2v2l-1 1v5l2 2v2h-5v6l-1 1-1-1v-6H5v-2l2-2V5l-1-1V2h10z"/></svg>
      Закреплено
    </div>

    <!-- Author row -->
    <div class="article-card__top">
      <div class="avatar avatar--32" :style="avatarStyle(article.user)">
        <img v-if="article.user.avatar_url" :src="article.user.avatar_url" :alt="article.user.name" />
        <template v-else>{{ article.user.avatar }}</template>
      </div>
      <div class="article-card__top-info">
        <Link :href="`/profile/${article.user.username}`" class="article-card__author">{{ article.user.name }}</Link>
        <span class="article-card__time">{{ timeAgo(article.created_at) }}</span>
      </div>
      <span :class="`badge badge-${article.category}`">{{ catLabel(article.category) }}</span>
    </div>

    <!-- Title -->
    <h2 class="article-card__title">
      <Link :href="`/articles/${article.slug}`">{{ article.title }}</Link>
    </h2>

    <!-- Cover image (full width) — shown before excerpt -->
    <Link v-if="article.cover_url" :href="`/articles/${article.slug}`" class="article-card__cover-wrap">
      <img
        :src="article.cover_url"
        :alt="article.title"
        class="article-card__cover"
        loading="lazy"
        @error="e => e.target.closest('.article-card__cover-wrap').style.display='none'"
      />
    </Link>

    <!-- Excerpt — only show if no cover image -->
    <p v-if="!article.cover_url && article.excerpt" class="article-card__excerpt">{{ article.excerpt }}</p>

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

    <!-- Footer: reactions row -->
    <div class="article-card__footer">
      <!-- Vote score as reaction-style pill -->
      <span
        class="article-card__vote-pill"
        :class="{
          'article-card__vote-pill--pos': article.votes_count > 0,
          'article-card__vote-pill--neg': article.votes_count < 0,
        }"
      >
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/><path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
        {{ article.votes_count > 0 ? '+' + article.votes_count : article.votes_count }}
      </span>

      <!-- Comments -->
      <Link :href="`/articles/${article.slug}#comments`" class="stat-btn" title="Комментарии">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        {{ article.comments_count ?? 0 }}
      </Link>

      <!-- Read time -->
      <span class="article-card__readtime">{{ readTime(article.body) }} мин</span>

      <span class="article-card__spacer"></span>

      <BookmarkButton :articleId="article.id" :isBookmarked="isBookmarked" />
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

// Avatar background palette (matches ITHub avatar color system)
const AVATAR_COLORS = ['#3b82f6', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444']
function avatarStyle(user) {
  if (user.avatar_url) return {}
  const idx = (user.id ?? 0) % AVATAR_COLORS.length
  return { background: AVATAR_COLORS[idx] }
}

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

<style scoped>
.article-card__top {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
}

.article-card__top-info {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 0;
}

/* Vote pill — reaction style */
.article-card__vote-pill {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 5px 10px;
  border-radius: var(--radius-pill);
  border: 1px solid var(--border);
  background: transparent;
  font-size: 13px;
  font-weight: 500;
  color: var(--text-secondary);
  font-family: var(--font-ui);
  transition: border-color var(--transition-base), background var(--transition-base);
}
.article-card__vote-pill:hover {
  border-color: var(--accent);
  background: var(--accent-light);
  color: var(--accent);
}
.article-card__vote-pill--pos { color: var(--accent); }
.article-card__vote-pill--neg { color: var(--color-red-500); }

/* Read time label */
.article-card__readtime {
  font-size: 12px;
  color: var(--text-muted);
  font-family: var(--font-ui);
  margin-left: 4px;
}
</style>
