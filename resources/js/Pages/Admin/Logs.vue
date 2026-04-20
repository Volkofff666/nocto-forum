<template>
  <AdminLayout title="Лог действий">
    <div class="admin-card">
      <table class="admin-table admin-table--full">
        <thead>
          <tr>
            <th>Администратор</th>
            <th>Действие</th>
            <th>Описание</th>
            <th>Дата</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="log in logs.data" :key="log.id">
            <td style="font-size:13px;white-space:nowrap;">
              <span style="font-weight:500;">{{ log.admin?.name }}</span>
              <div style="font-size:12px;color:var(--text-muted);">@{{ log.admin?.username }}</div>
            </td>
            <td>
              <span class="action-pill" :class="`action-pill--${log.action}`">{{ actionLabel(log.action) }}</span>
            </td>
            <td style="font-size:13px;color:var(--text-secondary);">{{ log.description }}</td>
            <td style="color:var(--text-muted);font-size:12px;white-space:nowrap;">{{ fmtDateTime(log.created_at) }}</td>
          </tr>
          <tr v-if="!logs.data.length">
            <td colspan="4" style="text-align:center;color:var(--text-muted);padding:24px;">Лог пуст</td>
          </tr>
        </tbody>
      </table>

      <Pagination :paginator="logs" style="padding:16px 0 4px;" />
    </div>
  </AdminLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'

defineProps({ logs: Object })

const labels = {
  ban:            'Бан',
  unban:          'Разбан',
  change_role:    'Смена роли',
  delete_user:    'Удаление юзера',
  delete_article: 'Удаление статьи',
  delete_comment: 'Удаление ком.',
  toggle_article: 'Смена статуса',
  resolve_report: 'Принята жалоба',
  dismiss_report: 'Откл. жалоба',
}

function actionLabel(action) {
  return labels[action] || action
}

function fmtDateTime(d) {
  return new Date(d).toLocaleString('ru-RU', {
    day: 'numeric', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  })
}
</script>
