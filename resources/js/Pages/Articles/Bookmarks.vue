<template>
  <AppLayout>
    <div class="page-wrap page-wrap--full" style="padding-top:20px;">
      <div class="content-card">
        <div class="tab-bar">
          <span class="tab-btn tab-btn--active">
            Закладки
            <span v-if="articles.total" style="margin-left:5px;font-size:12px;color:var(--text-muted);font-weight:400;">{{ articles.total }}</span>
          </span>
        </div>

        <div v-if="articles.data.length">
          <ArticleCard
            v-for="a in articles.data"
            :key="a.id"
            :article="a"
            :isBookmarked="true"
          />
        </div>
        <div v-else class="empty">
          <div class="empty__icon">🔖</div>
          <div class="empty__title">Закладок пока нет</div>
          <div class="empty__text">Нажмите «Сохранить» на любой статье — она появится здесь</div>
          <div style="margin-top:16px;">
            <Link href="/" class="btn btn-primary">Перейти в ленту</Link>
          </div>
        </div>

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
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ArticleCard from '@/Components/ArticleCard.vue'

defineProps({ articles: Object })
</script>
