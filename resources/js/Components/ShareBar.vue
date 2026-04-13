<template>
  <div class="share-bar">
    <span class="share-bar__label">Поделиться:</span>

    <a :href="tgUrl" target="_blank" rel="noopener" class="share-btn share-btn--tg" title="Telegram">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.04 9.607c-.148.658-.537.818-1.084.508l-3-2.21-1.447 1.393c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.48 14.607l-2.95-.924c-.641-.202-.654-.641.136-.951l11.52-4.442c.534-.194 1.001.13.376.958z"/></svg>
      Telegram
    </a>

    <button class="share-btn" :class="{ 'share-btn--copied': copied }" @click="copy" title="Копировать ссылку">
      <svg v-if="!copied" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
      <svg v-else width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
      {{ copied ? 'Скопировано!' : 'Копировать ссылку' }}
    </button>

    <template v-if="isAuth && articleId">
      <button v-if="!showReport" class="share-btn share-btn--report" @click="showReport = true">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Пожаловаться
      </button>
      <div v-else class="report-inline">
        <input v-model="reportReason" type="text" placeholder="Причина жалобы…" class="report-inline__input" />
        <button class="share-btn share-btn--danger" :disabled="!reportReason.trim()" @click="sendReport">Отправить</button>
        <button class="share-btn" @click="showReport = false; reportReason = ''">Отмена</button>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const props = defineProps({
  title:     String,
  url:       String,
  articleId: { type: Number, default: null },
})

const page   = usePage()
const isAuth = computed(() => !!page.props.auth?.user)

const copied       = ref(false)
const showReport   = ref(false)
const reportReason = ref('')

function sendReport() {
  router.post(`/report/article/${props.articleId}`, { reason: reportReason.value }, {
    preserveScroll: true,
    onSuccess: () => { showReport.value = false; reportReason.value = '' },
  })
}

const tgUrl = computed(() =>
  `https://t.me/share/url?url=${encodeURIComponent(props.url)}&text=${encodeURIComponent(props.title)}`
)

async function copy() {
  try {
    await navigator.clipboard.writeText(props.url)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
  } catch {
    // fallback
    const el = document.createElement('textarea')
    el.value = props.url
    document.body.appendChild(el)
    el.select()
    document.execCommand('copy')
    document.body.removeChild(el)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
  }
}
</script>

<style scoped>
.share-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}

.share-bar__label {
  font-size: 13px;
  color: var(--text-muted);
  margin-right: 2px;
}

.share-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: var(--radius);
  font-size: 13px;
  font-weight: 500;
  border: 1px solid var(--border-strong);
  background: var(--bg);
  color: var(--text);
  cursor: pointer;
  transition: all 0.12s;
  text-decoration: none;
}
.share-btn:hover { border-color: var(--accent); color: var(--accent); }
.share-btn--tg { border-color: #229ED9; color: #229ED9; }
.share-btn--tg:hover { background: rgba(34,158,217,0.08); }
.share-btn--copied { border-color: #28a745; color: #28a745; }
</style>
