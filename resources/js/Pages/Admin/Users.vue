<template>
  <AdminLayout title="Пользователи">
    <!-- Search -->
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

    <div class="admin-card">
      <table class="admin-table admin-table--full">
        <thead>
          <tr>
            <th>Пользователь</th>
            <th>Статей</th>
            <th>Роль</th>
            <th>Дата регистрации</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="u in users.data" :key="u.id">
            <td>
              <div class="admin-user-cell">
                <div class="avatar avatar--32" style="flex-shrink:0;">
                  <img v-if="u.avatar_url" :src="u.avatar_url" :alt="u.name" />
                  <template v-else>{{ initials(u.name) }}</template>
                </div>
                <div>
                  <div style="font-weight:500;">{{ u.name }}</div>
                  <div style="font-size:12px;color:var(--text-muted);">@{{ u.username }}</div>
                </div>
              </div>
            </td>
            <td>{{ u.articles_count }}</td>
            <td>
              <select
                class="admin-role-select"
                :value="u.role"
                @change="updateRole(u, $event.target.value)"
              >
                <option value="user">Пользователь</option>
                <option value="moderator">Модератор</option>
                <option value="admin">Администратор</option>
              </select>
            </td>
            <td style="color:var(--text-muted);font-size:13px;">{{ fmtDate(u.created_at) }}</td>
            <td>
              <button
                class="btn btn-danger-ghost btn-xs"
                @click="deleteUser(u)"
              >Удалить</button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="users.last_page > 1" class="pagination" style="padding:16px 0 4px;">
        <button
          v-for="link in users.links" :key="link.label"
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
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  users:   Object,
  filters: Object,
})

const search = ref(props.filters?.search || '')

function reload() {
  router.get('/admin/users', { search: search.value }, { preserveState: true, replace: true })
}

function updateRole(user, role) {
  router.patch(`/admin/users/${user.id}/role`, { role }, { preserveScroll: true })
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
