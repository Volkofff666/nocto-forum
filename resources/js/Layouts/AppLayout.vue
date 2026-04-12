<template>
  <div>
    <header class="header">
      <div class="container">
        <div class="header__inner">
          <Link href="/" class="header__logo">nocto<span>.</span>hub</Link>

          <nav class="header__nav" :class="{ 'header__nav--open': mobileNavOpen }">
            <Link href="/" :class="{ active: $page.url === '/' }">Лента</Link>
            <Link href="/?category=proxy" :class="{ active: $page.url.includes('category=proxy') }">Прокси</Link>
            <Link href="/?category=vpn" :class="{ active: $page.url.includes('category=vpn') }">VPN</Link>
            <Link href="/?category=tools" :class="{ active: $page.url.includes('category=tools') }">Инструменты</Link>
          </nav>

          <div class="header__actions">
            <template v-if="$page.props.auth?.user">
              <div class="user-menu" :class="{ 'user-menu--open': userMenuOpen }">
                <button class="user-menu__trigger" @click="userMenuOpen = !userMenuOpen">
                  <div class="avatar">
                    <img v-if="$page.props.auth.user.avatar_url" :src="$page.props.auth.user.avatar_url" :alt="$page.props.auth.user.name" />
                    <template v-else>{{ $page.props.auth.user.avatar }}</template>
                  </div>
                  <span>{{ $page.props.auth.user.username }}</span>
                </button>
                <div class="user-menu__dropdown">
                  <Link :href="`/profile/${$page.props.auth.user.username}`" @click="userMenuOpen = false">Профиль</Link>
                  <Link href="/articles/create" @click="userMenuOpen = false">Написать</Link>
                  <button @click="logout">Выйти</button>
                </div>
              </div>
            </template>
            <template v-else>
              <a href="/auth/telegram" class="btn-tg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.04 9.607c-.148.658-.537.818-1.084.508l-3-2.21-1.447 1.393c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.48 14.607l-2.95-.924c-.641-.202-.654-.641.136-.951l11.52-4.442c.534-.194 1.001.13.376.958z"/>
                </svg>
                Войти через Telegram
              </a>
              <Link href="/login" class="btn-outline">Войти</Link>
            </template>

            <button class="hamburger" @click="mobileNavOpen = !mobileNavOpen" aria-label="Меню">
              <span></span>
              <span></span>
              <span></span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <main>
      <div class="container">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const mobileNavOpen = ref(false)
const userMenuOpen = ref(false)

function logout() {
  userMenuOpen.value = false
  router.post('/logout')
}
</script>
