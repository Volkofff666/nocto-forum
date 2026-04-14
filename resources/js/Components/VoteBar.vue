<template>
  <div class="vote-bar">
    <button
      class="vote-btn vote-btn--up"
      :class="{ 'vote-btn--active': localVote === 'up' }"
      :disabled="!canVote"
      :title="canVote ? 'Нравится' : 'Войдите, чтобы голосовать'"
      @click="vote('up')"
    >
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="18 15 12 9 6 15"/>
      </svg>
    </button>

    <span class="vote-score" :class="scoreClass">{{ displayScore }}</span>

    <button
      class="vote-btn vote-btn--down"
      :class="{ 'vote-btn--active': localVote === 'down' }"
      :disabled="!canVote"
      :title="canVote ? 'Не нравится' : 'Войдите, чтобы голосовать'"
      @click="vote('down')"
    >
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
        <polyline points="6 9 12 15 18 9"/>
      </svg>
    </button>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  article:  Object,
  userVote: String,
})

const page       = usePage()
const canVote    = computed(() => !!page.props.auth?.user)
const localVote  = ref(props.userVote ?? null)
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
  if (!canVote.value) { router.visit('/login'); return }
  if (localVote.value === type) {
    localCount.value += type === 'up' ? -1 : 1
    localVote.value = null
  } else {
    if (localVote.value === 'up')   localCount.value--
    if (localVote.value === 'down') localCount.value++
    localCount.value += type === 'up' ? 1 : -1
    localVote.value = type
  }
  router.post(`/articles/${props.article.id}/vote`, { type }, { preserveScroll: true })
}
</script>
