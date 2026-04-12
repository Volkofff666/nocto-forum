<template>
  <AppLayout>
    <div class="page-wrap">
      <div>
        <div class="content-card article-page">

          <!-- Draft notice -->
          <div v-if="article.status === 'draft'" class="article-draft-bar">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Черновик — виден только вам.
            <button class="btn btn-primary btn-sm" style="margin-left:auto;" @click="publish">Опубликовать</button>
          </div>

          <!-- Category + title -->
          <div class="article-category">
            <span :class="`badge badge-${article.category}`">{{ catLabel(article.category) }}</span>
          </div>
          <h1 class="article-title">{{ article.title }}</h1>

          <!-- Byline -->
          <div class="article-byline">
            <div class="avatar avatar--40">
              <img v-if="article.user.avatar_url" :src="article.user.avatar_url" :alt="article.user.name" />
              <template v-else>{{ article.user.avatar }}</template>
            </div>
            <div class="article-byline__info">
              <div class="article-byline__author">
                <Link :href="`/profile/${article.user.username}`">{{ article.user.name }}</Link>
              </div>
              <div class="article-byline__meta">{{ formatDate(article.created_at) }}</div>
            </div>
            <div v-if="canEdit" class="article-byline__actions">
              <Link :href="`/articles/${article.id}/edit`" class="btn btn-outline btn-sm">Редактировать</Link>
              <button class="btn btn-danger-ghost btn-sm" @click="destroy">Удалить</button>
            </div>
          </div>

          <!-- Body -->
          <div class="article-body">
            <p v-for="(p, i) in paragraphs" :key="i">{{ p }}</p>
          </div>

          <!-- Vote bar -->
          <VoteBar :article="article" :userVote="userVote" />

          <!-- Related -->
          <div v-if="related.length" class="related">
            <div class="related__title">Читайте также</div>
            <div class="related__list">
              <div v-for="r in related" :key="r.id" class="related-item">
                <div class="avatar avatar--32">
                  <img v-if="r.user.avatar_url" :src="r.user.avatar_url" :alt="r.user.name" />
                  <template v-else>{{ r.user.avatar }}</template>
                </div>
                <div class="related-item__body">
                  <div class="related-item__title">
                    <Link :href="`/articles/${r.slug}`">{{ r.title }}</Link>
                  </div>
                  <div class="related-item__meta">{{ r.user.name }} · {{ formatDate(r.created_at) }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Comments -->
          <div class="comments" id="comments">
            <div class="comments__header">
              <div class="comments__title">
                Комментарии
                <span class="comments__count">({{ totalComments }})</span>
              </div>
            </div>

            <!-- Compose -->
            <div v-if="$page.props.auth?.user" class="comment-compose">
              <div class="comment-compose__inner">
                <div class="avatar avatar--32">
                  <img v-if="$page.props.auth.user.avatar_url" :src="$page.props.auth.user.avatar_url" :alt="$page.props.auth.user.name" />
                  <template v-else>{{ $page.props.auth.user.avatar }}</template>
                </div>
                <textarea
                  v-model="commentText"
                  class="comment-compose__input"
                  placeholder="Написать комментарий..."
                  @keydown.ctrl.enter="sendComment"
                ></textarea>
              </div>
              <div class="comment-compose__footer">
                <span style="font-size:12px;color:var(--text-muted);">Ctrl+Enter для отправки</span>
                <button class="btn btn-primary btn-sm" :disabled="!commentText.trim()" @click="sendComment">Отправить</button>
              </div>
            </div>
            <div v-else class="guest-comment">
              <Link href="/login">Войдите</Link>, чтобы оставить комментарий
            </div>

            <!-- List -->
            <div v-if="article.comments.length">
              <CommentItem
                v-for="c in article.comments"
                :key="c.id"
                :comment="c"
                :articleId="article.id"
              />
            </div>
            <div v-else class="empty" style="padding:28px 0;">
              <div class="empty__text">Комментариев пока нет — будьте первым!</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <aside class="sidebar">
        <div v-if="$page.props.auth?.user" class="sidebar-card write-cta">
          <p>Есть что рассказать сообществу?</p>
          <Link href="/articles/create" class="btn btn-primary" style="width:100%;">Написать статью</Link>
        </div>
        <div v-else class="sidebar-card write-cta">
          <p>Войдите, чтобы голосовать и комментировать</p>
          <a href="/auth/telegram" class="btn btn-tg" style="width:100%;justify-content:center;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.04 9.607c-.148.658-.537.818-1.084.508l-3-2.21-1.447 1.393c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.48 14.607l-2.95-.924c-.641-.202-.654-.641.136-.951l11.52-4.442c.534-.194 1.001.13.376.958z"/></svg>
            Войти через Telegram
          </a>
        </div>

        <!-- Author card -->
        <div class="sidebar-card">
          <div class="sidebar-card__title">Автор</div>
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
            <div class="avatar avatar--40">
              <img v-if="article.user.avatar_url" :src="article.user.avatar_url" :alt="article.user.name" />
              <template v-else>{{ article.user.avatar }}</template>
            </div>
            <div>
              <div style="font-size:14px;font-weight:600;">
                <Link :href="`/profile/${article.user.username}`" style="color:var(--text)">{{ article.user.name }}</Link>
              </div>
              <div style="font-size:13px;color:var(--text-muted);">@{{ article.user.username }}</div>
            </div>
          </div>
          <div v-if="article.user.bio" style="font-size:13px;color:var(--text-muted);line-height:1.5;">{{ article.user.bio }}</div>
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
const commentText = ref('')

const canEdit = computed(() => {
  const u = page.props.auth?.user
  return u && (u.id === props.article.user_id || u.role === 'admin')
})

const paragraphs = computed(() =>
  props.article.body.split('\n').filter(p => p.trim())
)

const totalComments = computed(() =>
  props.article.comments.reduce((sum, c) => sum + 1 + (c.replies?.length ?? 0), 0)
)

const cats = { proxy: 'Прокси', vpn: 'VPN', security: 'Безопасность', tools: 'Инструменты', other: 'Другое' }
function catLabel(c) { return cats[c] || c }

function formatDate(d) {
  return new Date(d).toLocaleDateString('ru-RU', { day: 'numeric', month: 'long', year: 'numeric' })
}

function sendComment() {
  if (!commentText.value.trim()) return
  router.post(`/articles/${props.article.id}/comments`, {
    body: commentText.value,
  }, {
    onSuccess: () => { commentText.value = '' },
    preserveScroll: true,
  })
}

function publish() {
  router.patch(`/articles/${props.article.id}/publish`)
}

function destroy() {
  if (confirm('Удалить статью? Это необратимо.')) {
    router.delete(`/articles/${props.article.id}`)
  }
}
</script>
