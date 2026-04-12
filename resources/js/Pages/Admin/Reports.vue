<template>
  <AdminLayout title="Жалобы">
    <!-- Status tabs -->
    <div class="admin-tabs">
      <button
        v-for="tab in tabs" :key="tab.value"
        class="admin-tab"
        :class="{ active: status === tab.value }"
        @click="setStatus(tab.value)"
      >
        {{ tab.label }}
        <span v-if="counts[tab.value] > 0" class="admin-tab__count">{{ counts[tab.value] }}</span>
      </button>
    </div>

    <div class="admin-card">
      <table class="admin-table admin-table--full">
        <thead>
          <tr>
            <th>Жалоба на</th>
            <th>Причина</th>
            <th>От кого</th>
            <th>Дата</th>
            <th v-if="status === 'pending'">Действия</th>
            <th v-else>Рассмотрел</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in reports.data" :key="r.id">
            <td>
              <div style="font-size:12px;color:var(--text-muted);margin-bottom:2px;">
                {{ typeLabel(r.reportable_type) }}
              </div>
              <div style="font-size:13px;">
                <template v-if="r.reportable">
                  <Link
                    v-if="r.reportable_type?.includes('Article')"
                    :href="`/articles/${r.reportable.slug}`"
                    class="admin-table__link"
                  >{{ r.reportable.title }}</Link>
                  <span v-else>{{ r.reportable.body?.slice(0, 60) }}…</span>
                </template>
                <span v-else class="text-muted">Удалено</span>
              </div>
            </td>
            <td style="max-width:200px;">{{ r.reason }}</td>
            <td style="font-size:13px;">@{{ r.user?.username }}</td>
            <td style="color:var(--text-muted);font-size:12px;">{{ fmtDate(r.created_at) }}</td>
            <td v-if="status === 'pending'">
              <div style="display:flex;gap:6px;">
                <button class="btn btn-danger btn-xs" @click="resolve(r)">Принять</button>
                <button class="btn btn-outline btn-xs" @click="dismiss(r)">Отклонить</button>
              </div>
            </td>
            <td v-else style="font-size:12px;color:var(--text-muted);">
              {{ r.resolver ? `@${r.resolver.username}` : '—' }}
            </td>
          </tr>
          <tr v-if="!reports.data.length">
            <td :colspan="status === 'pending' ? 5 : 5" style="text-align:center;color:var(--text-muted);padding:24px;">
              Жалоб нет
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="reports.last_page > 1" class="pagination" style="padding:16px 0 4px;">
        <button
          v-for="link in reports.links" :key="link.label"
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
  reports: Object,
  filters: Object,
  counts:  Object,
})

const status = ref(props.filters?.status || 'pending')

const tabs = [
  { value: 'pending',   label: 'Ожидают' },
  { value: 'resolved',  label: 'Приняты' },
  { value: 'dismissed', label: 'Отклонены' },
]

function setStatus(s) {
  status.value = s
  router.get('/admin/reports', { status: s }, { preserveState: true, replace: true })
}

function resolve(report) {
  router.post(`/admin/reports/${report.id}/resolve`, {}, { preserveScroll: true })
}

function dismiss(report) {
  router.post(`/admin/reports/${report.id}/dismiss`, {}, { preserveScroll: true })
}

function typeLabel(type) {
  if (!type) return ''
  return type.includes('Article') ? 'Статья' : 'Комментарий'
}

function fmtDate(d) {
  return new Date(d).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>
