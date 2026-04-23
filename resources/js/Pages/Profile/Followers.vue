<template>
  <AppLayout>
    <div class="followers-page">

      <!-- Header -->
      <div class="followers-header">
        <Link :href="`/profile/${profileUser.username}`" class="followers-header__back">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          {{ profileUser.name }}
        </Link>
        <h1 class="followers-header__title">
          {{ tab === 'followers' ? 'Подписчики' : 'Подписки' }}
        </h1>
      </div>

      <!-- Tabs -->
      <div class="followers-tabs">
        <Link
          :href="`/profile/${profileUser.username}/followers`"
          class="followers-tab"
          :class="{ 'followers-tab--active': tab === 'followers' }"
        >
          Подписчики
          <span class="followers-tab__count">{{ followersCount }}</span>
        </Link>
        <Link
          :href="`/profile/${profileUser.username}/following`"
          class="followers-tab"
          :class="{ 'followers-tab--active': tab === 'following' }"
        >
          Подписки
          <span class="followers-tab__count">{{ followingCount }}</span>
        </Link>
      </div>

      <!-- List -->
      <div v-if="users.data.length" class="content-card followers-list">
        <div
          v-for="u in users.data"
          :key="u.id"
          class="follower-item"
        >
          <Link :href="`/profile/${u.username}`" class="follower-item__avatar">
            <div class="avatar avatar--48">
              <img v-if="u.avatar_url" :src="u.avatar_url" :alt="u.name" />
              <template v-else>{{ initials(u.name) }}</template>
            </div>
          </Link>

          <div class="follower-item__info">
            <Link :href="`/profile/${u.username}`" class="follower-item__name">{{ u.name }}</Link>
            <div class="follower-item__username">@{{ u.username }}</div>
            <div v-if="u.bio" class="follower-item__bio">{{ u.bio }}</div>
          </div>

          <FollowButton
            v-if="currentUser && currentUser.id !== u.id"
            :userId="u.id"
            :isFollowing="u.is_following"
          />
        </div>
      </div>

      <!-- Empty -->
      <div v-else class="content-card">
        <div class="empty">
          <div class="empty__icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.35">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          <div class="empty__title">
            {{ tab === 'followers' ? 'Нет подписчиков' : 'Нет подписок' }}
          </div>
          <div class="empty__text">
            {{ tab === 'followers'
              ? 'На этого пользователя пока никто не подписан'
              : 'Этот пользователь ни на кого не подписан' }}
          </div>
        </div>
      </div>

      <Pagination :paginator="users" />
    </div>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import FollowButton from '@/Components/FollowButton.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  profileUser:    Object,
  users:          Object,
  tab:            String,
  followersCount: Number,
  followingCount: Number,
})

const page        = usePage()
const currentUser = computed(() => page.props.auth?.user)

function initials(name) {
  return (name || '').trim().split(' ').slice(0, 2).map(w => w[0]?.toUpperCase() || '').join('')
}
</script>

<style scoped>
.followers-page {
  max-width: 640px;
  margin: 0 auto;
  padding: 24px 0 48px;
}

/* Header */
.followers-header {
  margin-bottom: 16px;
}

.followers-header__back {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 13px;
  color: var(--text-muted);
  margin-bottom: 8px;
  transition: color 0.15s;
}
.followers-header__back:hover { color: var(--accent); }

.followers-header__title {
  font-size: 20px;
  font-weight: 700;
  color: var(--text);
}

/* Tabs */
.followers-tabs {
  display: flex;
  gap: 4px;
  margin-bottom: 16px;
  border-bottom: 1px solid var(--border);
}

.followers-tab {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 10px 16px;
  font-size: 14px;
  font-weight: 500;
  color: var(--text-muted);
  border-bottom: 2px solid transparent;
  margin-bottom: -1px;
  transition: color 0.15s, border-color 0.15s;
  text-decoration: none;
}
.followers-tab:hover { color: var(--text); }
.followers-tab--active {
  color: var(--accent);
  border-bottom-color: var(--accent);
}

.followers-tab__count {
  font-size: 12px;
  font-weight: 600;
  background: var(--bg-secondary);
  padding: 1px 6px;
  border-radius: 10px;
  color: var(--text-muted);
}

/* List */
.followers-list {
  padding: 0;
  overflow: hidden;
}

.follower-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 14px 16px;
  border-bottom: 1px solid var(--border);
  transition: background 0.12s;
}
.follower-item:last-child { border-bottom: none; }
.follower-item:hover { background: var(--bg-hover); }

.follower-item__avatar { flex-shrink: 0; }

.follower-item__info {
  flex: 1;
  min-width: 0;
}

.follower-item__name {
  font-size: 14px;
  font-weight: 600;
  color: var(--text);
  display: block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.follower-item__name:hover { color: var(--accent); }

.follower-item__username {
  font-size: 12px;
  color: var(--text-muted);
  margin-top: 1px;
}

.follower-item__bio {
  font-size: 13px;
  color: var(--text-muted);
  margin-top: 3px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
