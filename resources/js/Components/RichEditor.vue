<template>
  <div class="rich-editor" :class="{ 'rich-editor--focused': focused }">

    <!-- Toolbar -->
    <div v-if="editor" class="rich-editor__toolbar">

      <!-- Text style -->
      <button type="button" class="tb-btn" :class="{ 'tb-btn--active': editor.isActive('bold') }"
        @mousedown.prevent="editor.chain().focus().toggleBold().run()" title="Жирный (Ctrl+B)">
        <strong>B</strong>
      </button>
      <button type="button" class="tb-btn tb-btn--italic" :class="{ 'tb-btn--active': editor.isActive('italic') }"
        @mousedown.prevent="editor.chain().focus().toggleItalic().run()" title="Курсив (Ctrl+I)">
        <em>I</em>
      </button>
      <button type="button" class="tb-btn tb-btn--strike" :class="{ 'tb-btn--active': editor.isActive('strike') }"
        @mousedown.prevent="editor.chain().focus().toggleStrike().run()" title="Зачёркнутый">
        <s>S</s>
      </button>

      <div class="tb-sep"></div>

      <!-- Headings -->
      <button type="button" class="tb-btn" :class="{ 'tb-btn--active': editor.isActive('heading', { level: 1 }) }"
        @mousedown.prevent="editor.chain().focus().toggleHeading({ level: 1 }).run()" title="Заголовок 1">
        H1
      </button>
      <button type="button" class="tb-btn" :class="{ 'tb-btn--active': editor.isActive('heading', { level: 2 }) }"
        @mousedown.prevent="editor.chain().focus().toggleHeading({ level: 2 }).run()" title="Заголовок 2">
        H2
      </button>
      <button type="button" class="tb-btn" :class="{ 'tb-btn--active': editor.isActive('heading', { level: 3 }) }"
        @mousedown.prevent="editor.chain().focus().toggleHeading({ level: 3 }).run()" title="Заголовок 3">
        H3
      </button>

      <div class="tb-sep"></div>

      <!-- Lists -->
      <button type="button" class="tb-btn" :class="{ 'tb-btn--active': editor.isActive('bulletList') }"
        @mousedown.prevent="editor.chain().focus().toggleBulletList().run()" title="Маркированный список">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/>
          <circle cx="3.5" cy="6" r="1.5" fill="currentColor" stroke="none"/>
          <circle cx="3.5" cy="12" r="1.5" fill="currentColor" stroke="none"/>
          <circle cx="3.5" cy="18" r="1.5" fill="currentColor" stroke="none"/>
        </svg>
      </button>
      <button type="button" class="tb-btn" :class="{ 'tb-btn--active': editor.isActive('orderedList') }"
        @mousedown.prevent="editor.chain().focus().toggleOrderedList().run()" title="Нумерованный список">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <line x1="10" y1="6" x2="20" y2="6"/><line x1="10" y1="12" x2="20" y2="12"/><line x1="10" y1="18" x2="20" y2="18"/>
          <text x="1" y="8.5" font-size="7" fill="currentColor" stroke="none" font-family="sans-serif">1.</text>
          <text x="1" y="14.5" font-size="7" fill="currentColor" stroke="none" font-family="sans-serif">2.</text>
          <text x="1" y="20.5" font-size="7" fill="currentColor" stroke="none" font-family="sans-serif">3.</text>
        </svg>
      </button>

      <div class="tb-sep"></div>

      <!-- Blockquote + code -->
      <button type="button" class="tb-btn" :class="{ 'tb-btn--active': editor.isActive('blockquote') }"
        @mousedown.prevent="editor.chain().focus().toggleBlockquote().run()" title="Цитата">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
          <path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/>
          <path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"/>
        </svg>
      </button>
      <button type="button" class="tb-btn" :class="{ 'tb-btn--active': editor.isActive('code') }"
        @mousedown.prevent="editor.chain().focus().toggleCode().run()" title="Встроенный код">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
          <polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>
        </svg>
      </button>
      <button type="button" class="tb-btn" :class="{ 'tb-btn--active': editor.isActive('codeBlock') }"
        @mousedown.prevent="editor.chain().focus().toggleCodeBlock().run()" title="Блок кода">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <rect x="2" y="3" width="20" height="18" rx="2"/>
          <polyline points="8 10 5 13 8 16" stroke-width="2"/><polyline points="16 10 19 13 16 16" stroke-width="2"/>
        </svg>
      </button>

      <div class="tb-sep"></div>

      <!-- HR -->
      <button type="button" class="tb-btn"
        @mousedown.prevent="editor.chain().focus().setHorizontalRule().run()" title="Разделитель">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <line x1="3" y1="12" x2="21" y2="12"/>
        </svg>
      </button>

      <div style="flex:1"></div>

      <!-- Undo / Redo -->
      <button type="button" class="tb-btn" :disabled="!editor.can().undo()"
        @mousedown.prevent="editor.chain().focus().undo().run()" title="Отменить (Ctrl+Z)">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="9 14 4 9 9 4"/><path d="M20 20v-7a4 4 0 0 0-4-4H4"/>
        </svg>
      </button>
      <button type="button" class="tb-btn" :disabled="!editor.can().redo()"
        @mousedown.prevent="editor.chain().focus().redo().run()" title="Повторить (Ctrl+Shift+Z)">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="15 14 20 9 15 4"/><path d="M4 20v-7a4 4 0 0 1 4-4h12"/>
        </svg>
      </button>
    </div>

    <EditorContent :editor="editor" class="rich-editor__content" />
  </div>
</template>

<script setup>
import { watch, ref, onBeforeUnmount } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'

const props = defineProps({
  modelValue: { type: String, default: '' },
})
const emit = defineEmits(['update:modelValue'])

const focused = ref(false)

const editor = useEditor({
  content: props.modelValue,
  extensions: [StarterKit],
  editorProps: {
    attributes: { class: 'rich-editor__area' },
  },
  onUpdate({ editor }) {
    emit('update:modelValue', editor.getHTML())
  },
  onFocus() { focused.value = true },
  onBlur()  { focused.value = false },
})

watch(() => props.modelValue, (val) => {
  if (editor.value && editor.value.getHTML() !== val) {
    editor.value.commands.setContent(val, false)
  }
})

onBeforeUnmount(() => editor.value?.destroy())
</script>
