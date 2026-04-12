<template>
  <AppLayout>
    <div class="page-wrap" style="padding-top:20px;">

      <!-- Основная колонка: статьи -->
      <div>
        <!-- Шапка с именем (мобильная) -->
        <div class="profile-mobile-hero content-card" style="display:none;">
          <div class="profile-hero">
            <div class="avatar avatar--64">
              <img v-if="profileUser.avatar_url" :src="profileUser.avatar_url" :alt="profileUser.name" />
              <template v-else>{{ profileUser.avatar }}</template>
            </div>
            <div class="profile-hero__info">
              <div class="profile-hero__name">{{ profileUser.name }}</div>
              <div class="profile-hero__username">@{{ profileUser.username }}</div>
            </div>
          </div>
        </div>

        <!-- Таб-бар статей -->
        <div class="content-card">
          <div class="tab-bar">
            <button class="tab-btn tab-btn--active">
              Публикации
              <span v-if="articles.total" style="margin-left:5px;font-size:12px;color:var(--text-muted);font-weight:400;">{{ articles.total }}</span>
            </button>
          </div>

          <div v-if="articles.data.length">
            <ArticleCard v-for="a in articles.data" :key="a.id" :article="a" />
          </div>
          <div v-else class="empty">
            <div class="empty__icon">📝</div>
            <div class="empty__title">Публикаций пока нет</div>
            <div v-if="isOwnProfile" class="empty__text">
              Напишите первую статью — она появится здесь
            </div>
            <div v-else class="empty__text">
              {{ profileUser.name }} ещё ничего не опубликовал
            </div>
            <div v-if="isOwnProfile" style="margin-top:16px;">
              <Link href="/articles/create" class="btn btn-primary">Написать статью</Link>
            </div>
          </div>

          <div v-if="articles.last_page > 1" class="pagination">
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
      </div>

      <!-- Сайдбар: карточка пользователя -->
      <aside class="sidebar sidebar-sticky">
        <div class="sidebar-card">
          <!-- Аватар + имя -->
          <div style="text-align:center;padding-bottom:16px;border-bottom:1px solid var(--border);margin-bottom:16px;">
            <div class="avatar avatar--80" style="margin:0 auto 12px;">
              <img v-if="profileUser.avatar_url" :src="profileUser.avatar_url" :alt="profileUser.name" />
              <template v-else>{{ profileUser.avatar }}</template>
            </div>
            <div style="font-size:17px;font-weight:700;margin-bottom:3px;">{{ profileUser.name }}</div>
            <div style="font-size:13px;color:var(--text-muted);">@{{ profileUser.username }}</div>
            <div v-if="profileUser.role !== 'user'" style="margin-top:6px;">
              <span class="role-badge" :class="`role-badge--${profileUser.role}`">
                {{ roleLabel(profileUser.role) }}
              </span>
            </div>
          </div>

          <!-- Bio -->
          <div v-if="profileUser.bio" style="font-size:14px;color:var(--text-muted);line-height:1.55;margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid var(--border);">
            {{ profileUser.bio }}
          </div>

          <!-- Статистика -->
          <div class="profile-stats-grid">
            <div class="profile-stat-item">
              <div class="profile-stat-item__val">{{ articles.total }}</div>
              <div class="profile-stat-item__label">статей</div>
            </div>
            <div class="profile-stat-item">
              <div class="profile-stat-item__val">{{ formatVotes(totalVotes) }}</div>
              <div class="profile-stat-item__label">голосов</div>
            </div>
            <div class="profile-stat-item">
              <div class="profile-stat-item__val">{{ totalComments }}</div>
              <div class="profile-stat-item__label">комментариев</div>
            </div>
            <div class="profile-stat-item">
              <div class="profile-stat-item__val">{{ formatViews(totalViews) }}</div>
              <div class="profile-stat-item__label">просмотров</div>
            </div>
          </div>

          <!-- Кнопки действий -->
          <div style="margin-top:16px;display:flex;flex-direction:column;gap:8px;">
            <template v-if="isOwnProfile">
              <Link href="/settings" class="btn btn-outline" style="width:100%;justify-content:center;">
                Редактировать профиль
              </Link>
              <Link href="/articles/create" class="btn btn-primary" style="width:100%;justify-content:center;">
                Написать статью
              </Link>
            </template>
          </div>
        </div>

        <!-- Дата регистрации -->
        <div class="sidebar-card" style="text-align:center;font-size:13px;color:var(--text-muted);">
          На платформе с {{ joinDate }}
        </div>
      </aside>
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

function formatVotes(n) {
  return n > 0 ? `+${n}` : `${n}`
}

function formatViews(n) {
  if (n >= 1000) return `${(n / 1000).toFixed(1)}K`
  return `${n}`
}
</script>

<style scoped>
.profile-stats-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1px;
  background: var(--border);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
}

.profile-stat-item {
  background: var(--bg);
  padding: 12px;
  text-align: center;
}

.profile-stat-item__val {
  font-size: 18px;
  font-weight: 700;
  letter-spacing: -0.5px;
}

.profile-stat-item__label {
  font-size: 11px;
  color: var(--text-muted);
  margin-top: 2px;
}

.role-badge {
  display: inline-block;
  padding: 2px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
}
.role-badge--admin     { background: #fff3cd; color: #856404; }
.role-badge--moderator { background: #cce5ff; color: #004085; }

@media (max-width: 800px) {
  .profile-mobile-hero { display: block !important; margin-bottom: 12px; }
}
</style>
