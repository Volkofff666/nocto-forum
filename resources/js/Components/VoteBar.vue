<template>
  <div class="vote-bar">
    <button
      class="vote-btn vote-btn--up"
      :class="{ 'vote-btn--active': currentVote === 'up' }"
      @click="vote('up')"
      :disabled="!canVote"
    >
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/>
        <path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
      </svg>
      За
    </button>

    <span class="vote-count" :style="countStyle">{{ localCount }}</span>

    <button
      class="vote-btn vote-btn--down"
      :class="{ 'vote-btn--active': currentVote === 'down' }"
      @click="vote('down')"
      :disabled="!canVote"
    >
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M10 15v4a3 3 0 0 0 3 3l4-9V2H5.72a2 2 0 0 0-2 1.7l-1.38 9a2 2 0 0 0 2 2.3H10z"/>
        <path d="M17 2h2.67A2.31 2.31 0 0 1 22 4v7a2.31 2.31 0 0 1-2.33 2H17"/>
      </svg>
      Против
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

const page = usePage()
const canVote = computed(() => !!page.props.auth?.user)
const currentVote = ref(props.userVote ?? null)
const localCount = ref(props.article.votes_count)

const countStyle = computed(() => ({
  color: localCount.value > 0 ? 'var(--accent)' : localCount.value < 0 ? '#dc3545' : 'var(--text)',
}))

function vote(type) {
  if (!canVote.value) {
    router.visit('/login')
    return
  }

  // Оптимистичное обновление
  if (currentVote.value === type) {
    localCount.value += type === 'up' ? -1 : 1
    currentVote.value = null
  } else {
    if (currentVote.value === 'up') localCount.value--
    if (currentVote.value === 'down') localCount.value++
    localCount.value += type === 'up' ? 1 : -1
    currentVote.value = type
  }

  router.post(`/articles/${props.article.id}/vote`, { type }, {
    preserveScroll: true,
  })
}
</script>
