<template>
  <div class="auth-wrap">
    <div class="auth-box">
      <div class="auth-box__logo">nocto<span>.</span>hub</div>

      <a href="/auth/telegram" class="btn btn-tg" style="width:100%;justify-content:center;gap:8px;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.04 9.607c-.148.658-.537.818-1.084.508l-3-2.21-1.447 1.393c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.48 14.607l-2.95-.924c-.641-.202-.654-.641.136-.951l11.52-4.442c.534-.194 1.001.13.376.958z"/></svg>
        Войти через Telegram
      </a>

      <div class="auth-divider">или по email</div>

      <div v-if="status" style="color:#155724;background:#d4edda;padding:10px 12px;border-radius:var(--radius);font-size:14px;margin-bottom:14px;">
        {{ status }}
      </div>

      <form @submit.prevent="submit">
        <div class="form-group">
          <label class="form-label">Email</label>
          <input v-model="form.email" type="email" class="form-input" :class="{ 'form-input--error': errors.email }" autocomplete="email" required />
          <div v-if="errors.email" class="form-error">{{ errors.email }}</div>
        </div>

        <div class="form-group">
          <label class="form-label">Пароль</label>
          <input v-model="form.password" type="password" class="form-input" :class="{ 'form-input--error': errors.password }" autocomplete="current-password" required />
          <div v-if="errors.password" class="form-error">{{ errors.password }}</div>
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Войти</button>
      </form>

      <div class="auth-footer">
        Нет аккаунта? <Link href="/register">Зарегистрироваться</Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'

defineProps({ status: String, errors: { type: Object, default: () => ({}) } })

const form = ref({ email: '', password: '' })
function submit() { router.post('/login', form.value) }
</script>
