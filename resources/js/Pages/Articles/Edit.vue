<template>
  <AppLayout>
    <div class="page-wrap page-wrap--full" style="padding-top:20px;">
      <div class="content-card write-page">
        <div class="write-page__header">
          <span class="write-page__title">Редактировать статью</span>
          <Link :href="`/articles/${article.slug}`" class="btn btn-ghost btn-sm">✕ Отмена</Link>
        </div>

        <form @submit.prevent="submit">
          <!-- Title -->
          <input
            v-model="form.title"
            type="text"
            class="article-title-input"
            maxlength="255"
            placeholder="Заголовок статьи..."
          />
          <div v-if="errors.title" class="form-error" style="margin-bottom:12px;">{{ errors.title }}</div>

          <!-- Category -->
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;">
            <div>
              <label class="form-label">Категория</label>
              <select v-model="form.category" class="form-select">
                <option value="tech">Технологии</option>
                <option value="security">Безопасность</option>
                <option value="guides">Гайды</option>
                <option value="news">Новости</option>
                <option value="other">Другое</option>
              </select>
            </div>
          </div>

          <!-- Cover image -->
          <div class="form-group">
            <label class="form-label">Обложка <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--text-light);">— URL картинки (необязательно)</span></label>
            <input v-model="form.cover_url" type="url" class="form-input" placeholder="https://..." />
            <div v-if="form.cover_url" class="cover-preview">
              <img :src="form.cover_url" alt="Обложка" @error="e => e.target.style.display='none'" />
            </div>
            <div v-if="errors.cover_url" class="form-error">{{ errors.cover_url }}</div>
          </div>

          <!-- Excerpt -->
          <div class="form-group">
            <label class="form-label">Краткое описание</label>
            <textarea v-model="form.excerpt" class="form-textarea" rows="2" maxlength="500"></textarea>
            <div v-if="errors.excerpt" class="form-error">{{ errors.excerpt }}</div>
          </div>

          <!-- Body -->
          <div class="form-group">
            <label class="form-label">Текст статьи</label>
            <RichEditor v-model="form.body" />
            <div v-if="errors.body" class="form-error" style="margin-top:6px;">{{ errors.body }}</div>
          </div>

          <!-- Tags -->
          <div class="form-group">
            <label class="form-label">Теги</label>
            <TagInput v-model="form.tags" />
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <Link :href="`/articles/${article.slug}`" class="btn btn-outline">Отмена</Link>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import RichEditor from '@/Components/RichEditor.vue'
import TagInput from '@/Components/TagInput.vue'

const props = defineProps({
  article: Object,
  errors: { type: Object, default: () => ({}) },
})

const form = ref({
  title:     props.article.title,
  excerpt:   props.article.excerpt,
  body:      props.article.body,
  category:  props.article.category,
  cover_url: props.article.cover_url ?? '',
  tags:      props.article.tags ?? [],
})

function submit() { router.patch(`/articles/${props.article.id}`, form.value) }
</script>
