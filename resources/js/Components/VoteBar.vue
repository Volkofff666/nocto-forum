<template>
  <div class="vote-bar">
    <button
      class="vote-btn vote-btn--up"
      :class="{ 'vote-btn--active': localVote === 'up' }"
      :disabled="!canVote"
      @click="vote('up')"
      :title="canVote ? 'Нравится' : 'Войдите, чтобы голосовать'"
    >
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/>
        <path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
      </svg>
      Нравится
    </button>

    <span class="vote-score" :class="scoreClass">{{ displayScore }}</span>

    <button
      class="vote-btn vote-btn--down"
      :class="{ 'vote-btn--active': localVote === 'down' }"
      :disabled="!canVote"
      @click="vote('down')"
      :title="canVote ? 'Не нравится' : 'Войдите, чтобы голосовать'"
    >
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3H10z"/>
        <path d="M17 2h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"/>
      </svg>
      Не нравится
    </button>

    <div class="vote-bar__stats">
      <span class="vote-bar__stat">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        {{ article.views_count }} просмотров
      </span>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  article: Object,
  userVote: String,
})

const page = usePage()
const canVote = computed(() => !!page.props.auth?.user)
const localVote = ref(props.userVote ?? null)
const localCount = ref(props.article.votes_count)

const displayScore = computed(() => {
  const n = localCount.value
  return n > 0 ? `+${n}` : `${n}`
})

const scoreClass = computed(() => {
  if (localCount.value > 0) return 'vote-score--pos'
  if (localCount.value < 0) return 'vote-score--neg'
  return ''
})

function vote(type) {
  if (!canVote.value) {
    router.visit('/login')
    return
  }

  if (localVote.value === type) {
    localCount.value += type === 'up' ? -1 : 1
    localVote.value = null
  } else {
    if (localVote.value === 'up') localCount.value--
    if (localVote.value === 'down') localCount.value++
    localCount.value += type === 'up' ? 1 : -1
    localVote.value = type
  }

  router.post(`/articles/${props.article.id}/vote`, { type }, { preserveScroll: true })
}
</script>
