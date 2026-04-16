<template>
  <AppLayout>

    <!-- Cover -->
    <div class="profile-cover" :class="{ 'profile-cover--img': profileUser.cover_url }">
      <div
        v-if="profileUser.cover_url"
        class="profile-cover__bg"
        :style="`background-image:url('${profileUser.cover_url}')`"
      ></div>
    </div>

    <!-- Header -->
    <div class="profile-header">

      <!-- Top row: avatar + action buttons -->
      <div class="profile-header__top-row">
        <div class="profile-avatar">
          <div class="avatar avatar--80" style="border:4px solid var(--bg);box-shadow:0 2px 16px rgba(0,0,0,0.18);">
            <img v-if="profileUser.avatar_url" :src="profileUser.avatar_url" :alt="profileUser.name" />
            <template v-else>{{ profileUser.avatar }}</template>
          </div>
        </div>
        <div v-if="isOwnProfile" class="profile-header__actions">
          <Link href="/articles/create" class="btn btn-primary btn-sm">Написать статью</Link>
          <Link href="/settings" class="btn btn-edit btn-sm">Редактировать профиль</Link>
        </div>
      </div>

      <!-- Info block -->
      <div class="profile-info">
        <div class="profile-info__name-row">
          <h1 class="profile-info__name">{{ profileUser.name }}</h1>
          <span
            v-if="profileUser.role !== 'user'"
            class="profile-role-badge"
            :class="`profile-role-badge--${profileUser.role}`"
          >{{ roleLabel(profileUser.role) }}</span>
        </div>

        <div class="profile-info__username">@{{ profileUser.username }}</div>
        <div class="profile-info__joined">На сайте с {{ joinDate }}</div>
        <div v-if="profileUser.bio" class="profile-info__bio">{{ profileUser.bio }}</div>

        <div class="profile-info__stats">
          <span class="profile-stat">
            <strong>{{ articles.total }}</strong> статей
          </span>
          <span class="profile-stat__dot">·</span>
          <span class="profile-stat" :class="{ 'stat--pos': totalVotes > 0, 'stat--neg': totalVotes < 0 }">
            <strong>{{ totalVotes > 0 ? '+' : '' }}{{ totalVotes }}</strong> голосов
          </span>
          <span class="profile-stat__dot">·</span>
          <span class="profile-stat">
            <strong>{{ formatViews(totalViews) }}</strong> просмотров
          </span>
          <span class="profile-stat__dot">·</span>
          <span class="profile-stat">
            <strong>{{ totalComments }}</strong> комментариев
          </span>
        </div>
      </div>

    </div>

    <!-- Articles -->
    <div class="profile-content">
      <div v-if="articles.data.length" class="profile-articles">
        <ArticleCard v-for="a in articles.data" :key="a.id" :article="a" />

        <div v-if="articles.last_page > 1" class="pagination" style="padding:8px 0 0;">
          <button
            v-for="link in articles.links" :key="link.label"
            class="page-btn"
            :class="{ 'page-btn--active': link.active }"
            :disabled="!link.url"
            @click="link.url && router.get(link.url)"
            v-html="link.label"
          />
        </div>
      </div>

      <div v-else class="profile-empty">
        <div class="profile-empty__icon">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.35">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
          </svg>
        </div>
        <div class="profile-empty__title">Публикаций пока нет</div>
        <div class="profile-empty__text">
          {{ isOwnProfile ? 'Напишите первую статью — она появится здесь' : `${profileUser.name} ещё ничего не опубликовал` }}
        </div>
        <Link v-if="isOwnProfile" href="/articles/create" class="btn btn-primary" style="margin-top:16px;">Написать статью</Link>
      </div>
    </div>

  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ArticleCard from '@/Components/ArticleCard.vue'

const props = defineProps({
  profileUser:   Object,
  articles:      Object,
  totalVotes:    Number,
  totalViews:    Number,
  totalComments: Number,
})

const page = usePage()

const isOwnProfile = computed(() =>
  page.props.auth?.user?.id === props.profileUser.id
)

const joinDate = computed(() =>
  new Date(props.profileUser.created_at).toLocaleDateString('ru-RU', {
    month: 'long', year: 'numeric',
  })
)

function roleLabel(role) {
  return { moderator: 'Модератор', admin: 'Администратор' }[role] || role
}

function formatViews(n) {
  if (n >= 1000) return `${(n / 1000).toFixed(1)}K`
  return `${n}`
}
</script>

<style scoped>
/* ── Cover ──────────────────────────────────── */
.profile-cover {
  position: relative;
  height: 200px;
  background: linear-gradient(135deg, var(--accent-light) 0%, var(--bg-secondary) 100%);
  overflow: hidden;
  border-radius: var(--radius-lg);
  margin-bottom: 0;
}

.profile-cover--img { background: var(--bg-secondary); }

.profile-cover__bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
}

/* ── Header ─────────────────────────────────── */
.profile-header {
  background: var(--bg);
  border-bottom: 1px solid var(--border);
  padding: 0 16px 20px;
}

/* Top row: avatar + buttons */
.profile-header__top-row {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  min-height: 40px; /* кнопки выравниваются по нижнему краю аватара */
  margin-bottom: 12px;
}

.profile-avatar {
  position: relative;
  z-index: 1;
  margin-top: -40px;
}

.profile-header__actions {
  display: flex;
  gap: 8px;
  padding-bottom: 2px;
}

/* ── Info ───────────────────────────────────── */
.profile-info__name-row {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 4px;
}

.profile-info__name {
  font-size: 20px;
  font-weight: 800;
  letter-spacing: -0.5px;
  color: var(--text);
  line-height: 1.2;
}

.profile-info__username {
  font-size: 14px;
  color: var(--text-muted);
  margin-bottom: 2px;
}

.profile-info__joined {
  font-size: 12px;
  color: var(--text-muted);
  opacity: 0.75;
  margin-bottom: 6px;
}

.profile-info__bio {
  font-size: 14px;
  color: var(--text-muted);
  line-height: 1.55;
  margin-bottom: 10px;
  max-width: 520px;
}

/* ── Stats ──────────────────────────────────── */
.profile-info__stats {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 10px;
}

.profile-stat {
  font-size: 13px;
  color: var(--text-muted);
}

.profile-stat strong {
  color: var(--text);
  font-weight: 700;
}

.profile-stat__dot {
  color: var(--border-strong);
  opacity: 0.5;
}

.stat--pos strong { color: #16a34a; }
.stat--neg strong { color: #dc2626; }

/* ── Edit button ────────────────────────────── */
.btn-edit {
  background: var(--bg-secondary);
  color: var(--text);
  border: 1px solid var(--text-muted);
}
.btn-edit:hover { background: var(--bg-hover); }

/* ── Role badges ────────────────────────────── */
.profile-role-badge {
  font-size: 11px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 20px;
  flex-shrink: 0;
}
.profile-role-badge--admin     { background: #fef3c7; color: #92400e; }
.profile-role-badge--moderator { background: #dbeafe; color: #1e40af; }

/* ── Content ────────────────────────────────── */
.profile-content {
  padding: 20px 0 48px;
}

.profile-articles {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

/* ── Empty ──────────────────────────────────── */
.profile-empty {
  text-align: center;
  padding: 60px 20px;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  display: flex;
  flex-direction: column;
  align-items: center;
}
.profile-empty__icon  { margin-bottom: 16px; color: var(--text-muted); }
.profile-empty__title { font-size: 17px; font-weight: 700; color: var(--text); margin-bottom: 8px; }
.profile-empty__text  { font-size: 14px; color: var(--text-muted); }

/* ── Mobile ─────────────────────────────────── */
@media (max-width: 600px) {
  .profile-cover { height: 130px; border-radius: 8px; }
  .profile-avatar { margin-top: -32px; }
  .profile-header__top-row { flex-wrap: wrap; gap: 10px; }
  .profile-header__actions { width: 100%; justify-content: flex-end; }
  .profile-info__name { font-size: 18px; }
  .profile-info__stats { gap: 6px; }
}
</style>
