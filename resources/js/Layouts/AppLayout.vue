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
            <Link href="/tools" class="header__nav-link" :class="{ active: isTools }">Инструменты</Link>
          </nav>

          <div class="header__right">
            <!-- Search -->
            <div class="header-search" :class="{ 'header-search--open': searchOpen }">
              <button v-if="!searchOpen" class="header-search__icon" @click="openSearch" title="Поиск">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              </button>
              <div v-else class="header-search__form">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:var(--text-muted)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input
                  ref="searchEl"
                  v-model="searchQ"
                  type="search"
                  class="header-search__input"
                  placeholder="Поиск..."
                  @keydown.enter="doSearch"
                  @keydown.esc="closeSearch"
                />
                <button class="header-search__close" @click="closeSearch">✕</button>
              </div>
            </div>

            <template v-if="user">
              <Link href="/notifications" class="notif-bell" title="Уведомления">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                  <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <span v-if="unreadCount > 0" class="notif-bell__badge">{{ unreadCount > 99 ? '99+' : unreadCount }}</span>
              </Link>

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
                  <template v-if="user.role === 'admin' || user.role === 'moderator'">
                    <div class="user-menu__sep"></div>
                    <Link href="/admin" class="user-menu__item" @click="menuOpen = false">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                      Панель модерации
                    </Link>
                  </template>
                  <div class="user-menu__sep"></div>
                  <button class="user-menu__item" @click="toggleTheme(); menuOpen = false">
                    <svg v-if="isDark" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="5"/>
                      <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                      <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                      <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                      <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                    </svg>
                    <svg v-else width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                    {{ isDark ? 'Светлая тема' : 'Тёмная тема' }}
                  </button>
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
      <Link href="/tools" class="header__nav-link" @click="mobileOpen = false">Инструменты</Link>
      <hr style="border:none;border-top:1px solid var(--border);margin:8px 0;" />
      <template v-if="user">
        <Link :href="`/profile/${user.username}`" class="header__nav-link" @click="mobileOpen = false">Профиль</Link>
        <Link href="/articles/create" class="header__nav-link" @click="mobileOpen = false">Написать статью</Link>
        <Link href="/my/bookmarks" class="header__nav-link" @click="mobileOpen = false">Закладки</Link>
        <button class="header__nav-link" style="text-align:left;border:none;background:none;" @click="toggleTheme(); mobileOpen = false">{{ isDark ? 'Светлая тема' : 'Тёмная тема' }}</button>
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
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import Toast from '@/Components/Toast.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const unreadCount = computed(() => page.props.unreadNotificationsCount ?? 0)

const menuOpen   = ref(false)
const mobileOpen = ref(false)

// Theme
const isDark = ref(false)
function applyTheme(dark) {
  isDark.value = dark
  document.documentElement.setAttribute('data-theme', dark ? 'dark' : 'light')
}
function toggleTheme() {
  const next = !isDark.value
  applyTheme(next)
  try { localStorage.setItem('theme', next ? 'dark' : 'light') } catch {}
}

// Search
const searchOpen = ref(false)
const searchQ    = ref('')
const searchEl   = ref(null)

async function openSearch() {
  searchOpen.value = true
  await nextTick()
  searchEl.value?.focus()
}
function closeSearch() { searchOpen.value = false; searchQ.value = '' }
function doSearch() {
  const q = searchQ.value.trim()
  if (!q) return
  closeSearch()
  router.get('/search', { q })
}

const isHome  = computed(() => page.url === '/' || (page.url.startsWith('/?') && !page.url.includes('category=')))
const isTools = computed(() => page.url.startsWith('/tools'))

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
onMounted(() => {
  window.addEventListener('scroll', onScroll)
  // Restore saved theme (or detect system preference)
  try {
    const saved = localStorage.getItem('theme')
    if (saved) {
      applyTheme(saved === 'dark')
    } else {
      applyTheme(window.matchMedia('(prefers-color-scheme: dark)').matches)
    }
  } catch {
    applyTheme(false)
  }
})
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

<style scoped>
.notif-bell {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 8px;
  color: var(--text-muted);
  transition: background 0.15s, color 0.15s;
  text-decoration: none;
}
.notif-bell:hover {
  background: var(--bg-hover);
  color: var(--text);
}
.notif-bell__badge {
  position: absolute;
  top: 2px;
  right: 2px;
  min-width: 16px;
  height: 16px;
  padding: 0 4px;
  border-radius: 8px;
  background: #e53e3e;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  line-height: 16px;
  text-align: center;
}
.menu-badge {
  margin-left: auto;
  min-width: 18px;
  height: 18px;
  padding: 0 5px;
  border-radius: 9px;
  background: #e53e3e;
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  line-height: 18px;
  text-align: center;
}
</style>
