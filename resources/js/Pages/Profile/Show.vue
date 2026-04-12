<template>
  <AppLayout>
    <div class="page-layout page-layout--full" style="max-width:760px;margin:0 auto;padding-top:32px;">
      <!-- Шапка профиля -->
      <div class="profile-header">
        <div class="avatar avatar--lg">
          <img v-if="profileUser.avatar_url" :src="profileUser.avatar_url" :alt="profileUser.name" />
          <template v-else>{{ profileUser.avatar }}</template>
        </div>
        <div class="profile-header__info">
          <div class="profile-header__name">{{ profileUser.name }}</div>
          <div class="profile-header__username">@{{ profileUser.username }}</div>
          <div v-if="profileUser.bio" class="profile-header__bio">{{ profileUser.bio }}</div>
          <div class="profile-stats">
            <div class="profile-stat">
              <div class="profile-stat__value">{{ articles.total }}</div>
              <div class="profile-stat__label">статей</div>
            </div>
            <div class="profile-stat">
              <div class="profile-stat__value">{{ totalVotes }}</div>
              <div class="profile-stat__label">голосов</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Статьи пользователя -->
      <div v-if="articles.data.length > 0">
        <ArticleCard
          v-for="article in articles.data"
          :key="article.id"
          :article="article"
        />

        <!-- Пагинация -->
        <div v-if="articles.last_page > 1" class="pagination">
          <button
            v-for="link in articles.links"
            :key="link.label"
            class="pagination__item"
            :class="{
              'pagination__item--active': link.active,
              'pagination__item--disabled': !link.url
            }"
            :disabled="!link.url"
            @click="link.url && router.get(link.url)"
            v-html="link.label"
          />
        </div>
      </div>
      <div v-else class="empty-state">
        <div class="empty-state__title">Публикаций пока нет</div>
        <div class="empty-state__text">{{ profileUser.name }} ещё ничего не опубликовал</div>
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
