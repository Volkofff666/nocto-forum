<template>
  <AppLayout>
    <div class="page-wrap page-wrap--full" style="padding-top:20px;">
      <div class="content-card write-page">
        <div class="write-page__header">
          <span class="write-page__title">Новая статья</span>
          <Link href="/" class="btn btn-ghost btn-sm">✕ Отмена</Link>
        </div>

        <form @submit.prevent>
          <!-- Title -->
          <input
            v-model="form.title"
            type="text"
            class="article-title-input"
            placeholder="Заголовок статьи..."
            maxlength="255"
          />
          <div v-if="errors.title" class="form-error" style="margin-bottom:12px;">{{ errors.title }}</div>

          <!-- Category -->
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;">
            <div>
              <label class="form-label">Категория</label>
              <select v-model="form.category" class="form-select">
                <option value="" disabled>Выбрать...</option>
                <option value="tech">Технологии</option>
                <option value="security">Безопасность</option>
                <option value="guides">Гайды</option>
                <option value="news">Новости</option>
                <option value="other">Другое</option>
              </select>
              <div v-if="errors.category" class="form-error">{{ errors.category }}</div>
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
            <label class="form-label">Краткое описание <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--text-light);">— покажется в ленте</span></label>
            <textarea v-model="form.excerpt" class="form-textarea" rows="2" maxlength="500" placeholder="Одно–два предложения о чём статья..."></textarea>
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
            <button type="button" class="btn btn-outline" @click="saveDraft">Сохранить черновик</button>
            <button type="button" class="btn btn-primary" @click="savePublish">Опубликовать</button>
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

defineProps({ errors: { type: Object, default: () => ({}) } })

const form = ref({ title: '', excerpt: '', body: '', category: '', cover_url: '', tags: [], publish: false })

function saveDraft()   { form.value.publish = false; router.post('/articles', form.value) }
function savePublish() { form.value.publish = true;  router.post('/articles', form.value) }
</script>
