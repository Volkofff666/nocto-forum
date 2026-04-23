<template>
  <AppLayout>
    <div class="notif-page">
      <div class="notif-page__header">
        <h1 class="notif-page__title">Уведомления</h1>
        <button
          v-if="hasAnyNotification"
          class="btn btn-ghost btn-sm"
          :disabled="markingAll"
          @click="markAllRead"
        >
          Отметить все прочитанными
        </button>
      </div>

      <!-- Empty state -->
      <div v-if="!hasAnyNotification" class="content-card">
        <div class="empty">
          <div class="empty__icon">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
          </div>
          <div class="empty__title">Нет уведомлений</div>
          <div class="empty__text">Здесь появятся уведомления о комментариях, ответах и новых подписчиках</div>
        </div>
      </div>

      <!-- Unread section -->
      <template v-if="unread.length">
        <div class="notif-section-label">Непрочитанные</div>
        <div class="content-card notif-list">
          <NotifItem
            v-for="n in unread"
            :key="n.id"
            :notification="n"
            :unread="true"
            @click="handleClick(n)"
          />
        </div>
      </template>

      <!-- Read section -->
      <template v-if="read.length">
        <div class="notif-section-label">Прочитанные</div>
        <div class="content-card notif-list">
          <NotifItem
            v-for="n in read"
            :key="n.id"
            :notification="n"
            :unread="false"
            @click="handleClick(n)"
          />
        </div>
      </template>

      <!-- Pagination -->
      <Pagination :paginator="notifications" />
    </div>
  </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import NotifItem from '@/Components/NotifItem.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  notifications: Object, // paginated: { data, current_page, last_page, links, ... }
})

const markingAll = ref(false)

const unread = computed(() => props.notifications.data.filter(n => !n.read_at))
const read   = computed(() => props.notifications.data.filter(n =>  n.read_at))
const hasAnyNotification = computed(() => props.notifications.data.length > 0)

function notifTarget(n) {
  if (n.data.type === 'new_follower') {
    return `/profile/${n.data.actor_username}`
  }
  return `/articles/${n.data.article_slug}`
}

function handleClick(n) {
  const target = notifTarget(n)
  if (!n.read_at) {
    router.post(`/notifications/${n.id}/read`, {}, {
      preserveScroll: true,
      onSuccess: () => router.visit(target),
    })
    return
  }
  router.visit(target)
}

function markAllRead() {
  markingAll.value = true
  router.post('/notifications/read-all', {}, {
    onFinish: () => { markingAll.value = false },
  })
}
</script>

<style scoped>
.notif-page {
  max-width: 680px;
  margin: 0 auto;
  padding: 24px 0;
}

.notif-page__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
  gap: 12px;
}

.notif-page__title {
  font-size: 20px;
  font-weight: 700;
  color: var(--text);
}

.notif-section-label {
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--text-muted);
  margin: 20px 0 8px;
}

.notif-list {
  padding: 0;
  overflow: hidden;
}
</style>
