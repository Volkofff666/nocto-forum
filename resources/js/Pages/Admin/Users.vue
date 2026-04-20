<template>
  <AdminLayout title="Пользователи">
    <div class="admin-toolbar">
      <input
        v-model="search"
        type="search"
        class="admin-search"
        placeholder="Поиск по имени или username..."
        @keydown.enter="reload"
      />
      <button class="btn btn-outline btn-sm" @click="reload">Найти</button>
    </div>

    <!-- Role tabs -->
    <div class="admin-tabs">
      <button
        v-for="tab in roleTabs" :key="tab.value"
        class="admin-tab"
        :class="{ active: role === tab.value }"
        @click="setRole(tab.value)"
      >{{ tab.label }}</button>
    </div>

    <div class="admin-card">
      <table class="admin-table admin-table--full">
        <thead>
          <tr>
            <th>Пользователь</th>
            <th>Статей</th>
            <th>Роль</th>
            <th>Статус</th>
            <th>Дата регистрации</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="u in users.data" :key="u.id" :class="{ 'row--banned': u.banned_at }">
            <td>
              <div class="admin-user-cell">
                <div class="avatar avatar--32" style="flex-shrink:0;">
                  <img v-if="u.avatar_url" :src="u.avatar_url" :alt="u.name" />
                  <template v-else>{{ initials(u.name) }}</template>
                </div>
                <div>
                  <Link :href="`/admin/users/${u.id}`" style="font-weight:500;">{{ u.name }}</Link>
                  <div style="font-size:12px;color:var(--text-muted);">@{{ u.username }}</div>
                </div>
              </div>
            </td>
            <td>{{ u.articles_count }}</td>
            <td>
              <select
                class="admin-role-select"
                :value="u.role"
                :disabled="u.id === $page.props.auth?.user?.id"
                @change="updateRole(u, $event.target.value)"
              >
                <option value="user">Пользователь</option>
                <option value="moderator">Модератор</option>
                <option value="admin">Администратор</option>
              </select>
            </td>
            <td>
              <span v-if="u.banned_at" class="role-pill role-pill--banned" :title="`Причина: ${u.ban_reason || 'не указана'}`">
                Забанен
              </span>
              <span v-else class="role-pill role-pill--user">Активен</span>
            </td>
            <td style="color:var(--text-muted);font-size:13px;">{{ fmtDate(u.created_at) }}</td>
            <td>
              <div style="display:flex;gap:6px;align-items:center;">
                <button v-if="!u.banned_at" class="btn btn-warn-ghost btn-xs" @click="openBan(u)">Бан</button>
                <button v-else class="btn btn-outline btn-xs" @click="unban(u)">Разбан</button>
                <button
                  v-if="u.id !== $page.props.auth?.user?.id"
                  class="btn btn-danger-ghost btn-xs"
                  @click="deleteUser(u)"
                >Удалить</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <Pagination :paginator="users" style="padding:16px 0 4px;" />
    </div>

    <!-- Ban modal -->
    <div v-if="banTarget" class="modal-overlay" @click.self="banTarget = null">
      <div class="modal">
        <div class="modal__title">Заблокировать @{{ banTarget.username }}</div>
        <label class="modal__label">Причина (необязательно)</label>
        <input v-model="banReason" type="text" class="modal__input" placeholder="Нарушение правил…" />
        <div class="modal__actions">
          <button class="btn btn-secondary" @click="banTarget = null">Отмена</button>
          <button class="btn btn-danger" @click="confirmBan">Заблокировать</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  users:   Object,
  filters: Object,
})

const search    = ref(props.filters?.search || '')
const role      = ref(props.filters?.role || '')
const banTarget = ref(null)
const banReason = ref('')

const roleTabs = [
  { value: '',          label: 'Все' },
  { value: 'user',      label: 'Пользователи' },
  { value: 'moderator', label: 'Модераторы' },
  { value: 'admin',     label: 'Администраторы' },
  { value: 'banned',    label: 'Заблокированные' },
]

function reload() {
  router.get('/admin/users', { search: search.value, role: role.value }, { preserveState: true, replace: true })
}

function setRole(r) { role.value = r; reload() }

function updateRole(user, newRole) {
  router.patch(`/admin/users/${user.id}/role`, { role: newRole }, { preserveScroll: true })
}

function openBan(user) { banTarget.value = user; banReason.value = '' }

function confirmBan() {
  router.post(`/admin/users/${banTarget.value.id}/ban`, { reason: banReason.value }, {
    preserveScroll: true,
    onSuccess: () => { banTarget.value = null },
  })
}

function unban(user) {
  router.post(`/admin/users/${user.id}/unban`, {}, { preserveScroll: true })
}

function deleteUser(user) {
  if (!confirm(`Удалить пользователя "${user.name}"? Все его статьи и комментарии тоже удалятся.`)) return
  router.delete(`/admin/users/${user.id}`, { preserveScroll: true })
}

function initials(name) {
  return name.split(' ').slice(0, 2).map(w => w[0]?.toUpperCase()).join('')
}
function fmtDate(d) {
  return new Date(d).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>
