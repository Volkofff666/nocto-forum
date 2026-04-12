<template>
  <AppLayout>
    <div class="page-layout page-layout--full" style="max-width:760px;margin:0 auto;">
      <div style="padding:32px 0;">
        <h1 style="font-size:22px;font-weight:700;margin-bottom:24px;">Новая статья</h1>

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
              <option value="" disabled>Выберите категорию</option>
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
              placeholder="Короткое описание для анонса в ленте..."
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
              placeholder="Напишите статью здесь..."
              rows="16"
              @input="autoResize"
            ></textarea>
            <div v-if="errors.body" class="form-error">{{ errors.body }}</div>
          </div>

          <!-- Кнопки -->
          <div class="form-actions">
            <button type="submit" class="btn-outline" @click.prevent="saveDraft">
              Сохранить черновик
            </button>
            <button type="button" class="btn-primary" @click.prevent="saveAndPublish">
              Опубликовать
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  errors: { type: Object, default: () => ({}) },
})

const form = ref({
  title:    '',
  excerpt:  '',
  body:     '',
  category: '',
  publish:  false,
})

const bodyRef = ref(null)

function autoResize() {
  const el = bodyRef.value
  if (el) {
    el.style.height = 'auto'
    el.style.height = el.scrollHeight + 'px'
  }
}

function saveDraft() {
  form.value.publish = false
  submit()
}

function saveAndPublish() {
  form.value.publish = true
  submit()
}

function submit() {
  router.post('/articles', form.value)
}
</script>
