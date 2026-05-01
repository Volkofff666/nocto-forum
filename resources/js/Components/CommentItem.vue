<template>
  <div class="comment-item">
    <!-- Avatar 28px — ITHub comment size -->
    <div class="avatar avatar--28" :style="avatarStyle(comment.user)" style="margin-top:2px;flex-shrink:0">
      <img v-if="comment.user.avatar_url" :src="comment.user.avatar_url" :alt="comment.user.name" />
      <template v-else>{{ comment.user.avatar }}</template>
    </div>

    <div class="comment-item__body">
      <!-- Header: author + time -->
      <div class="comment-item__header">
        <Link :href="`/profile/${comment.user.username}`" class="comment-item__author">{{ comment.user.name }}</Link>
        <span class="comment-item__time">{{ timeAgo(comment.created_at) }}</span>
      </div>

      <!-- Body text -->
      <div class="comment-item__text">{{ comment.body }}</div>

      <!-- Action row -->
      <div class="comment-item__actions">
        <button v-if="canComment" class="comment-action-btn" @click="toggleReply">
          {{ showReply ? 'Отмена' : 'Ответить' }}
        </button>
        <button v-if="canComment && !canDelete" class="comment-action-btn" @click="toggleReport">
          Пожаловаться
        </button>
        <form v-if="canDelete" @submit.prevent="del">
          <button type="submit" class="comment-action-btn comment-action-btn--danger">Удалить</button>
        </form>
      </div>

      <!-- Report form -->
      <div v-if="showReport" class="reply-compose">
        <input
          v-model="reportReason"
          type="text"
          class="reply-compose__input"
          placeholder="Причина жалобы…"
          style="height:auto;padding:8px;"
        />
        <div class="reply-compose__footer">
          <button class="btn btn-outline btn-sm" @click="showReport = false">Отмена</button>
          <button class="btn btn-danger btn-sm" :disabled="!reportReason.trim()" @click="sendReport">Отправить жалобу</button>
        </div>
      </div>

      <!-- Reply compose -->
      <div v-if="showReply" class="reply-compose">
        <textarea
          v-model="replyText"
          class="reply-compose__input"
          placeholder="Ваш ответ..."
          rows="2"
          ref="replyRef"
        ></textarea>
        <div class="reply-compose__footer">
          <button class="btn btn-outline btn-sm" @click="showReply = false">Отмена</button>
          <button class="btn btn-primary btn-sm" :disabled="!replyText.trim()" @click="sendReply">Отправить</button>
        </div>
      </div>

      <p v-if="replyError" style="color:var(--color-red-500);font-size:13px;margin-top:4px;">{{ replyError }}</p>

      <!-- Replies (ITHub nested indent) -->
      <div v-if="comment.replies?.length" class="comment-replies">
        <div v-for="reply in comment.replies" :key="reply.id" class="comment-item" style="padding:10px 0;">
          <div class="avatar avatar--28" :style="avatarStyle(reply.user)" style="margin-top:2px;flex-shrink:0">
            <img v-if="reply.user.avatar_url" :src="reply.user.avatar_url" :alt="reply.user.name" />
            <template v-else>{{ reply.user.avatar }}</template>
          </div>
          <div class="comment-item__body">
            <div class="comment-item__header">
              <Link :href="`/profile/${reply.user.username}`" class="comment-item__author">{{ reply.user.name }}</Link>
              <span class="comment-item__time">{{ timeAgo(reply.created_at) }}</span>
            </div>
            <div class="comment-item__text">{{ reply.body }}</div>
            <div class="comment-item__actions">
              <form v-if="canDeleteComment(reply)" @submit.prevent="delReply(reply.id)">
                <button type="submit" class="comment-action-btn comment-action-btn--danger">Удалить</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  comment: Object,
  articleId: Number,
})

const page = usePage()
const showReply    = ref(false)
const replyText    = ref('')
const replyRef     = ref(null)
const replyError   = ref(null)
const showReport   = ref(false)
const reportReason = ref('')

const canComment = computed(() => !!page.props.auth?.user)

const canDelete = computed(() => {
  const u = page.props.auth?.user
  return u && (u.id === props.comment.user_id || ['moderator','admin'].includes(u.role))
})

function canDeleteComment(c) {
  const u = page.props.auth?.user
  return u && (u.id === c.user_id || ['moderator','admin'].includes(u.role))
}

// Avatar background palette (matches ITHub avatar color system)
const AVATAR_COLORS = ['#3b82f6', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444']
function avatarStyle(user) {
  if (user.avatar_url) return {}
  const idx = (user.id ?? 0) % AVATAR_COLORS.length
  return { background: AVATAR_COLORS[idx] }
}

function toggleReport() {
  showReport.value = !showReport.value
  reportReason.value = ''
}

function sendReport() {
  router.post(`/report/comment/${props.comment.id}`, { reason: reportReason.value }, {
    preserveScroll: true,
    onSuccess: () => { showReport.value = false; reportReason.value = '' },
  })
}

async function toggleReply() {
  showReply.value = !showReply.value
  if (showReply.value) {
    await nextTick()
    replyRef.value?.focus()
  }
}

function sendReply() {
  if (!replyText.value.trim()) return
  router.post(`/articles/${props.articleId}/comments`, {
    body: replyText.value,
    parent_id: props.comment.id,
  }, {
    onSuccess: () => { replyText.value = ''; showReply.value = false },
    preserveScroll: true,
  })
}

function del() {
  if (confirm('Удалить комментарий?')) {
    router.delete(`/comments/${props.comment.id}`, { preserveScroll: true })
  }
}

async function delReply(id) {
  if (!confirm('Удалить комментарий?')) return
  replyError.value = null
  try {
    await router.delete(`/comments/${id}`, {
      preserveScroll: true,
      onError: (errors) => {
        replyError.value = 'Не удалось удалить комментарий. Попробуйте обновить страницу.'
      },
    })
  } catch (e) {
    replyError.value = 'Комментарий уже был удалён или недоступен.'
  }
}

function timeAgo(d) {
  const s = Math.floor((Date.now() - new Date(d)) / 1000)
  if (s < 60) return 'только что'
  if (s < 3600) return `${Math.floor(s/60)} мин. назад`
  if (s < 86400) return `${Math.floor(s/3600)} ч. назад`
  if (s < 2592000) return `${Math.floor(s/86400)} дн. назад`
  return `${Math.floor(s/2592000)} мес. назад`
}
</script>

<style scoped>
/* Comment action buttons — ghost style, no border, compact */
.comment-action-btn {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 12px;
  font-weight: 500;
  color: var(--text-muted);
  font-family: var(--font-ui);
  padding: 0;
  transition: color var(--transition-base);
}
.comment-action-btn:hover { color: var(--text); }
.comment-action-btn--danger { color: var(--color-red-500); }
.comment-action-btn--danger:hover { color: #dc2626; }
</style>
