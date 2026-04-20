<template>
  <AppLayout>
    <div class="page-wrap page-wrap--full" style="padding-top:20px;">
      <div class="content-card">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--border);">
          <h1 style="font-size:18px;font-weight:700;">Мои черновики</h1>
          <Link href="/articles/create" class="btn btn-primary btn-sm">Написать статью</Link>
        </div>

        <div v-if="drafts.data.length">
          <div v-for="draft in drafts.data" :key="draft.id" class="draft-item">
            <div class="draft-item__main">
              <Link :href="`/articles/${draft.id}/edit`" class="draft-item__title">{{ draft.title || 'Без названия' }}</Link>
              <div class="draft-item__meta">
                <span :class="`badge badge-${draft.category}`">{{ catLabel(draft.category) }}</span>
                <span>Изменён {{ timeAgo(draft.updated_at) }}</span>
              </div>
            </div>
            <div class="draft-item__actions">
              <Link :href="`/articles/${draft.id}/edit`" class="btn btn-outline btn-sm">Редактировать</Link>
              <form @submit.prevent="publish(draft)">
                <button type="submit" class="btn btn-primary btn-sm">Опубликовать</button>
              </form>
              <form @submit.prevent="destroy(draft)">
                <button type="submit" class="btn btn-danger-ghost btn-sm">Удалить</button>
              </form>
            </div>
          </div>

          <Pagination :paginator="drafts" />
        </div>

        <div v-else class="empty">
          <div class="empty__icon">✏️</div>
          <div class="empty__title">Черновиков нет</div>
          <div class="empty__text">Начните писать — черновики сохраняются здесь</div>
          <div style="margin-top:16px;">
            <Link href="/articles/create" class="btn btn-primary">Написать статью</Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'

defineProps({ drafts: Object })

const cats = { proxy: 'Прокси', vpn: 'VPN', security: 'Безопасность', tools: 'Инструменты', other: 'Другое' }
function catLabel(c) { return cats[c] || c }

function timeAgo(d) {
  const s = Math.floor((Date.now() - new Date(d)) / 1000)
  if (s < 60) return 'только что'
  if (s < 3600) return `${Math.floor(s/60)} мин. назад`
  if (s < 86400) return `${Math.floor(s/3600)} ч. назад`
  if (s < 2592000) return `${Math.floor(s/86400)} дн. назад`
  return `${Math.floor(s/2592000)} мес. назад`
}

function publish(draft) {
  router.patch(`/articles/${draft.id}/publish`, {}, {
    onSuccess: () => router.reload(),
  })
}

function destroy(draft) {
  if (confirm(`Удалить черновик «${draft.title || 'Без названия'}»?`)) {
    router.delete(`/articles/${draft.id}`)
  }
}
</script>

<style scoped>
.draft-item {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px 24px;
  border-bottom: 1px solid var(--border);
  transition: background 0.1s;
}
.draft-item:last-child { border-bottom: none; }
.draft-item:hover { background: rgba(0,0,0,0.01); }

.draft-item__main { flex: 1; min-width: 0; }

.draft-item__title {
  display: block;
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 6px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: var(--text);
}
.draft-item__title:hover { color: var(--accent); }

.draft-item__meta {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: var(--text-muted);
}

.draft-item__actions {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-shrink: 0;
}
</style>
