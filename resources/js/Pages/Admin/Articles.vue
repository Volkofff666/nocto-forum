<template>
  <AdminLayout title="Статьи">
    <!-- Toolbar -->
    <div class="admin-toolbar">
      <input
        v-model="search"
        type="search"
        class="admin-search"
        placeholder="Поиск по заголовку..."
        @keydown.enter="reload"
      />
      <select v-model="status" class="admin-select" @change="reload">
        <option value="">Все статусы</option>
        <option value="published">Опубликованные</option>
        <option value="draft">Черновики</option>
      </select>
      <button class="btn btn-outline btn-sm" @click="reload">Найти</button>
    </div>

    <div class="admin-card">
      <table class="admin-table admin-table--full">
        <thead>
          <tr>
            <th>Заголовок</th>
            <th>Автор</th>
            <th>Катег.</th>
            <th>Комм.</th>
            <th>Статус</th>
            <th>Дата</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="a in articles.data" :key="a.id">
            <td style="max-width:260px;">
              <Link :href="`/articles/${a.slug}`" class="admin-table__link" target="_blank">{{ a.title }}</Link>
            </td>
            <td style="white-space:nowrap;">
              <Link :href="`/profile/${a.user?.username}`" class="admin-table__link">{{ a.user?.name }}</Link>
            </td>
            <td><span :class="`badge badge-${a.category}`" style="font-size:11px;">{{ catLabel(a.category) }}</span></td>
            <td>{{ a.comments_count }}</td>
            <td>
              <span class="status-pill" :class="`status-pill--${a.status}`">
                {{ a.status === 'published' ? 'Опубл.' : 'Черновик' }}
              </span>
            </td>
            <td style="color:var(--text-muted);font-size:13px;white-space:nowrap;">{{ fmtDate(a.created_at) }}</td>
            <td style="white-space:nowrap;">
              <button class="btn btn-outline btn-xs" @click="toggleStatus(a)" style="margin-right:4px;">
                {{ a.status === 'published' ? 'Скрыть' : 'Опубл.' }}
              </button>
              <button class="btn btn-danger-ghost btn-xs" @click="deleteArticle(a)">Удалить</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="articles.last_page > 1" class="pagination" style="padding:16px 0 4px;">
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
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  articles: Object,
  filters:  Object,
})

const search = ref(props.filters?.search || '')
const status = ref(props.filters?.status || '')

function reload() {
  const params = {}
  if (search.value) params.search = search.value
  if (status.value) params.status = status.value
  router.get('/admin/articles', params, { preserveState: true, replace: true })
}

function toggleStatus(article) {
  router.patch(`/admin/articles/${article.id}/toggle`, {}, { preserveScroll: true })
}

function deleteArticle(article) {
  if (!confirm(`Удалить статью "${article.title}"?`)) return
  router.delete(`/admin/articles/${article.id}`, { preserveScroll: true })
}

const cats = { proxy: 'Прокси', vpn: 'VPN', security: 'Безоп.', tools: 'Инстр.', other: 'Другое' }
function catLabel(c) { return cats[c] || c }
function fmtDate(d) {
  return new Date(d).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>
