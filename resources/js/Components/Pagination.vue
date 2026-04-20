<template>
  <div v-if="paginator.last_page > 1" class="pagination">
    <button
      v-for="link in paginator.links"
      :key="link.label"
      class="page-btn"
      :class="{ 'page-btn--active': link.active }"
      :disabled="!link.url"
      @click="link.url && router.get(link.url)"
    >{{ pageLabel(link.label) }}</button>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'

defineProps({ paginator: Object })

function pageLabel(label) {
  if (label.includes('laquo')) return '‹'
  if (label.includes('raquo')) return '›'
  return label
}
</script>
