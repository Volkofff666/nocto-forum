<template>
  <div class="nb-wrap">

    <!-- Top bar -->
    <div class="nb-bar">
      <Link :href="`/articles/${article.slug}`" class="nb-bar__back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        <span class="nb-bar__back-label">К статье</span>
      </Link>
      <span class="nb-bar__brand">nocto<span>.</span>hub</span>
      <div class="nb-bar__actions">
        <Link :href="`/articles/${article.slug}`" class="btn btn-ghost btn-sm">Отмена</Link>
        <button class="btn btn-primary btn-sm" @click="submit" :disabled="saving">Сохранить</button>
      </div>
    </div>

    <!-- Editor card -->
    <div class="nb-doc">
      <ArticleEditor
        v-model:title="form.title"
        v-model:excerpt="form.excerpt"
        v-model:body="form.body"
        v-model:tags="form.tags"
        v-model:coverUrl="form.cover_url"
        :errors="errors"
      />

      <!-- Meta row -->
      <div class="nb-meta">
        <div class="nb-meta__field">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
          <select v-model="form.category" class="nb-meta__select">
            <option value="" disabled>Категория</option>
            <option value="tech">Технологии</option>
            <option value="security">Безопасность</option>
            <option value="guides">Гайды</option>
            <option value="news">Новости</option>
            <option value="other">Другое</option>
          </select>
        </div>
        <div class="nb-meta__sep"></div>
        <div class="nb-meta__field nb-meta__field--grow">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
          <input
            v-model="form.cover_url"
            type="url"
            class="nb-meta__input"
            placeholder="URL обложки (необязательно)..."
          />
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import ArticleEditor from '@/Components/ArticleEditor.vue'

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

const saving = ref(false)

function submit() {
  saving.value = true
  router.patch(`/articles/${props.article.id}`, form.value, {
    onFinish: () => { saving.value = false },
  })
}
</script>

<style scoped>
.nb-wrap {
  min-height: 100vh;
  background: var(--bg);
  display: flex;
  flex-direction: column;
}

.nb-bar {
  position: sticky;
  top: 0;
  z-index: 50;
  background: var(--surface);
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 0 20px;
  height: 52px;
}

.nb-bar__back {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.875rem;
  color: var(--text-muted);
  text-decoration: none;
  flex-shrink: 0;
}
.nb-bar__back:hover { color: var(--text); }

.nb-bar__brand {
  font-size: 1rem;
  font-weight: 700;
  color: var(--text);
  letter-spacing: -0.02em;
  margin-right: auto;
}
.nb-bar__brand span { color: var(--primary); }

.nb-bar__actions {
  display: flex;
  gap: 8px;
  flex-shrink: 0;
}

.nb-doc {
  max-width: 800px;
  width: 100%;
  margin: 32px auto 60px;
  padding: 0 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.nb-meta {
  display: flex;
  align-items: center;
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 10px;
  overflow: hidden;
  height: 44px;
}

.nb-meta__field {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 0 16px;
  color: var(--text-muted);
  height: 100%;
  flex-shrink: 0;
}
.nb-meta__field--grow { flex: 1; min-width: 0; }

.nb-meta__sep {
  width: 1px;
  height: 60%;
  background: var(--border);
  flex-shrink: 0;
}

.nb-meta__select,
.nb-meta__input {
  border: none;
  outline: none;
  background: transparent;
  font-size: 0.83rem;
  color: var(--text);
  font-family: inherit;
  padding: 0;
  min-width: 0;
  width: 100%;
}
.nb-meta__select { max-width: 140px; cursor: pointer; }
.nb-meta__input::placeholder { color: var(--text-muted); }

@media (max-width: 600px) {
  .nb-bar { padding: 0 12px; }
  .nb-bar__back-label { display: none; }
  .nb-doc { margin: 16px auto 40px; }
  .nb-meta { height: auto; flex-direction: column; align-items: stretch; }
  .nb-meta__field { height: 40px; }
  .nb-meta__sep { width: 100%; height: 1px; }
  .nb-meta__select { max-width: 100%; }
}
</style>
