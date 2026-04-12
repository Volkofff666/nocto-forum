<template>
  <AdminLayout title="Обзор">
    <!-- Stats -->
    <div class="admin-stats">
      <div class="admin-stat-card">
        <div class="admin-stat-card__val">{{ stats.users }}</div>
        <div class="admin-stat-card__label">Пользователей</div>
      </div>
      <div class="admin-stat-card">
        <div class="admin-stat-card__val">{{ stats.published }}</div>
        <div class="admin-stat-card__label">Опубликовано</div>
      </div>
      <div class="admin-stat-card admin-stat-card--warn">
        <div class="admin-stat-card__val">{{ stats.drafts }}</div>
        <div class="admin-stat-card__label">Черновиков</div>
      </div>
      <div class="admin-stat-card">
        <div class="admin-stat-card__val">{{ stats.comments }}</div>
        <div class="admin-stat-card__label">Комментариев</div>
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
                <div style="font-weight:500;">{{ u.name }}</div>
                <div style="font-size:12px;color:var(--text-muted);">@{{ u.username }}</div>
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
  </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
  stats:          Object,
  recentUsers:    Array,
  recentArticles: Array,
})

function roleLabel(r) {
  return { admin: 'Админ', moderator: 'Модер.', user: 'Юзер' }[r] || r
}
function fmtDate(d) {
  return new Date(d).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' })
}
</script>
