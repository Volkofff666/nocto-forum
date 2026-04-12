<template>
  <div class="auth-page">
    <div class="auth-card">
      <div class="auth-card__title">Регистрация</div>

      <!-- Telegram -->
      <a href="/auth/telegram" class="btn-tg" style="width:100%;justify-content:center;margin-bottom:4px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
          <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.04 9.607c-.148.658-.537.818-1.084.508l-3-2.21-1.447 1.393c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.48 14.607l-2.95-.924c-.641-.202-.654-.641.136-.951l11.52-4.442c.534-.194 1.001.13.376.958z"/>
        </svg>
        Войти через Telegram
      </a>

      <div class="auth-divider">или</div>

      <form @submit.prevent="submit">
        <div class="form-group">
          <label class="form-label">Имя</label>
          <input
            v-model="form.name"
            type="text"
            class="form-input"
            :class="{ 'form-input--error': errors.name }"
            required
          />
          <div v-if="errors.name" class="form-error">{{ errors.name }}</div>
        </div>

        <div class="form-group">
          <label class="form-label">Логин</label>
          <input
            v-model="form.username"
            type="text"
            class="form-input"
            :class="{ 'form-input--error': errors.username }"
            required
          />
          <div v-if="errors.username" class="form-error">{{ errors.username }}</div>
        </div>

        <div class="form-group">
          <label class="form-label">Email</label>
          <input
            v-model="form.email"
            type="email"
            class="form-input"
            :class="{ 'form-input--error': errors.email }"
            required
          />
          <div v-if="errors.email" class="form-error">{{ errors.email }}</div>
        </div>

        <div class="form-group">
          <label class="form-label">Пароль</label>
          <input
            v-model="form.password"
            type="password"
            class="form-input"
            :class="{ 'form-input--error': errors.password }"
            required
          />
          <div v-if="errors.password" class="form-error">{{ errors.password }}</div>
        </div>

        <div class="form-group">
          <label class="form-label">Подтвердите пароль</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            class="form-input"
            required
          />
        </div>

        <button type="submit" class="btn-primary" style="width:100%;justify-content:center;">
          Зарегистрироваться
        </button>
      </form>

      <div class="auth-footer">
        Уже есть аккаунт? <Link href="/login">Войти</Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'

defineProps({
  errors: { type: Object, default: () => ({}) },
})

const form = ref({
  name:                  '',
  username:              '',
  email:                 '',
  password:              '',
  password_confirmation: '',
})

function submit() {
  router.post('/register', form.value)
}
</script>
