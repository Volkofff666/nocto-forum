<template>
  <div class="comment-item">
    <div class="avatar avatar--32" style="margin-top:1px;">
      <img v-if="comment.user.avatar_url" :src="comment.user.avatar_url" :alt="comment.user.name" />
      <template v-else>{{ comment.user.avatar }}</template>
    </div>

    <div class="comment-item__body">
      <div class="comment-item__header">
        <Link :href="`/profile/${comment.user.username}`" class="comment-item__author">{{ comment.user.name }}</Link>
        <span class="comment-item__time">{{ timeAgo(comment.created_at) }}</span>
      </div>

      <div class="comment-item__text">{{ comment.body }}</div>

      <div class="comment-item__actions">
        <button v-if="canComment" class="btn btn-ghost btn-xs" @click="toggleReply">
          {{ showReply ? 'Отмена' : 'Ответить' }}
        </button>
        <button v-if="canComment && !canDelete" class="btn btn-ghost btn-xs" @click="toggleReport">
          Пожаловаться
        </button>
        <form v-if="canDelete" @submit.prevent="del">
          <button type="submit" class="btn btn-danger-ghost btn-xs">Удалить</button>
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

      <!-- Replies -->
      <div v-if="comment.replies?.length" class="comment-replies">
        <div v-for="reply in comment.replies" :key="reply.id" class="comment-item" style="padding:10px 0;">
          <div class="avatar avatar--24" style="margin-top:1px;">
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
                <button type="submit" class="btn btn-danger-ghost btn-xs">Удалить</button>
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
const showReply   = ref(false)
const replyText   = ref('')
const replyRef    = ref(null)
const showReport  = ref(false)
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

function toggleReport() { showReport.value = !showReport.value; reportReason.value = '' }

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

function delReply(id) {
  if (confirm('Удалить комментарий?')) {
    router.delete(`/comments/${id}`, { preserveScroll: true })
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
