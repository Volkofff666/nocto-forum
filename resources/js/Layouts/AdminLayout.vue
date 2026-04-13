<template>
  <div class="admin-shell">
    <Toast :message="$page.props.flash?.success" type="success" />
    <Toast :message="$page.props.flash?.error"   type="error" />

    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <div class="admin-sidebar__logo">
        <Link href="/">nocto<span>.</span>hub</Link>
        <span class="admin-sidebar__badge">admin</span>
      </div>

      <nav class="admin-nav">
        <Link href="/admin" class="admin-nav__item" :class="{ active: isActive('/admin', true) }">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
          Обзор
        </Link>
        <Link href="/admin/users" class="admin-nav__item" :class="{ active: isActive('/admin/users') }">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          Пользователи
        </Link>
        <Link href="/admin/articles" class="admin-nav__item" :class="{ active: isActive('/admin/articles') }">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          Статьи
        </Link>
        <Link href="/admin/comments" class="admin-nav__item" :class="{ active: isActive('/admin/comments') }">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          Комментарии
        </Link>
        <Link href="/admin/reports" class="admin-nav__item" :class="{ active: isActive('/admin/reports') }">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          Жалобы
          <span v-if="pendingReports > 0" class="admin-nav__badge">{{ pendingReports }}</span>
        </Link>
        <Link href="/admin/logs" class="admin-nav__item" :class="{ active: isActive('/admin/logs') }">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          Лог действий
        </Link>
      </nav>

      <div class="admin-sidebar__footer">
        <Link href="/" class="admin-nav__item">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          На сайт
        </Link>
      </div>
    </aside>

    <!-- Main -->
    <div class="admin-main">
      <header class="admin-header">
        <div class="admin-header__title">{{ title }}</div>
        <div class="admin-header__user">
          <div class="avatar avatar--32">
            <img v-if="user?.avatar_url" :src="user.avatar_url" :alt="user.name" />
            <template v-else>{{ user?.avatar }}</template>
          </div>
          <span>{{ user?.name }}</span>
        </div>
      </header>

      <div class="admin-content">
        <slot />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import Toast from '@/Components/Toast.vue'

defineProps({ title: { type: String, default: 'Админ-панель' } })

const page = usePage()
const user = computed(() => page.props.auth?.user)
const pendingReports = computed(() => page.props.pendingReports ?? 0)

function isActive(path, exact = false) {
  return exact ? page.url === path : page.url.startsWith(path)
}
</script>
