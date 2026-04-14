<template>
  <div class="editor-shell">

    <!-- Top bar -->
    <div class="editor-bar">
      <Link :href="`/articles/${article.slug}`" class="editor-bar__back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        <span class="editor-bar__back-label">К статье</span>
      </Link>
      <span class="editor-bar__brand">nocto<span>.</span>hub</span>
      <div class="editor-bar__actions">
        <Link :href="`/articles/${article.slug}`" class="btn btn-ghost btn-sm">Отмена</Link>
        <button class="btn btn-primary btn-sm" @click="submit" :disabled="saving">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
          Сохранить
        </button>
      </div>
    </div>

    <!-- Body: editor + sidebar -->
    <div class="editor-body">

      <!-- Main editor area -->
      <div class="editor-main">
        <ArticleEditor
          v-model:title="form.title"
          v-model:excerpt="form.excerpt"
          v-model:body="form.body"
          v-model:tags="form.tags"
          v-model:coverUrl="form.cover_url"
          :errors="errors"
        />
      </div>

      <!-- Settings sidebar -->
      <aside class="editor-side">

        <!-- Category -->
        <div class="editor-side__block">
          <div class="editor-side__title">Категория</div>
          <div class="editor-cats">
            <button
              v-for="cat in CATEGORIES" :key="cat.value"
              class="editor-cat"
              :class="{ active: form.category === cat.value }"
              @click="form.category = cat.value"
            >{{ cat.label }}</button>
          </div>
        </div>

        <!-- Cover URL -->
        <div class="editor-side__block">
          <div class="editor-side__title">Обложка</div>
          <input
            v-model="form.cover_url"
            type="url"
            class="editor-side__input"
            placeholder="https://example.com/cover.jpg"
          />
          <div v-if="form.cover_url" class="editor-cover-preview">
            <img :src="form.cover_url" alt="preview" @error="e => e.target.style.display='none'" />
          </div>
        </div>

        <!-- Status -->
        <div class="editor-side__block">
          <div class="editor-side__title">Статус</div>
          <div style="font-size:13px;color:var(--text-muted);">
            <span class="status-pill" :class="`status-pill--${article.status}`">
              {{ article.status === 'published' ? 'Опубликовано' : 'Черновик' }}
            </span>
          </div>
        </div>

      </aside>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import ArticleEditor from '@/Components/ArticleEditor.vue'
import { CATEGORIES } from '@/composables/useCategories'

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
.editor-shell {
  min-height: 100vh;
  background: var(--bg-secondary);
  display: flex;
  flex-direction: column;
}

.editor-bar {
  position: sticky;
  top: 0;
  z-index: 100;
  background: var(--bg);
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 0 20px;
  height: 52px;
  box-shadow: 0 1px 8px rgba(0,0,0,0.04);
}

.editor-bar__back {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 13px;
  color: var(--text-muted);
  text-decoration: none;
  flex-shrink: 0;
  transition: color 0.12s;
}
.editor-bar__back:hover { color: var(--text); }
.editor-bar__back-label { display: none; }
@media (min-width: 600px) { .editor-bar__back-label { display: inline; } }

.editor-bar__brand {
  font-size: 15px;
  font-weight: 800;
  letter-spacing: -0.5px;
  color: var(--text);
  margin-right: auto;
}
.editor-bar__brand span { color: var(--accent); }

.editor-bar__actions { display: flex; gap: 8px; flex-shrink: 0; }

.editor-body {
  display: grid;
  grid-template-columns: 1fr 260px;
  gap: 20px;
  max-width: 1100px;
  width: 100%;
  margin: 24px auto;
  padding: 0 16px 60px;
  align-items: start;
}

@media (max-width: 860px) {
  .editor-body { grid-template-columns: 1fr; }
  .editor-side { order: -1; }
}

.editor-main { min-width: 0; }

.editor-side {
  display: flex;
  flex-direction: column;
  gap: 12px;
  position: sticky;
  top: 68px;
}

.editor-side__block {
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 16px;
}

.editor-side__title {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: var(--text-muted);
  margin-bottom: 12px;
}

.editor-side__input {
  width: 100%;
  padding: 8px 10px;
  border: 1px solid var(--border-strong);
  border-radius: var(--radius);
  font-size: 13px;
  background: var(--bg-secondary);
  color: var(--text);
  font-family: inherit;
}
.editor-side__input:focus { outline: none; border-color: var(--accent); }
.editor-side__input::placeholder { color: var(--text-muted); font-size: 12px; }

.editor-cats { display: flex; flex-wrap: wrap; gap: 6px; }

.editor-cat {
  padding: 5px 12px;
  border-radius: 20px;
  border: 1px solid var(--border-strong);
  background: var(--bg-secondary);
  color: var(--text-muted);
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.12s;
  font-family: inherit;
}
.editor-cat:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
.editor-cat.active { border-color: var(--accent); color: var(--accent); background: var(--accent-light); font-weight: 700; }

.editor-cover-preview {
  margin-top: 10px;
  border-radius: var(--radius);
  overflow: hidden;
  border: 1px solid var(--border);
  max-height: 120px;
}
.editor-cover-preview img {
  width: 100%;
  height: 120px;
  object-fit: cover;
  display: block;
}
</style>
