<template>
  <div
    class="notif-item"
    :class="{ 'notif-item--unread': unread }"
    role="button"
    tabindex="0"
    @click="$emit('click')"
    @keydown.enter="$emit('click')"
    @keydown.space.prevent="$emit('click')"
  >
    <div class="notif-item__icon" :class="iconClass">
      <!-- New reply icon -->
      <svg v-if="isReply" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="9 17 4 12 9 7"/>
        <path d="M20 18v-2a4 4 0 0 0-4-4H4"/>
      </svg>
      <!-- New comment icon (default) -->
      <svg v-else width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
    </div>

    <div class="notif-item__body">
      <div class="notif-item__text">
        <span class="notif-item__actor">{{ notification.data.actor_name }}</span>
        {{ actionLabel }}
        <span class="notif-item__article">«{{ notification.data.article_title }}»</span>
      </div>
      <div v-if="notification.data.comment_preview" class="notif-item__preview">
        {{ notification.data.comment_preview }}
      </div>
      <div class="notif-item__meta">
        <span class="notif-item__time">{{ timeAgo(notification.created_at) }}</span>
        <span v-if="unread" class="notif-item__dot" aria-hidden="true"></span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  notification: { type: Object, required: true },
  unread:       { type: Boolean, default: false },
})

defineEmits(['click'])

const isReply = computed(() => props.notification.data.type === 'new_reply')

const actionLabel = computed(() =>
  isReply.value ? 'ответил на ваш комментарий в' : 'прокомментировал'
)

const iconClass = computed(() =>
  isReply.value ? 'notif-item__icon--reply' : 'notif-item__icon--comment'
)

const rtf = new Intl.RelativeTimeFormat('ru', { numeric: 'auto' })

function timeAgo(dateStr) {
  const diff = (new Date(dateStr) - Date.now()) / 1000
  const abs  = Math.abs(diff)
  if (abs < 60)   return rtf.format(Math.round(diff), 'second')
  if (abs < 3600) return rtf.format(Math.round(diff / 60), 'minute')
  if (abs < 86400) return rtf.format(Math.round(diff / 3600), 'hour')
  if (abs < 2592000) return rtf.format(Math.round(diff / 86400), 'day')
  return rtf.format(Math.round(diff / 2592000), 'month')
}
</script>

<style scoped>
.notif-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 14px 16px;
  cursor: pointer;
  border-bottom: 1px solid var(--border);
  transition: background 0.12s;
  outline: none;
}
.notif-item:last-child {
  border-bottom: none;
}
.notif-item:hover,
.notif-item:focus-visible {
  background: var(--bg-hover);
}
.notif-item--unread {
  background: var(--bg-secondary);
}
.notif-item--unread:hover,
.notif-item--unread:focus-visible {
  background: var(--bg-hover);
}

.notif-item__icon {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  margin-top: 1px;
}
.notif-item__icon--comment {
  background: var(--accent-light);
  color: var(--accent);
}
.notif-item__icon--reply {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.notif-item__body {
  flex: 1;
  min-width: 0;
}

.notif-item__text {
  font-size: 14px;
  color: var(--text);
  line-height: 1.45;
}
.notif-item__actor {
  font-weight: 600;
}
.notif-item__article {
  color: var(--accent);
}

.notif-item__preview {
  margin-top: 4px;
  font-size: 13px;
  color: var(--text-muted);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.notif-item__meta {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 5px;
}
.notif-item__time {
  font-size: 12px;
  color: var(--text-muted);
}
.notif-item__dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--accent);
  flex-shrink: 0;
}
</style>
