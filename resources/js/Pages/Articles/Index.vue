<template>
  <AppLayout>
    <div class="page-layout">
      <!-- Основной контент -->
      <div>
        <!-- Табы -->
        <div class="tabs">
          <button
            class="tab"
            :class="{ 'tab--active': currentSort === 'latest' }"
            @click="setSort('latest')"
          >Новое</button>
          <button
            class="tab"
            :class="{ 'tab--active': currentSort === 'popular' }"
            @click="setSort('popular')"
          >Популярное</button>
          <button
            class="tab"
            :class="{ 'tab--active': currentSort === 'discussed' }"
            @click="setSort('discussed')"
          >Обсуждения</button>
        </div>

        <!-- Фильтр по категориям -->
        <div class="filter-pills">
          <button
            class="filter-pill"
            :class="{ 'filter-pill--active': !currentCategory }"
            @click="setCategory(null)"
          >Все</button>
          <button
            v-for="cat in categories"
            :key="cat.value"
            class="filter-pill"
            :class="{ 'filter-pill--active': currentCategory === cat.value }"
            @click="setCategory(cat.value)"
          >{{ cat.label }}</button>
        </div>

        <!-- Список статей -->
        <div v-if="articles.data.length > 0">
          <ArticleCard
            v-for="article in articles.data"
            :key="article.id"
            :article="article"
          />
        </div>
        <div v-else class="empty-state">
          <div class="empty-state__title">Статей пока нет</div>
          <div class="empty-state__text">Будьте первым — напишите что-нибудь интересное</div>
        </div>

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
            @click="link.url && goToPage(link.url)"
            v-html="link.label"
          />
        </div>
      </div>

      <!-- Сайдбар -->
      <aside class="sidebar">
        <!-- Telegram блок -->
        <div class="sidebar-block tg-block">
          <div class="tg-block__subs">9 047</div>
          <div class="tg-block__label">подписчиков в Telegram</div>
          <a href="https://t.me/noctohub" target="_blank" rel="noopener" class="btn-tg" style="width:100%;justify-content:center;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.04 9.607c-.148.658-.537.818-1.084.508l-3-2.21-1.447 1.393c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.48 14.607l-2.95-.924c-.641-.202-.654-.641.136-.951l11.52-4.442c.534-.194 1.001.13.376.958z"/>
            </svg>
            Подписаться
          </a>
        </div>

        <!-- CTA написать -->
        <div class="sidebar-block" style="text-align:center;" v-if="$page.props.auth?.user">
          <p style="font-size:14px;color:var(--text-muted);margin-bottom:12px;">Поделитесь опытом с сообществом</p>
          <Link href="/articles/create" class="btn-primary" style="width:100%;justify-content:center;">Написать статью</Link>
        </div>

        <!-- Категории -->
        <div class="sidebar-block">
          <div class="sidebar-block__title">Категории</div>
          <ul class="category-list">
            <li v-for="cat in categories" :key="cat.value">
              <a :href="`/?category=${cat.value}`">
                <span>{{ cat.label }}</span>
                <span class="category-list__count">{{ categoryCounts[cat.value] || 0 }}</span>
              </a>
            </li>
          </ul>
        </div>
      </aside>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ArticleCard from '@/Components/ArticleCard.vue'

const props = defineProps({
  articles: Object,
  filters: Object,
  categoryCounts: Object,
})

const currentSort = ref(props.filters?.sort || 'latest')
const currentCategory = ref(props.filters?.category || null)

const categories = [
  { value: 'proxy',    label: 'Прокси' },
  { value: 'vpn',      label: 'VPN' },
  { value: 'security', label: 'Безопасность' },
  { value: 'tools',    label: 'Инструменты' },
  { value: 'other',    label: 'Другое' },
]

function setSort(sort) {
  currentSort.value = sort
  reload()
}

function setCategory(category) {
  currentCategory.value = category
  reload()
}

function reload() {
  const params = { sort: currentSort.value }
  if (currentCategory.value) params.category = currentCategory.value
  router.get('/', params, { preserveState: true, replace: true })
}

function goToPage(url) {
  router.get(url)
}
</script>
