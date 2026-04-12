<template>
  <AppLayout>
    <div class="page-layout page-layout--full" style="max-width:760px;margin:0 auto;">
      <div style="padding:32px 0;">
        <h1 style="font-size:22px;font-weight:700;margin-bottom:24px;">Редактировать статью</h1>

        <form @submit.prevent="submit">
          <!-- Заголовок -->
          <input
            v-model="form.title"
            type="text"
            class="article-title-input"
            placeholder="Заголовок статьи..."
            maxlength="255"
          />
          <div v-if="errors.title" class="form-error" style="margin-bottom:12px;">{{ errors.title }}</div>

          <!-- Категория -->
          <div class="form-group">
            <label class="form-label">Категория</label>
            <select v-model="form.category" class="form-select">
              <option value="proxy">Прокси</option>
              <option value="vpn">VPN</option>
              <option value="security">Безопасность</option>
              <option value="tools">Инструменты</option>
              <option value="other">Другое</option>
            </select>
            <div v-if="errors.category" class="form-error">{{ errors.category }}</div>
          </div>

          <!-- Краткое описание -->
          <div class="form-group">
            <label class="form-label">Краткое описание</label>
            <textarea
              v-model="form.excerpt"
              class="form-textarea"
              rows="2"
              maxlength="500"
            ></textarea>
            <div v-if="errors.excerpt" class="form-error">{{ errors.excerpt }}</div>
          </div>

          <!-- Тело -->
          <div class="form-group">
            <label class="form-label">Текст статьи</label>
            <textarea
              v-model="form.body"
              ref="bodyRef"
              class="form-textarea"
              rows="16"
              @input="autoResize"
            ></textarea>
            <div v-if="errors.body" class="form-error">{{ errors.body }}</div>
          </div>

          <!-- Кнопки -->
          <div class="form-actions">
            <Link :href="`/articles/${article.slug}`" class="btn-outline">Отмена</Link>
            <button type="submit" class="btn-primary">Сохранить</button>
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

const bodyRef = ref(null)

function autoResize() {
  const el = bodyRef.value
  if (el) {
    el.style.height = 'auto'
    el.style.height = el.scrollHeight + 'px'
  }
}

function submit() {
  router.patch(`/articles/${props.article.id}`, form.value)
}
</script>
