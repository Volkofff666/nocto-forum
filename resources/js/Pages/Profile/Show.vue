<template>
  <AppLayout>
    <div class="page-wrap page-wrap--full" style="padding-top:20px;">
      <div class="content-card">
        <!-- Hero -->
        <div class="profile-hero">
          <div class="avatar avatar--80">
            <img v-if="profileUser.avatar_url" :src="profileUser.avatar_url" :alt="profileUser.name" />
            <template v-else>{{ profileUser.avatar }}</template>
          </div>
          <div class="profile-hero__info">
            <div class="profile-hero__name">{{ profileUser.name }}</div>
            <div class="profile-hero__username">@{{ profileUser.username }}</div>
            <div v-if="profileUser.bio" class="profile-hero__bio">{{ profileUser.bio }}</div>
            <div class="profile-stats">
              <div class="profile-stat">
                <div class="profile-stat__val">{{ articles.total }}</div>
                <div class="profile-stat__label">статей</div>
              </div>
              <div class="profile-stat">
                <div class="profile-stat__val">{{ totalVotes > 0 ? '+' : '' }}{{ totalVotes }}</div>
                <div class="profile-stat__label">голосов</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Articles -->
        <div v-if="articles.data.length">
          <ArticleCard v-for="a in articles.data" :key="a.id" :article="a" />
        </div>
        <div v-else class="empty">
          <div class="empty__title">Публикаций пока нет</div>
          <div class="empty__text">{{ profileUser.name }} ещё ничего не опубликовал</div>
        </div>

        <!-- Pagination -->
        <div v-if="articles.last_page > 1" class="pagination">
          <button
            v-for="link in articles.links" :key="link.label"
            class="page-btn"
            :class="{ 'page-btn--active': link.active }"
            :disabled="!link.url"
            @click="link.url && router.get(link.url)"
            v-html="link.label"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ArticleCard from '@/Components/ArticleCard.vue'

defineProps({
  profileUser: Object,
  articles:    Object,
  totalVotes:  Number,
})
</script>
