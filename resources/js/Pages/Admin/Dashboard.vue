<template>
  <AdminLayout title="Обзор">
    <!-- Stats grid -->
    <div class="admin-stats">
      <div class="admin-stat-card">
        <div class="admin-stat-card__val">{{ stats.users }}</div>
        <div class="admin-stat-card__label">Пользователей</div>
        <div class="admin-stat-card__sub">+{{ stats.users_today }} сегодня</div>
      </div>
      <div class="admin-stat-card" :class="{ 'admin-stat-card--warn': stats.banned > 0 }">
        <div class="admin-stat-card__val">{{ stats.banned }}</div>
        <div class="admin-stat-card__label">Заблокировано</div>
      </div>
      <div class="admin-stat-card">
        <div class="admin-stat-card__val">{{ stats.published }}</div>
        <div class="admin-stat-card__label">Опубликовано</div>
        <div class="admin-stat-card__sub">+{{ stats.articles_today }} сегодня</div>
      </div>
      <div class="admin-stat-card admin-stat-card--warn">
        <div class="admin-stat-card__val">{{ stats.drafts }}</div>
        <div class="admin-stat-card__label">Черновиков</div>
      </div>
      <div class="admin-stat-card">
        <div class="admin-stat-card__val">{{ stats.comments }}</div>
        <div class="admin-stat-card__label">Комментариев</div>
      </div>
      <div class="admin-stat-card">
        <div class="admin-stat-card__val">{{ stats.votes }}</div>
        <div class="admin-stat-card__label">Голосов</div>
      </div>
      <div class="admin-stat-card" :class="{ 'admin-stat-card--danger': stats.reports > 0 }">
        <div class="admin-stat-card__val">{{ stats.reports }}</div>
        <div class="admin-stat-card__label">Жалоб</div>
        <Link v-if="stats.reports > 0" href="/admin/reports" class="admin-stat-card__sub admin-stat-card__sub--link">Рассмотреть →</Link>
      </div>
    </div>

    <!-- Charts -->
    <div class="admin-grid-2" style="margin-bottom:24px;">
      <div class="admin-card">
        <div class="admin-card__header">Регистрации за 7 дней</div>
        <Sparkline :data="userChart" color="#3b82f6" />
      </div>
      <div class="admin-card">
        <div class="admin-card__header">Публикации за 7 дней</div>
        <Sparkline :data="articleChart" color="#10b981" />
      </div>
    </div>

    <div class="admin-grid-2">
      <!-- Recent users -->
      <div class="admin-card">
        <div class="admin-card__header">
          Последние пользователи
          <Link href="/admin/users" class="admin-card__link">Все →</Link>
        </div>
        <table class="admin-table">
          <thead>
            <tr><th>Пользователь</th><th>Роль</th><th>Дата</th></tr>
          </thead>
          <tbody>
            <tr v-for="u in recentUsers" :key="u.id">
              <td>
                <Link :href="`/admin/users/${u.id}`" style="font-weight:500;">{{ u.name }}</Link>
                <div style="font-size:12px;color:var(--text-muted);">@{{ u.username }}</div>
                <span v-if="u.banned_at" class="role-pill role-pill--banned">Забанен</span>
              </td>
              <td><span class="role-pill" :class="`role-pill--${u.role}`">{{ roleLabel(u.role) }}</span></td>
              <td style="color:var(--text-muted);font-size:13px;">{{ fmtDate(u.created_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Recent articles -->
      <div class="admin-card">
        <div class="admin-card__header">
          Последние статьи
          <Link href="/admin/articles" class="admin-card__link">Все →</Link>
        </div>
        <table class="admin-table">
          <thead>
            <tr><th>Статья</th><th>Статус</th><th>Дата</th></tr>
          </thead>
          <tbody>
            <tr v-for="a in recentArticles" :key="a.id">
              <td>
                <Link :href="`/articles/${a.slug}`" class="admin-table__link">{{ a.title }}</Link>
                <div style="font-size:12px;color:var(--text-muted);">{{ a.user?.name }}</div>
              </td>
              <td><span class="status-pill" :class="`status-pill--${a.status}`">{{ a.status === 'published' ? 'Опубл.' : 'Черновик' }}</span></td>
              <td style="color:var(--text-muted);font-size:13px;">{{ fmtDate(a.created_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Recent admin log -->
    <div class="admin-card" style="margin-top:24px;">
      <div class="admin-card__header">
        Последние действия
        <Link href="/admin/logs" class="admin-card__link">Все →</Link>
      </div>
      <table class="admin-table">
        <thead>
          <tr><th>Администратор</th><th>Действие</th><th>Дата</th></tr>
        </thead>
        <tbody>
          <tr v-for="log in recentLogs" :key="log.id">
            <td style="font-size:13px;">@{{ log.admin?.username }}</td>
            <td style="font-size:13px;">{{ log.description }}</td>
            <td style="color:var(--text-muted);font-size:12px;">{{ fmtDateTime(log.created_at) }}</td>
          </tr>
          <tr v-if="!recentLogs.length">
            <td colspan="3" style="color:var(--text-muted);text-align:center;padding:16px;">Действий пока нет</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Sparkline from '@/Components/Sparkline.vue'

defineProps({
  stats:          Object,
  userChart:      Array,
  articleChart:   Array,
  recentUsers:    Array,
  recentArticles: Array,
  recentLogs:     Array,
})

function roleLabel(r) {
  return { admin: 'Админ', moderator: 'Модер.', user: 'Юзер' }[r] || r
}
function fmtDate(d) {
  return new Date(d).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' })
}
function fmtDateTime(d) {
  return new Date(d).toLocaleString('ru-RU', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
}
</script>
