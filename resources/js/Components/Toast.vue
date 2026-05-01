<template>
  <Transition name="toast">
    <div v-if="visible" class="toast" :class="`toast--${type}`" @click="visible = false">
      <svg v-if="type === 'success'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      {{ message }}
    </div>
  </Transition>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  message: String,
  type: { type: String, default: 'success' },
})

const visible = ref(false)
let timer = null

watch(() => props.message, (msg) => {
  if (!msg) return
  visible.value = true
  clearTimeout(timer)
  timer = setTimeout(() => { visible.value = false }, 3000)
}, { immediate: true })
</script>

<style scoped>
.toast {
  position: fixed;
  bottom: 24px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  border-radius: var(--radius-md);
  font-size: var(--text-base);
  font-weight: 500;
  cursor: pointer;
  z-index: var(--z-toast);
  box-shadow: var(--elevation-3);
  white-space: nowrap;
  font-family: var(--font-ui);
}
.toast--success { background: var(--color-gray-900); color: #fff; }
.toast--error   { background: var(--color-red-500); color: #fff; }

.toast-enter-active { transition: all 0.2s ease; }
.toast-leave-active { transition: all 0.25s ease; }
.toast-enter-from   { opacity: 0; transform: translateX(-50%) translateY(12px); }
.toast-leave-to     { opacity: 0; transform: translateX(-50%) translateY(12px); }
</style>
