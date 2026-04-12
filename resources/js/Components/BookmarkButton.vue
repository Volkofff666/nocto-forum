<template>
  <button
    type="button"
    class="stat-btn bookmark-btn"
    :class="{ 'bookmark-btn--active': local }"
    @click.prevent="toggle"
    :title="local ? 'Убрать из закладок' : 'Сохранить в закладки'"
  >
    <svg width="14" height="14" viewBox="0 0 24 24" :fill="local ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2">
      <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>
    </svg>
    {{ local ? 'Сохранено' : 'Сохранить' }}
  </button>
</template>

<script setup>
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  articleId:    { type: Number, required: true },
  isBookmarked: { type: Boolean, default: false },
})

const page  = usePage()
const local = ref(props.isBookmarked)

function toggle() {
  if (!page.props.auth?.user) {
    router.get('/login')
    return
  }
  local.value = !local.value
  router.post(`/articles/${props.articleId}/bookmark`, {}, {
    preserveState:  true,
    preserveScroll: true,
    onError: () => { local.value = !local.value }, // revert on error
  })
}
</script>
