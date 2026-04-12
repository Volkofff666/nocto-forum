<template>
  <AdminLayout :title="`Пользователь: ${user.name}`">
    <div style="margin-bottom:16px;">
      <Link href="/admin/users" class="admin-back">← Все пользователи</Link>
    </div>

    <div class="admin-grid-2" style="align-items:start;">
      <!-- Profile card -->
      <div class="admin-card">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
          <div class="avatar avatar--48">
            <img v-if="user.avatar_url" :src="user.avatar_url" :alt="user.name" />
            <template v-else>{{ initials(user.name) }}</template>
          </div>
          <div>
            <div style="font-size:18px;font-weight:600;">{{ user.name }}</div>
            <div style="color:var(--text-muted);">@{{ user.username }}</div>
            <div style="margin-top:4px;display:flex;gap:6px;">
              <span class="role-pill" :class="`role-pill--${user.role}`">{{ roleLabel(user.role) }}</span>
              <span v-if="user.banned_at" class="role-pill role-pill--banned">Забанен</span>
            </div>
          </div>
        </div>

        <div class="detail-row">
          <span class="detail-row__label">Email</span>
          <span>{{ user.email || '—' }}</span>
        </div>
        <div class="detail-row">
          <span class="detail-row__label">Telegram ID</span>
          <span>{{ user.telegram_id || '—' }}</span>
        </div>
        <div class="detail-row">
          <span class="detail-row__label">Регистрация</span>
          <span>{{ fmtDate(user.created_at) }}</span>
        </div>
        <div class="detail-row">
          <span class="detail-row__label">Статей</span>
          <span>{{ user.articles_count }}</span>
        </div>
        <div class="detail-row">
          <span class="detail-row__label">Комментариев</span>
          <span>{{ user.comments_count }}</span>
        </div>
        <div v-if="user.banned_at" class="detail-row">
          <span class="detail-row__label">Причина бана</span>
          <span style="color:var(--color-danger);">{{ user.ban_reason || 'не указана' }}</span>
        </div>
        <div v-if="user.bio" class="detail-row">
          <span class="detail-row__label">Bio</span>
          <span>{{ user.bio }}</span>
        </div>

        <div style="margin-top:20px;display:flex;gap:8px;flex-wrap:wrap;">
          <button v-if="!user.banned_at" class="btn btn-warn-ghost btn-sm" @click="openBan">Заблокировать</button>
          <button v-else class="btn btn-outline btn-sm" @click="unban">Разблокировать</button>
          <select class="admin-role-select" :value="user.role" @change="changeRole($event.target.value)">
            <option value="user">Пользователь</option>
            <option value="moderator">Модератор</option>
            <option value="admin">Администратор</option>
          </select>
        </div>
      </div>

      <!-- Stats side -->
      <div style="display:flex;flex-direction:column;gap:20px;">
        <!-- Articles -->
        <div class="admin-card">
          <div class="admin-card__header">Последние статьи</div>
          <table class="admin-table">
            <tbody>
              <tr v-for="a in articles" :key="a.id">
                <td>
                  <Link :href="`/articles/${a.slug}`" class="admin-table__link">{{ a.title }}</Link>
                </td>
                <td><span class="status-pill" :class="`status-pill--${a.status}`">{{ a.status === 'published' ? 'Опубл.' : 'Черн.' }}</span></td>
                <td style="color:var(--text-muted);font-size:12px;">{{ a.votes_count }} / {{ a.views_count }}</td>
                <td style="color:var(--text-muted);font-size:12px;">{{ fmtDate(a.created_at) }}</td>
              </tr>
              <tr v-if="!articles.length">
                <td style="color:var(--text-muted);">Нет статей</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Comments -->
        <div class="admin-card">
          <div class="admin-card__header">Последние комментарии</div>
          <div v-for="c in comments" :key="c.id" style="padding:10px 0;border-bottom:1px solid var(--border);">
            <div style="font-size:13px;margin-bottom:4px;">{{ c.body }}</div>
            <div style="font-size:12px;color:var(--text-muted);">
              к статье
              <Link :href="`/articles/${c.article?.slug}`" class="admin-table__link">{{ c.article?.title }}</Link>
              · {{ fmtDate(c.created_at) }}
            </div>
          </div>
          <div v-if="!comments.length" style="color:var(--text-muted);font-size:13px;padding:8px 0;">Нет комментариев</div>
        </div>
      </div>
    </div>

    <!-- Ban modal -->
    <div v-if="showBanModal" class="modal-overlay" @click.self="showBanModal = false">
      <div class="modal">
        <div class="modal__title">Заблокировать @{{ user.username }}</div>
        <label class="modal__label">Причина (необязательно)</label>
        <input v-model="banReason" type="text" class="modal__input" placeholder="Нарушение правил…" />
        <div class="modal__actions">
          <button class="btn btn-secondary" @click="showBanModal = false">Отмена</button>
          <button class="btn btn-danger" @click="confirmBan">Заблокировать</button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  user:     Object,
  articles: Array,
  comments: Array,
})

const showBanModal = ref(false)
const banReason    = ref('')

function openBan() { showBanModal.value = true; banReason.value = '' }

function confirmBan() {
  router.post(`/admin/users/${props.user.id}/ban`, { reason: banReason.value }, {
    preserveScroll: true,
    onSuccess: () => { showBanModal.value = false },
  })
}

function unban() {
  router.post(`/admin/users/${props.user.id}/unban`, {}, { preserveScroll: true })
}

function changeRole(role) {
  router.patch(`/admin/users/${props.user.id}/role`, { role }, { preserveScroll: true })
}

function roleLabel(r) {
  return { admin: 'Администратор', moderator: 'Модератор', user: 'Пользователь' }[r] || r
}
function initials(name) {
  return name.split(' ').slice(0, 2).map(w => w[0]?.toUpperCase()).join('')
}
function fmtDate(d) {
  return new Date(d).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>
