<template>
  <button
    class="btn btn-sm"
    :class="localFollowing ? 'btn-following' : 'btn-primary'"
    :disabled="loading"
    @click="toggle"
  >
    {{ localFollowing ? 'Вы подписаны' : 'Подписаться' }}
  </button>
</template>

<script setup>
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  userId:      { type: Number, required: true },
  isFollowing: { type: Boolean, default: false },
})

const page           = usePage()
const localFollowing = ref(props.isFollowing)
const loading        = ref(false)

function toggle() {
  if (!page.props.auth?.user) {
    router.visit('/login')
    return
  }

  const prev = localFollowing.value
  localFollowing.value = !prev
  loading.value = true

  router.post(`/users/${props.userId}/follow`, {}, {
    preserveScroll: true,
    preserveState:  true,
    onError: () => { localFollowing.value = prev },
    onFinish: () => { loading.value = false },
  })
}
</script>
