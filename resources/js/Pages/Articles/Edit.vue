<template>
  <AppLayout>
    <div class="page-wrap page-wrap--full" style="padding-top:20px;">
      <div class="content-card write-page">
        <div class="write-page__header">
          <span class="write-page__title">Редактировать статью</span>
          <Link :href="`/articles/${article.slug}`" class="btn btn-ghost btn-sm">✕ Отмена</Link>
        </div>

        <form @submit.prevent="submit">
          <input
            v-model="form.title"
            type="text"
            class="article-title-input"
            maxlength="255"
          />
          <div v-if="errors.title" class="form-error" style="margin-bottom:12px;">{{ errors.title }}</div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;">
            <div>
              <label class="form-label">Категория</label>
              <select v-model="form.category" class="form-select">
                <option value="proxy">Прокси</option>
                <option value="vpn">VPN</option>
                <option value="security">Безопасность</option>
                <option value="tools">Инструменты</option>
                <option value="other">Другое</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Краткое описание</label>
            <textarea v-model="form.excerpt" class="form-textarea" rows="2" maxlength="500"></textarea>
            <div v-if="errors.excerpt" class="form-error">{{ errors.excerpt }}</div>
          </div>

          <div class="form-group">
            <label class="form-label">Текст статьи</label>
            <textarea
              v-model="form.body"
              ref="bodyEl"
              class="form-textarea"
              rows="18"
              @input="resize"
            ></textarea>
            <div v-if="errors.body" class="form-error">{{ errors.body }}</div>
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

const props = defineProps({
  article: Object,
  errors: { type: Object, default: () => ({}) },
})

const form = ref({
  title:    props.article.title,
  excerpt:  props.article.excerpt,
  body:     props.article.body,
  category: props.article.category,
})

const bodyEl = ref(null)
function resize() {
  const el = bodyEl.value
  if (el) { el.style.height = 'auto'; el.style.height = el.scrollHeight + 'px' }
}

function submit() { router.patch(`/articles/${props.article.id}`, form.value) }
</script>
