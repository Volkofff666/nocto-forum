<template>
  <AdminLayout title="Комментарии">
    <div class="admin-toolbar">
      <input
        v-model="search"
        type="search"
        class="admin-search"
        placeholder="Поиск по тексту..."
        @keydown.enter="reload"
      />
      <button class="btn btn-outline btn-sm" @click="reload">Найти</button>
    </div>

    <div class="admin-card">
      <table class="admin-table admin-table--full">
        <thead>
          <tr>
            <th>Автор</th>
            <th>Комментарий</th>
            <th>Статья</th>
            <th>Дата</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="c in comments.data" :key="c.id">
            <td style="white-space:nowrap;">
              <Link :href="`/profile/${c.user?.username}`" class="admin-table__link">{{ c.user?.name }}</Link>
              <div style="font-size:11px;color:var(--text-muted);">@{{ c.user?.username }}</div>
            </td>
            <td style="max-width:320px;">
              <div class="admin-comment-body">{{ c.body }}</div>
            </td>
            <td style="max-width:200px;">
              <Link :href="`/articles/${c.article?.slug}#comments`" class="admin-table__link" target="_blank">
                {{ c.article?.title }}
              </Link>
            </td>
            <td style="color:var(--text-muted);font-size:13px;white-space:nowrap;">{{ fmtDate(c.created_at) }}</td>
            <td>
              <button class="btn btn-danger-ghost btn-xs" @click="deleteComment(c)">Удалить</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="comments.last_page > 1" class="pagination" style="padding:16px 0 4px;">
        <button
          v-for="link in comments.links" :key="link.label"
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
  comments: Object,
  filters:  Object,
})

const search = ref(props.filters?.search || '')

function reload() {
  router.get('/admin/comments', { search: search.value }, { preserveState: true, replace: true })
}

function deleteComment(c) {
  if (!confirm('Удалить комментарий?')) return
  router.delete(`/admin/comments/${c.id}`, { preserveScroll: true })
}

function fmtDate(d) {
  return new Date(d).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short' })
}
</script>
