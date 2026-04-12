<template>
  <div class="sparkline">
    <svg :viewBox="`0 0 ${width} ${height}`" preserveAspectRatio="none" class="sparkline__svg">
      <defs>
        <linearGradient :id="`grad-${uid}`" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" :stop-color="color" stop-opacity="0.3" />
          <stop offset="100%" :stop-color="color" stop-opacity="0" />
        </linearGradient>
      </defs>
      <path :d="areaPath" :fill="`url(#grad-${uid})`" />
      <path :d="linePath" :stroke="color" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
    <div class="sparkline__labels">
      <span v-for="(item, i) in data" :key="i" class="sparkline__label">{{ item.label }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  data:   { type: Array, default: () => [] },
  color:  { type: String, default: '#3b82f6' },
})

const uid   = Math.random().toString(36).slice(2)
const width = 300
const height = 60
const pad   = 4

const points = computed(() => {
  if (!props.data.length) return []
  const max = Math.max(...props.data.map(d => d.count), 1)
  return props.data.map((d, i) => ({
    x: pad + (i / (props.data.length - 1 || 1)) * (width - pad * 2),
    y: pad + (1 - d.count / max) * (height - pad * 2),
  }))
})

const linePath = computed(() => {
  if (!points.value.length) return ''
  return points.value.map((p, i) => `${i === 0 ? 'M' : 'L'}${p.x},${p.y}`).join(' ')
})

const areaPath = computed(() => {
  if (!points.value.length) return ''
  const line = linePath.value
  const last = points.value[points.value.length - 1]
  const first = points.value[0]
  return `${line} L${last.x},${height} L${first.x},${height} Z`
})
</script>

<style scoped>
.sparkline { padding: 8px 0; }
.sparkline__svg { width: 100%; height: 60px; display: block; }
.sparkline__labels {
  display: flex;
  justify-content: space-between;
  margin-top: 4px;
}
.sparkline__label {
  font-size: 11px;
  color: var(--text-muted);
  flex: 1;
  text-align: center;
}
</style>
