<template>
  <div class="notebook">
    <!-- Top bar -->
    <div class="notebook__bar">
      <Link :href="`/articles/${article.slug}`" class="notebook__back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        К статье
      </Link>
      <span class="notebook__brand">nocto<span>.</span>hub</span>
      <div class="notebook__bar-actions">
        <Link :href="`/articles/${article.slug}`" class="btn btn-ghost btn-sm">Отмена</Link>
        <button class="btn btn-primary btn-sm" @click="submit" :disabled="saving">Сохранить</button>
      </div>
    </div>

    <!-- Document -->
    <div class="notebook__doc">

      <!-- Cover preview -->
      <div v-if="form.cover_url && coverValid" class="notebook__cover">
        <img :src="form.cover_url" alt="Обложка" @error="coverValid = false" @load="coverValid = true" />
        <button class="notebook__cover-remove" @click="form.cover_url = ''; coverValid = true" title="Убрать обложку">×</button>
      </div>

      <!-- Title -->
      <textarea
        ref="titleEl"
        v-model="form.title"
        class="notebook__title"
        placeholder="Заголовок статьи..."
        maxlength="255"
        rows="1"
        @input="autoResize($event.target)"
        @keydown.enter.prevent="focusExcerpt"
      ></textarea>
      <div v-if="errors.title" class="nb-error">{{ errors.title }}</div>

      <!-- Excerpt -->
      <textarea
        ref="excerptEl"
        v-model="form.excerpt"
        class="notebook__excerpt"
        placeholder="Краткое описание — покажется в ленте..."
        maxlength="500"
        rows="1"
        @input="autoResize($event.target)"
      ></textarea>
      <div v-if="errors.excerpt" class="nb-error">{{ errors.excerpt }}</div>

      <!-- Divider -->
      <div class="notebook__divider"></div>

      <!-- Body -->
      <div class="notebook__body">
        <RichEditor v-model="form.body" />
      </div>
      <div v-if="errors.body" class="nb-error" style="margin-top:8px;">{{ errors.body }}</div>

    </div>

    <!-- Meta bar (sticky bottom) -->
    <div class="notebook__meta">
      <div class="notebook__meta-inner">

        <!-- Category -->
        <div class="nb-meta-field">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
          <select v-model="form.category" class="nb-meta-select">
            <option value="" disabled>Категория</option>
            <option value="tech">Технологии</option>
            <option value="security">Безопасность</option>
            <option value="guides">Гайды</option>
            <option value="news">Новости</option>
            <option value="other">Другое</option>
          </select>
        </div>

        <!-- Cover URL -->
        <div class="nb-meta-field">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          <input
            v-model="form.cover_url"
            type="url"
            class="nb-meta-input"
            placeholder="Обложка (URL картинки...)"
            @blur="coverValid = true"
          />
        </div>

        <!-- Tags -->
        <div class="nb-meta-field nb-meta-field--tags">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
          <TagInput v-model="form.tags" :compact="true" />
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import RichEditor from '@/Components/RichEditor.vue'
import TagInput from '@/Components/TagInput.vue'

const props = defineProps({
  article: Object,
  errors:  { type: Object, default: () => ({}) },
})

const form = ref({
  title:     props.article.title,
  excerpt:   props.article.excerpt,
  body:      props.article.body,
  category:  props.article.category,
  cover_url: props.article.cover_url ?? '',
  tags:      props.article.tags ?? [],
})

const saving     = ref(false)
const coverValid = ref(true)
const titleEl    = ref(null)
const excerptEl  = ref(null)

onMounted(() => {
  nextTick(() => {
    if (titleEl.value)   autoResize(titleEl.value)
    if (excerptEl.value) autoResize(excerptEl.value)
  })
})

function autoResize(el) {
  el.style.height = 'auto'
  el.style.height = el.scrollHeight + 'px'
}

function focusExcerpt() {
  excerptEl.value?.focus()
}

function submit() {
  saving.value = true
  router.patch(`/articles/${props.article.id}`, form.value, {
    onFinish: () => { saving.value = false },
  })
}
</script>

<style scoped>
.notebook {
  min-height: 100vh;
  background: var(--bg);
  display: flex;
  flex-direction: column;
}

.notebook__bar {
  position: sticky;
  top: 0;
  z-index: 100;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 0 24px;
  height: 52px;
}

.notebook__back {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.875rem;
  color: var(--text-muted);
  text-decoration: none;
  transition: color 0.15s;
}
.notebook__back:hover { color: var(--text); }

.notebook__brand {
  font-size: 1rem;
  font-weight: 700;
  color: var(--text);
  letter-spacing: -0.02em;
  margin-right: auto;
}
.notebook__brand span { color: var(--primary); }

.notebook__bar-actions {
  display: flex;
  gap: 8px;
}

.notebook__doc {
  flex: 1;
  max-width: 760px;
  width: 100%;
  margin: 0 auto;
  padding: 48px 24px 120px;
}

.notebook__cover {
  position: relative;
  margin-bottom: 32px;
  border-radius: 12px;
  overflow: hidden;
}
.notebook__cover img {
  width: 100%;
  max-height: 400px;
  object-fit: cover;
  display: block;
}
.notebook__cover-remove {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: rgba(0,0,0,0.5);
  color: #fff;
  border: none;
  cursor: pointer;
  font-size: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
}
.notebook__cover-remove:hover { background: rgba(0,0,0,0.75); }

.notebook__title {
  width: 100%;
  border: none;
  outline: none;
  background: transparent;
  font-size: 2.2rem;
  font-weight: 700;
  color: var(--text);
  line-height: 1.25;
  resize: none;
  overflow: hidden;
  padding: 0;
  margin-bottom: 16px;
  font-family: inherit;
}
.notebook__title::placeholder { color: var(--text-muted); opacity: 0.5; }

.notebook__excerpt {
  width: 100%;
  border: none;
  outline: none;
  background: transparent;
  font-size: 1.05rem;
  color: var(--text-muted);
  line-height: 1.6;
  resize: none;
  overflow: hidden;
  padding: 0;
  margin-bottom: 24px;
  font-family: inherit;
}
.notebook__excerpt::placeholder { color: var(--text-muted); opacity: 0.5; }

.notebook__divider {
  height: 1px;
  background: var(--border);
  margin-bottom: 28px;
}

.notebook__body {
  font-size: 1rem;
  line-height: 1.8;
}

.nb-error {
  font-size: 0.8rem;
  color: #dc3545;
  margin-top: -8px;
  margin-bottom: 12px;
}

.notebook__meta {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  background: var(--surface);
  border-top: 1px solid var(--border);
  z-index: 100;
  padding: 0 24px;
}

.notebook__meta-inner {
  max-width: 760px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  height: 48px;
}

.nb-meta-field {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 0 14px;
  border-right: 1px solid var(--border);
  height: 100%;
  color: var(--text-muted);
  flex-shrink: 0;
}
.nb-meta-field:first-child { padding-left: 0; }
.nb-meta-field--tags {
  border-right: none;
  flex: 1;
  min-width: 0;
}

.nb-meta-select,
.nb-meta-input {
  border: none;
  outline: none;
  background: transparent;
  font-size: 0.82rem;
  color: var(--text);
  font-family: inherit;
  cursor: pointer;
  padding: 0;
}
.nb-meta-select { max-width: 130px; }
.nb-meta-input { width: 200px; }
.nb-meta-input::placeholder { color: var(--text-muted); }
</style>
