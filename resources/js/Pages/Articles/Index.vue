<template>
  <AppLayout>
    <div class="page-wrap">
      <!-- Feed -->
      <div>
        <div class="content-card">
          <!-- Tab bar -->
          <div class="tab-bar">
            <button class="tab-btn" :class="{ 'tab-btn--active': sort === 'latest' }"   @click="setSort('latest')">Новое</button>
            <button class="tab-btn" :class="{ 'tab-btn--active': sort === 'popular' }"  @click="setSort('popular')">Популярное</button>
            <button class="tab-btn" :class="{ 'tab-btn--active': sort === 'discussed' }" @click="setSort('discussed')">Обсуждаемое</button>
          </div>

          <!-- Tag filter notice -->
          <div v-if="tag" class="tag-filter-bar">
            <span>Тег: <strong>#{{ tag }}</strong></span>
            <button class="tag-filter-bar__clear" @click="clearTag">× Сбросить</button>
          </div>

          <!-- Category pills -->
          <div v-else class="cat-pills">
            <button class="cat-pill" :class="{ 'cat-pill--active': !category }" @click="setCategory(null)">Все</button>
            <button
              v-for="c in categories" :key="c.value"
              class="cat-pill"
              :class="{ 'cat-pill--active': category === c.value }"
              @click="setCategory(c.value)"
            >{{ c.label }}</button>
          </div>

          <!-- Articles -->
          <div v-if="articles.data.length">
            <ArticleCard
              v-for="a in articles.data"
              :key="a.id"
              :article="a"
              :isBookmarked="bookmarkedIds.includes(a.id)"
            />
          </div>
          <div v-else class="empty">
            <div class="empty__icon">📭</div>
            <div class="empty__title">Статей пока нет</div>
            <div class="empty__text">Станьте первым — напишите что-нибудь интересное</div>
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

      <!-- Sidebar -->
      <aside class="sidebar sidebar-sticky">
        <div v-if="$page.props.auth?.user" class="sidebar-card write-cta">
          <p>Поделитесь опытом с сообществом</p>
          <Link href="/articles/create" class="btn btn-primary" style="width:100%;">Написать статью</Link>
        </div>
        <div v-else class="sidebar-card write-cta">
          <p>Войдите, чтобы публиковать статьи и участвовать в обсуждениях</p>
          <a href="/auth/telegram" class="btn btn-tg" style="width:100%;justify-content:center;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.04 9.607c-.148.658-.537.818-1.084.508l-3-2.21-1.447 1.393c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.48 14.607l-2.95-.924c-.641-.202-.654-.641.136-.951l11.52-4.442c.534-.194 1.001.13.376.958z"/></svg>
            Войти через Telegram
          </a>
        </div>

        <div class="sidebar-card tg-widget">
          <div class="tg-widget__count">{{ fmtCount(tgSubscribers) }}</div>
          <div class="tg-widget__label">подписчиков в Telegram</div>
          <a href="https://t.me/noctoproxy" target="_blank" rel="noopener" class="btn btn-tg" style="width:100%;justify-content:center;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.562 8.248l-2.04 9.607c-.148.658-.537.818-1.084.508l-3-2.21-1.447 1.393c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.12L7.48 14.607l-2.95-.924c-.641-.202-.654-.641.136-.951l11.52-4.442c.534-.194 1.001.13.376.958z"/></svg>
            Подписаться
          </a>
        </div>

        <div class="sidebar-card">
          <div class="sidebar-card__title">Категории</div>
          <div class="cat-list">
            <a v-for="c in categories" :key="c.value" :href="`/?category=${c.value}`" class="cat-list__item">
              <span>{{ c.label }}</span>
              <span class="cat-list__count">{{ categoryCounts[c.value] || 0 }}</span>
            </a>
          </div>
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
  articles:      Object,
  filters:       Object,
  categoryCounts:Object,
  bookmarkedIds: { type: Array,   default: () => [] },
  tgSubscribers: { type: Number,  default: 0 },
})

function fmtCount(n) {
  if (!n) return '—'
  if (n >= 1000) return (n / 1000).toFixed(1).replace('.0', '') + ' K'
  return n.toString()
}

const sort     = ref(props.filters?.sort || 'latest')
const category = ref(props.filters?.category || null)
const tag      = ref(props.filters?.tag || null)

const categories = [
  { value: 'tech',     label: 'Технологии' },
  { value: 'security', label: 'Безопасность' },
  { value: 'guides',   label: 'Гайды' },
  { value: 'news',     label: 'Новости' },
  { value: 'other',    label: 'Другое' },
]

function setSort(s)     { sort.value = s; reload() }
function setCategory(c) { category.value = c; reload() }
function clearTag()     { tag.value = null; reload() }

function reload() {
  const params = { sort: sort.value }
  if (category.value) params.category = category.value
  if (tag.value)      params.tag = tag.value
  router.get('/', params, { preserveState: true, replace: true })
}
</script>
