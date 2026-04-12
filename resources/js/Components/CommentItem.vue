<template>
  <div class="comment-item">
    <div class="comment-item__header">
      <div class="avatar" style="width:28px;height:28px;font-size:11px;">
        <img v-if="comment.user.avatar_url" :src="comment.user.avatar_url" :alt="comment.user.name" />
        <template v-else>{{ comment.user.avatar }}</template>
      </div>
      <Link :href="`/profile/${comment.user.username}`" class="comment-item__author">
        {{ comment.user.name }}
      </Link>
      <span class="comment-item__time">{{ timeAgo(comment.created_at) }}</span>
    </div>

    <div class="comment-item__body">{{ comment.body }}</div>

    <div class="comment-item__actions">
      <button
        v-if="$page.props.auth?.user"
        class="comment-item__reply-btn"
        @click="showReply = !showReply"
      >
        {{ showReply ? 'Отмена' : 'Ответить' }}
      </button>
      <form v-if="canDelete" @submit.prevent="deleteComment">
        <button type="submit" class="btn-danger" style="padding:2px 8px;font-size:12px;">Удалить</button>
      </form>
    </div>

    <!-- Форма ответа -->
    <div v-if="showReply" class="reply-form">
      <form @submit.prevent="submitReply">
        <textarea
          v-model="replyBody"
          placeholder="Ваш ответ..."
          rows="2"
        ></textarea>
        <div class="reply-form__actions">
          <button type="button" class="btn-outline" style="padding:5px 12px;font-size:13px;" @click="showReply = false">Отмена</button>
          <button type="submit" class="btn-primary" style="padding:5px 12px;font-size:13px;" :disabled="!replyBody.trim()">Отправить</button>
        </div>
      </form>
    </div>

    <!-- Вложенные ответы (max 2 уровня) -->
    <div v-if="comment.replies && comment.replies.length > 0" class="comment-item__replies">
      <div
        v-for="reply in comment.replies"
        :key="reply.id"
        class="comment-item"
        style="padding:10px 0;"
      >
        <div class="comment-item__header">
          <div class="avatar" style="width:24px;height:24px;font-size:10px;">
            <img v-if="reply.user.avatar_url" :src="reply.user.avatar_url" :alt="reply.user.name" />
            <template v-else>{{ reply.user.avatar }}</template>
          </div>
          <Link :href="`/profile/${reply.user.username}`" class="comment-item__author">
            {{ reply.user.name }}
          </Link>
          <span class="comment-item__time">{{ timeAgo(reply.created_at) }}</span>
        </div>
        <div class="comment-item__body">{{ reply.body }}</div>
        <div class="comment-item__actions">
          <form v-if="canDeleteComment(reply)" @submit.prevent="deleteReply(reply.id)">
            <button type="submit" class="btn-danger" style="padding:2px 8px;font-size:12px;">Удалить</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
  comment:   Object,
  articleId: Number,
})

const page = usePage()
const showReply = ref(false)
const replyBody = ref('')

const canDelete = computed(() => {
  const user = page.props.auth?.user
  if (!user) return false
  return user.id === props.comment.user_id || ['moderator', 'admin'].includes(user.role)
})

function canDeleteComment(comment) {
  const user = page.props.auth?.user
  if (!user) return false
  return user.id === comment.user_id || ['moderator', 'admin'].includes(user.role)
}

function timeAgo(dateStr) {
  const now = new Date()
  const date = new Date(dateStr)
  const diff = Math.floor((now - date) / 1000)

  if (diff < 60)       return 'только что'
  if (diff < 3600)     return `${Math.floor(diff / 60)} мин. назад`
  if (diff < 86400)    return `${Math.floor(diff / 3600)} ч. назад`
  if (diff < 2592000)  return `${Math.floor(diff / 86400)} дн. назад`
  return `${Math.floor(diff / 2592000)} мес. назад`
}

function submitReply() {
  router.post(`/articles/${props.articleId}/comments`, {
    body:      replyBody.value,
    parent_id: props.comment.id,
  }, {
    onSuccess: () => {
      replyBody.value = ''
      showReply.value = false
    },
    preserveScroll: true,
  })
}

function deleteComment() {
  if (confirm('Удалить комментарий?')) {
    router.delete(`/comments/${props.comment.id}`, { preserveScroll: true })
  }
}

function deleteReply(replyId) {
  if (confirm('Удалить комментарий?')) {
    router.delete(`/comments/${replyId}`, { preserveScroll: true })
  }
}
</script>
