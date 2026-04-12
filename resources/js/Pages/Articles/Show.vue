<template>
  <AppLayout>
    <div class="page-layout">
      <div>
        <!-- Черновик -->
        <div v-if="article.status === 'draft'" class="draft-notice">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          Это черновик — только вы его видите.
          <form @submit.prevent="publish" style="display:inline;">
            <button type="submit" class="btn-primary" style="padding:4px 12px;font-size:13px;margin-left:8px;">Опубликовать</button>
          </form>
        </div>

        <!-- Заголовок -->
        <div class="article-header">
          <div class="article-header__category">
            <span :class="`badge badge-${article.category}`">{{ categoryLabel(article.category) }}</span>
          </div>
          <h1 class="article-header__title">{{ article.title }}</h1>
          <div class="article-header__meta">
            <div class="avatar">
              <img v-if="article.user.avatar_url" :src="article.user.avatar_url" :alt="article.user.name" />
              <template v-else>{{ article.user.avatar }}</template>
            </div>
            <div>
              <div class="article-header__author">
                <Link :href="`/profile/${article.user.username}`">{{ article.user.name }}</Link>
              </div>
              <div class="article-header__time">{{ formatDate(article.created_at) }}</div>
            </div>

            <!-- Кнопки управления для автора -->
            <div v-if="canEdit" style="margin-left:auto;display:flex;gap:8px;">
              <Link :href="`/articles/${article.id}/edit`" class="btn-outline">Редактировать</Link>
              <form @submit.prevent="destroy">
                <button type="submit" class="btn-danger">Удалить</button>
              </form>
            </div>
          </div>
        </div>

        <!-- Тело статьи -->
        <div class="article-body">
          <p v-for="(paragraph, i) in bodyParagraphs" :key="i">{{ paragraph }}</p>
        </div>

        <!-- Голосование -->
        <VoteBar :article="article" :userVote="userVote" />

        <!-- Похожие статьи -->
        <div v-if="related.length > 0" class="related-section">
          <div class="related-section__title">Похожие материалы</div>
          <div class="related-list">
            <div v-for="rel in related" :key="rel.id" class="related-item">
              <div class="avatar" style="width:28px;height:28px;font-size:11px;flex-shrink:0;">
                <img v-if="rel.user.avatar_url" :src="rel.user.avatar_url" :alt="rel.user.name" />
                <template v-else>{{ rel.user.avatar }}</template>
              </div>
              <div>
                <div class="related-item__title">
                  <Link :href="`/articles/${rel.slug}`">{{ rel.title }}</Link>
                </div>
                <div class="related-item__meta">{{ rel.user.name }} · {{ formatDate(rel.created_at) }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Комментарии -->
        <div class="comments-section">
          <div class="comments-section__title">Комментарии ({{ article.comments.length }})</div>

          <!-- Форма добавления комментария -->
          <div v-if="$page.props.auth?.user" class="comment-form">
            <form @submit.prevent="submitComment">
              <textarea
                v-model="commentBody"
                placeholder="Написать комментарий..."
                rows="3"
              ></textarea>
              <div class="comment-form__actions">
                <button type="submit" class="btn-primary" :disabled="!commentBody.trim()">Отправить</button>
              </div>
            </form>
          </div>
          <div v-else style="padding:12px 0;color:var(--text-muted);font-size:14px;">
            <Link href="/login" style="color:var(--accent);">Войдите</Link>, чтобы оставить комментарий.
          </div>

          <!-- Список комментариев -->
          <div v-if="article.comments.length > 0">
            <CommentItem
              v-for="comment in article.comments"
              :key="comment.id"
              :comment="comment"
              :articleId="article.id"
            />
          </div>
          <div v-else class="empty-state" style="padding:24px 0;">
            <div class="empty-state__text">Комментариев пока нет. Будьте первым!</div>
          </div>
        </div>
      </div>

      <!-- Сайдбар -->
      <aside class="sidebar">
        <div class="sidebar-block tg-block">
          <div class="tg-block__subs">9 047</div>
          <div class="tg-block__label">подписчиков в Telegram</div>
          <a href="https://t.me/noctohub" target="_blank" rel="noopener" class="btn-tg" style="width:100%;justify-content:center;">Подписаться</a>
        </div>

        <div class="sidebar-block" v-if="$page.props.auth?.user">
          <Link href="/articles/create" class="btn-primary" style="width:100%;justify-content:center;">Написать статью</Link>
        </div>
      </aside>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import VoteBar from '@/Components/VoteBar.vue'
import CommentItem from '@/Components/CommentItem.vue'

const props = defineProps({
  article: Object,
  related: Array,
  userVote: String,
})

const page = usePage()
const commentBody = ref('')

const canEdit = computed(() => {
  const user = page.props.auth?.user
  if (!user) return false
  return user.id === props.article.user_id || user.role === 'admin'
})

const bodyParagraphs = computed(() =>
  props.article.body.split('\n').filter(p => p.trim())
)

const categoryLabels = {
  proxy:    'Прокси',
  vpn:      'VPN',
  security: 'Безопасность',
  tools:    'Инструменты',
  other:    'Другое',
}

function categoryLabel(cat) {
  return categoryLabels[cat] || cat
}

function formatDate(dateStr) {
  return new Date(dateStr).toLocaleDateString('ru-RU', {
    day: 'numeric', month: 'long', year: 'numeric'
  })
}

function submitComment() {
  router.post(`/articles/${props.article.id}/comments`, {
    body: commentBody.value,
  }, {
    onSuccess: () => { commentBody.value = '' },
    preserveScroll: true,
  })
}

function publish() {
  router.patch(`/articles/${props.article.id}/publish`)
}

function destroy() {
  if (confirm('Удалить статью? Это действие нельзя отменить.')) {
    router.delete(`/articles/${props.article.id}`)
  }
}
</script>
