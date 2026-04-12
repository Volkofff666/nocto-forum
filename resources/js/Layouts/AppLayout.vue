<template>
  <div>
    <Toast :message="$page.props.flash?.success" type="success" />
    <Toast :message="$page.props.flash?.error" type="error" />
    <header class="header">
      <div class="container">
        <div class="header__inner">
          <Link href="/" class="header__logo">nocto<span>.</span>hub</Link>

          <nav class="header__nav">
            <Link href="/" class="header__nav-link" :class="{ active: isHome }">Лента</Link>
            <Link href="/?category=proxy" class="header__nav-link" :class="{ active: isCategory('proxy') }">Прокси</Link>
            <Link href="/?category=vpn" class="header__nav-link" :class="{ active: isCategory('vpn') }">VPN</Link>
            <Link href="/?category=security" class="header__nav-link" :class="{ active: isCategory('security') }">Безопасность</Link>
            <Link href="/?category=tools" class="header__nav-link" :class="{ active: isCategory('tools') }">Инструменты</Link>
          </nav>

          <div class="header__right">
            <template v-if="user">
              <Link href="/articles/create" class="btn btn-primary btn-sm">Написать</Link>

              <div class="user-menu" :class="{ 'user-menu--open': menuOpen }" v-click-outside="closeMenu">
                <button class="user-menu__trigger" @click="menuOpen = !menuOpen">
                  <div class="avatar avatar--32">
                    <img v-if="user.avatar_url" :src="user.avatar_url" :alt="user.name" />
                    <template v-else>{{ user.avatar }}</template>
                  </div>
                  <span>{{ user.username }}</span>
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
                </button>

                <div class="user-menu__dropdown">
                  <Link :href="`/profile/${user.username}`" class="user-menu__item" @click="menuOpen = false">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Профиль
                  </Link>
                  <Link href="/articles/create" class="user-menu__item" @click="menuOpen = false">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Написать статью
                  </Link>
                  <Link href="/my/drafts" class="user-menu__item" @click="menuOpen = false">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Черновики
                  </Link>
                  <Link href="/my/bookmarks" class="user-menu__item" @click="menuOpen = false">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
                    Закладки
                  </Link>
                  <Link href="/settings" class="user-menu__item" @click="menuOpen = false">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    Настройки
                  </Link>
                  <div class="user-menu__sep"></div>
                  <button class="user-menu__item user-menu__item--danger" @click="logout">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Выйти
                  </button>
                </div>
              </div>
            </template>

            <template v-else>
              <a href="/auth/telegram" class="btn btn-tg btn-sm">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.04 9.607c-.148.658-.537.818-1.084.508l-3-2.21-1.447 1.393c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.48 14.607l-2.95-.924c-.641-.202-.654-.641.136-.951l11.52-4.442c.534-.194 1.001.13.376.958z"/></svg>
                Войти
              </a>
              <Link href="/login" class="btn btn-outline btn-sm">Вход</Link>
            </template>

            <button class="hamburger" @click="mobileOpen = !mobileOpen">
              <span></span><span></span><span></span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Mobile nav -->
    <div class="mobile-nav" :class="{ 'mobile-nav--open': mobileOpen }">
      <Link href="/" class="header__nav-link" @click="mobileOpen = false">Лента</Link>
      <Link href="/?category=proxy" class="header__nav-link" @click="mobileOpen = false">Прокси</Link>
      <Link href="/?category=vpn" class="header__nav-link" @click="mobileOpen = false">VPN</Link>
      <Link href="/?category=security" class="header__nav-link" @click="mobileOpen = false">Безопасность</Link>
      <Link href="/?category=tools" class="header__nav-link" @click="mobileOpen = false">Инструменты</Link>
      <hr style="border:none;border-top:1px solid var(--border);margin:8px 0;" />
      <template v-if="user">
        <Link :href="`/profile/${user.username}`" class="header__nav-link" @click="mobileOpen = false">Профиль</Link>
        <Link href="/articles/create" class="header__nav-link" @click="mobileOpen = false">Написать статью</Link>
        <Link href="/my/bookmarks" class="header__nav-link" @click="mobileOpen = false">Закладки</Link>
        <button class="header__nav-link" style="text-align:left;border:none;background:none;color:#dc3545;" @click="logout">Выйти</button>
      </template>
      <template v-else>
        <a href="/auth/telegram" class="header__nav-link">Войти через Telegram</a>
        <Link href="/login" class="header__nav-link" @click="mobileOpen = false">Вход по email</Link>
      </template>
    </div>

    <main>
      <div class="container">
        <slot />
      </div>
    </main>

    <!-- Back to top -->
    <Transition name="fade">
      <button v-if="showTop" class="back-to-top" @click="scrollTop" title="Наверх">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
      </button>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import Toast from '@/Components/Toast.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const menuOpen = ref(false)
const mobileOpen = ref(false)

const isHome = computed(() => page.url === '/' || (page.url.startsWith('/?') && !page.url.includes('category=')))
function isCategory(cat) {
  return page.url.includes(`category=${cat}`)
}

function closeMenu() { menuOpen.value = false }

function logout() {
  menuOpen.value = false
  mobileOpen.value = false
  router.post('/logout')
}

// Back to top
const showTop = ref(false)
function onScroll() { showTop.value = window.scrollY > 400 }
function scrollTop() { window.scrollTo({ top: 0, behavior: 'smooth' }) }
onMounted(() => window.addEventListener('scroll', onScroll))
onUnmounted(() => window.removeEventListener('scroll', onScroll))

// Click outside directive
const vClickOutside = {
  mounted(el, binding) {
    el._clickOutside = (e) => {
      if (!el.contains(e.target)) binding.value()
    }
    document.addEventListener('click', el._clickOutside, true)
  },
  unmounted(el) {
    document.removeEventListener('click', el._clickOutside, true)
  },
}
</script>
