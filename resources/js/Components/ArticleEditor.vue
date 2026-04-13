<template>
  <div class="ae">

    <!-- ── Toolbar ── -->
    <div v-if="editor" class="ae__toolbar">
      <div class="ae__toolbar-left">

        <button type="button" class="ae-btn" :class="{ active: editor.isActive('heading', { level: 1 }) }"
          @mousedown.prevent="editor.chain().focus().toggleHeading({ level: 1 }).run()" title="H1">H1</button>
        <button type="button" class="ae-btn" :class="{ active: editor.isActive('heading', { level: 2 }) }"
          @mousedown.prevent="editor.chain().focus().toggleHeading({ level: 2 }).run()" title="H2">H2</button>
        <button type="button" class="ae-btn" :class="{ active: editor.isActive('heading', { level: 3 }) }"
          @mousedown.prevent="editor.chain().focus().toggleHeading({ level: 3 }).run()" title="H3">H3</button>

        <div class="ae-sep"></div>

        <button type="button" class="ae-btn ae-btn--b" :class="{ active: editor.isActive('bold') }"
          @mousedown.prevent="editor.chain().focus().toggleBold().run()" title="Жирный"><b>B</b></button>
        <button type="button" class="ae-btn ae-btn--i" :class="{ active: editor.isActive('italic') }"
          @mousedown.prevent="editor.chain().focus().toggleItalic().run()" title="Курсив"><i>I</i></button>
        <button type="button" class="ae-btn ae-btn--s" :class="{ active: editor.isActive('strike') }"
          @mousedown.prevent="editor.chain().focus().toggleStrike().run()" title="Зачёркнутый"><s>S</s></button>
        <button type="button" class="ae-btn" :class="{ active: editor.isActive('code') }"
          @mousedown.prevent="editor.chain().focus().toggleCode().run()" title="Код">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
        </button>

        <div class="ae-sep"></div>

        <button type="button" class="ae-btn" :class="{ active: editor.isActive('blockquote') }"
          @mousedown.prevent="editor.chain().focus().toggleBlockquote().run()" title="Цитата">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/></svg>
        </button>
        <button type="button" class="ae-btn" :class="{ active: editor.isActive('codeBlock') }"
          @mousedown.prevent="editor.chain().focus().toggleCodeBlock().run()" title="Блок кода">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="18" rx="2"/><polyline points="8 10 5 13 8 16"/><polyline points="16 10 19 13 16 16"/></svg>
        </button>

        <div class="ae-sep"></div>

        <button type="button" class="ae-btn" :class="{ active: editor.isActive('bulletList') }"
          @mousedown.prevent="editor.chain().focus().toggleBulletList().run()" title="Список">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/>
            <circle cx="3.5" cy="6" r="1.5" fill="currentColor" stroke="none"/>
            <circle cx="3.5" cy="12" r="1.5" fill="currentColor" stroke="none"/>
            <circle cx="3.5" cy="18" r="1.5" fill="currentColor" stroke="none"/>
          </svg>
        </button>
        <button type="button" class="ae-btn" :class="{ active: editor.isActive('orderedList') }"
          @mousedown.prevent="editor.chain().focus().toggleOrderedList().run()" title="Нумерованный список">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
            <line x1="10" y1="6" x2="20" y2="6"/><line x1="10" y1="12" x2="20" y2="12"/><line x1="10" y1="18" x2="20" y2="18"/>
            <text x="1" y="8.5" font-size="7" fill="currentColor" stroke="none" font-family="sans-serif">1.</text>
            <text x="1" y="14.5" font-size="7" fill="currentColor" stroke="none" font-family="sans-serif">2.</text>
            <text x="1" y="20.5" font-size="7" fill="currentColor" stroke="none" font-family="sans-serif">3.</text>
          </svg>
        </button>
        <button type="button" class="ae-btn"
          @mousedown.prevent="editor.chain().focus().setHorizontalRule().run()" title="Разделитель">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="3" y1="12" x2="21" y2="12"/></svg>
        </button>

        <div class="ae-sep ae-sep--mobile-hide"></div>

        <button type="button" class="ae-btn ae-sep--mobile-hide" :disabled="!editor.can().undo()"
          @mousedown.prevent="editor.chain().focus().undo().run()" title="Отменить">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 14 4 9 9 4"/><path d="M20 20v-7a4 4 0 0 0-4-4H4"/></svg>
        </button>
        <button type="button" class="ae-btn ae-sep--mobile-hide" :disabled="!editor.can().redo()"
          @mousedown.prevent="editor.chain().focus().redo().run()" title="Повторить">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 14 20 9 15 4"/><path d="M4 20v-7a4 4 0 0 1 4-4h12"/></svg>
        </button>

      </div>
    </div>

    <!-- ── Cover preview ── -->
    <div v-if="coverUrl" class="ae__cover">
      <img
        :src="coverUrl"
        alt="Обложка"
        @load="coverLoaded = true"
        @error="coverLoaded = false"
        :style="{ opacity: coverLoaded ? 1 : 0 }"
      />
      <button type="button" class="ae__cover-rm" @click="$emit('update:coverUrl', '')" title="Убрать">×</button>
    </div>

    <!-- ── Title ── -->
    <div class="ae__fields">
      <textarea
        ref="titleEl"
        :value="title"
        class="ae__title"
        placeholder="Заголовок..."
        maxlength="255"
        rows="1"
        @input="onTitle"
        @keydown.enter.prevent="$refs.excerptEl.focus()"
      ></textarea>
      <div v-if="errors.title" class="ae__err">{{ errors.title }}</div>

      <textarea
        ref="excerptEl"
        :value="excerpt"
        class="ae__excerpt"
        placeholder="Краткое описание — покажется в ленте..."
        maxlength="500"
        rows="1"
        @input="onExcerpt"
      ></textarea>
      <div v-if="errors.excerpt" class="ae__err">{{ errors.excerpt }}</div>
    </div>

    <!-- ── Divider ── -->
    <div class="ae__divider"></div>

    <!-- ── Editor content ── -->
    <div class="ae__content">
      <EditorContent :editor="editor" />
      <div v-if="errors.body" class="ae__err" style="margin-top:6px;">{{ errors.body }}</div>
    </div>

    <!-- ── Tags ── -->
    <div class="ae__tags">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:var(--text-muted)"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
      <TagInput v-model="localTags" :compact="true" />
    </div>

  </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import TagInput from '@/Components/TagInput.vue'

const props = defineProps({
  title:    { type: String, default: '' },
  excerpt:  { type: String, default: '' },
  body:     { type: String, default: '' },
  tags:     { type: Array,  default: () => [] },
  coverUrl: { type: String, default: '' },
  errors:   { type: Object, default: () => ({}) },
})

const emit = defineEmits(['update:title', 'update:excerpt', 'update:body', 'update:tags', 'update:coverUrl'])

const titleEl    = ref(null)
const excerptEl  = ref(null)
const coverLoaded = ref(false)

// Tags proxy
const localTags = ref(props.tags)
watch(localTags, v => emit('update:tags', v))
watch(() => props.tags, v => { localTags.value = v })

// Editor
const editor = useEditor({
  content: props.body,
  extensions: [StarterKit],
  editorProps: { attributes: { class: 'ae__area' } },
  onUpdate({ editor }) { emit('update:body', editor.getHTML()) },
})

watch(() => props.body, val => {
  if (editor.value && editor.value.getHTML() !== val) {
    editor.value.commands.setContent(val, false)
  }
})

watch(() => props.coverUrl, () => { coverLoaded.value = false })

onMounted(() => {
  nextTick(() => {
    if (titleEl.value)   resize(titleEl.value)
    if (excerptEl.value) resize(excerptEl.value)
    titleEl.value?.focus()
  })
})

onBeforeUnmount(() => editor.value?.destroy())

function resize(el) {
  el.style.height = 'auto'
  el.style.height = el.scrollHeight + 'px'
}

function onTitle(e) {
  resize(e.target)
  emit('update:title', e.target.value)
}

function onExcerpt(e) {
  resize(e.target)
  emit('update:excerpt', e.target.value)
}
</script>

<style scoped>
/* Card */
.ae {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 12px;
  overflow: hidden;
}

/* Toolbar */
.ae__toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  border-bottom: 1px solid var(--border);
  gap: 4px;
  flex-wrap: wrap;
}

.ae__toolbar-left {
  display: flex;
  align-items: center;
  gap: 2px;
  flex-wrap: wrap;
}

.ae-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 28px;
  border: none;
  background: transparent;
  border-radius: 5px;
  cursor: pointer;
  color: var(--text-muted);
  font-size: 0.8rem;
  transition: background 0.12s, color 0.12s;
  padding: 0;
  flex-shrink: 0;
}
.ae-btn:hover:not(:disabled) {
  background: var(--border);
  color: var(--text);
}
.ae-btn.active {
  background: var(--primary);
  color: #fff;
}
.ae-btn:disabled { opacity: 0.3; cursor: default; }
.ae-btn--b { font-weight: 700; }
.ae-btn--i { font-style: italic; }
.ae-btn--s { text-decoration: line-through; }

.ae-sep {
  width: 1px;
  height: 18px;
  background: var(--border);
  margin: 0 4px;
  flex-shrink: 0;
}

/* Cover */
.ae__cover {
  position: relative;
  line-height: 0;
}
.ae__cover img {
  width: 100%;
  max-height: 380px;
  object-fit: cover;
  display: block;
  transition: opacity 0.2s;
}
.ae__cover-rm {
  position: absolute;
  top: 10px;
  right: 10px;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: rgba(0,0,0,0.55);
  color: #fff;
  border: none;
  cursor: pointer;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
  transition: background 0.15s;
}
.ae__cover-rm:hover { background: rgba(0,0,0,0.8); }

/* Fields */
.ae__fields {
  padding: 24px 24px 0;
}

.ae__title {
  width: 100%;
  border: none;
  outline: none;
  background: transparent;
  font-size: 1.9rem;
  font-weight: 700;
  color: var(--text);
  line-height: 1.25;
  resize: none;
  overflow: hidden;
  padding: 0;
  margin-bottom: 12px;
  font-family: inherit;
  display: block;
}
.ae__title::placeholder { color: var(--text-muted); opacity: 0.45; }

.ae__excerpt {
  width: 100%;
  border: none;
  outline: none;
  background: transparent;
  font-size: 1rem;
  color: var(--text-muted);
  line-height: 1.6;
  resize: none;
  overflow: hidden;
  padding: 0;
  margin-bottom: 4px;
  font-family: inherit;
  display: block;
}
.ae__excerpt::placeholder { color: var(--text-muted); opacity: 0.45; }

.ae__err {
  font-size: 0.78rem;
  color: #dc3545;
  margin-bottom: 8px;
}

/* Divider */
.ae__divider {
  height: 1px;
  background: var(--border);
  margin: 16px 0 0;
}

/* Editor content */
.ae__content {
  padding: 20px 24px 8px;
}

:deep(.ae__area) {
  outline: none;
  min-height: 260px;
  font-size: 1rem;
  line-height: 1.75;
  color: var(--text);
}
:deep(.ae__area p) { margin: 0 0 0.75em; }
:deep(.ae__area p:last-child) { margin-bottom: 0; }
:deep(.ae__area h1) { font-size: 1.6rem; font-weight: 700; margin: 1em 0 0.4em; }
:deep(.ae__area h2) { font-size: 1.3rem; font-weight: 700; margin: 1em 0 0.4em; }
:deep(.ae__area h3) { font-size: 1.1rem; font-weight: 700; margin: 1em 0 0.4em; }
:deep(.ae__area ul), :deep(.ae__area ol) { padding-left: 1.5em; margin: 0 0 0.75em; }
:deep(.ae__area blockquote) {
  border-left: 3px solid var(--primary);
  margin: 0 0 0.75em;
  padding: 4px 0 4px 16px;
  color: var(--text-muted);
}
:deep(.ae__area code) {
  background: var(--bg);
  border-radius: 4px;
  padding: 2px 5px;
  font-size: 0.88em;
  font-family: 'JetBrains Mono', monospace;
}
:deep(.ae__area pre) {
  background: var(--bg);
  border-radius: 8px;
  padding: 14px 16px;
  overflow-x: auto;
  margin: 0 0 0.75em;
}
:deep(.ae__area pre code) { background: none; padding: 0; font-size: 0.875em; }
:deep(.ae__area hr) { border: none; border-top: 2px solid var(--border); margin: 1.5em 0; }

/* Tags row */
.ae__tags {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 24px 14px;
  border-top: 1px solid var(--border);
  margin-top: 12px;
}

/* Mobile */
@media (max-width: 600px) {
  .ae-sep--mobile-hide { display: none; }
  .ae__toolbar { padding: 6px 10px; }
  .ae-btn { width: 28px; height: 26px; }
  .ae__fields { padding: 18px 16px 0; }
  .ae__title { font-size: 1.5rem; }
  .ae__content { padding: 16px 16px 8px; }
  .ae__tags { padding: 10px 16px 14px; }
}
</style>
