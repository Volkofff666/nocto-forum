<template>
  <AppLayout>
    <div class="page-wrap page-wrap--full" style="padding-top:20px;">
      <div class="content-card">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);">
          <h1 style="font-size:18px;font-weight:700;">Настройки профиля</h1>
        </div>

        <div style="padding:24px;">
          <form @submit.prevent="submit">
            <!-- Cover preview -->
            <div style="margin-bottom:28px;padding-bottom:24px;border-bottom:1px solid var(--border);">
              <div class="settings-cover-preview" :style="form.cover_url ? `background-image:url('${form.cover_url}')` : ''">
                <div class="settings-cover-preview__empty" v-if="!form.cover_url || coverError">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.4"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                  <span>Фон профиля</span>
                </div>
              </div>
              <div class="form-group" style="margin-top:12px;margin-bottom:0;">
                <label class="form-label">Фон профиля</label>
                <input
                  v-model="form.cover_url"
                  type="url"
                  class="form-input"
                  :class="{ 'form-input--error': errors.cover_url }"
                  placeholder="https://example.com/background.jpg"
                  @input="coverError = false"
                />
                <div v-if="errors.cover_url" class="form-error">{{ errors.cover_url }}</div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Вставьте URL картинки — она будет показана как шапка профиля</div>
              </div>
            </div>

            <!-- Sidebar image preview -->
            <div style="margin-bottom:28px;padding-bottom:24px;border-bottom:1px solid var(--border);">
              <div class="settings-cover-preview" :style="form.sidebar_image_url ? `background-image:url('${form.sidebar_image_url}')` : ''">
                <div class="settings-cover-preview__empty" v-if="!form.sidebar_image_url || sidebarError">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.4"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                  <span>Баннер сайдбара</span>
                </div>
              </div>
              <div class="form-group" style="margin-top:12px;margin-bottom:0;">
                <label class="form-label">Баннер сайдбара</label>
                <input
                  v-model="form.sidebar_image_url"
                  type="url"
                  class="form-input"
                  :class="{ 'form-input--error': errors.sidebar_image_url }"
                  placeholder="https://example.com/banner.jpg"
                  @input="sidebarError = false"
                />
                <div v-if="errors.sidebar_image_url" class="form-error">{{ errors.sidebar_image_url }}</div>
                <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Картинка будет показана в сайдбаре главной страницы</div>
              </div>
            </div>

            <!-- Avatar preview -->
            <div style="display:flex;align-items:center;gap:16px;margin-bottom:28px;padding-bottom:24px;border-bottom:1px solid var(--border);">
              <div class="avatar avatar--80">
                <img v-if="form.avatar_url" :src="form.avatar_url" :alt="form.name" @error="imgError = true" />
                <template v-if="!form.avatar_url || imgError">{{ initials }}</template>
              </div>
              <div style="flex:1;">
                <div style="font-size:14px;font-weight:600;margin-bottom:6px;">Фото профиля</div>
                <div class="form-group" style="margin-bottom:0;">
                  <input
                    v-model="form.avatar_url"
                    type="url"
                    class="form-input"
                    :class="{ 'form-input--error': errors.avatar_url }"
                    placeholder="https://example.com/photo.jpg"
                    @input="imgError = false"
                  />
                  <div v-if="errors.avatar_url" class="form-error">{{ errors.avatar_url }}</div>
                  <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">Вставьте URL изображения</div>
                </div>
              </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
              <div class="form-group">
                <label class="form-label">Имя</label>
                <input v-model="form.name" type="text" class="form-input" :class="{ 'form-input--error': errors.name }" maxlength="255" required />
                <div v-if="errors.name" class="form-error">{{ errors.name }}</div>
              </div>

              <div class="form-group">
                <label class="form-label">Логин <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--text-light);">— только латиница, цифры, _</span></label>
                <div style="position:relative;">
                  <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:15px;">@</span>
                  <input
                    v-model="form.username"
                    type="text"
                    class="form-input"
                    :class="{ 'form-input--error': errors.username }"
                    style="padding-left:28px;"
                    maxlength="32"
                    required
                  />
                </div>
                <div v-if="errors.username" class="form-error">{{ errors.username }}</div>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">О себе</label>
              <textarea
                v-model="form.bio"
                class="form-textarea"
                rows="3"
                maxlength="500"
                placeholder="Расскажите о себе..."
              ></textarea>
              <div style="display:flex;justify-content:flex-end;font-size:12px;color:var(--text-muted);margin-top:4px;">
                {{ (form.bio || '').length }}/500
              </div>
              <div v-if="errors.bio" class="form-error">{{ errors.bio }}</div>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn btn-primary" :disabled="saving">
                {{ saving ? 'Сохраняем...' : 'Сохранить изменения' }}
              </button>
              <Link :href="`/profile/${user.username}`" class="btn btn-outline">Посмотреть профиль</Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  user: Object,
  errors: { type: Object, default: () => ({}) },
})

const form = ref({
  name:              props.user.name,
  username:          props.user.username,
  bio:               props.user.bio || '',
  avatar_url:        props.user.avatar_url || '',
  cover_url:         props.user.cover_url || '',
  sidebar_image_url: props.user.sidebar_image_url || '',
})

const saving      = ref(false)
const imgError    = ref(false)
const coverError  = ref(false)
const sidebarError = ref(false)

const initials = computed(() => {
  const words = (form.value.name || '').trim().split(' ')
  return words.slice(0, 2).map(w => w[0]?.toUpperCase() || '').join('')
})

function submit() {
  saving.value = true
  router.patch('/settings', form.value, {
    onFinish: () => { saving.value = false },
  })
}
</script>

<style scoped>
.settings-cover-preview {
  width: 100%;
  height: 140px;
  border-radius: var(--radius-lg);
  border: 1px solid var(--border);
  background-color: var(--bg-secondary);
  background-size: cover;
  background-position: center;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.settings-cover-preview__empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  color: var(--text-muted);
  font-size: 12px;
}
</style>
