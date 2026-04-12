<template>
  <div class="auth-wrap">
    <div class="auth-box">
      <div class="auth-box__logo">nocto<span>.</span>hub</div>

      <a href="/auth/telegram" class="btn btn-tg" style="width:100%;justify-content:center;gap:8px;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.04 9.607c-.148.658-.537.818-1.084.508l-3-2.21-1.447 1.393c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.48 14.607l-2.95-.924c-.641-.202-.654-.641.136-.951l11.52-4.442c.534-.194 1.001.13.376.958z"/></svg>
        Зарегистрироваться через Telegram
      </a>

      <div class="auth-divider">или по email</div>

      <form @submit.prevent="submit">
        <div class="form-group">
          <label class="form-label">Имя</label>
          <input v-model="form.name" type="text" class="form-input" :class="{ 'form-input--error': errors.name }" required />
          <div v-if="errors.name" class="form-error">{{ errors.name }}</div>
        </div>

        <div class="form-group">
          <label class="form-label">Логин <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--text-light);">— только латиница, цифры, _</span></label>
          <input v-model="form.username" type="text" class="form-input" :class="{ 'form-input--error': errors.username }" required />
          <div v-if="errors.username" class="form-error">{{ errors.username }}</div>
        </div>

        <div class="form-group">
          <label class="form-label">Email</label>
          <input v-model="form.email" type="email" class="form-input" :class="{ 'form-input--error': errors.email }" required />
          <div v-if="errors.email" class="form-error">{{ errors.email }}</div>
        </div>

        <div class="form-group">
          <label class="form-label">Пароль</label>
          <input v-model="form.password" type="password" class="form-input" :class="{ 'form-input--error': errors.password }" required />
          <div v-if="errors.password" class="form-error">{{ errors.password }}</div>
        </div>

        <div class="form-group">
          <label class="form-label">Подтвердите пароль</label>
          <input v-model="form.password_confirmation" type="password" class="form-input" required />
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Создать аккаунт</button>
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

defineProps({ errors: { type: Object, default: () => ({}) } })

const form = ref({ name: '', username: '', email: '', password: '', password_confirmation: '' })
function submit() { router.post('/register', form.value) }
</script>
