<template>
  <div class="tag-input" @click="focusInput">
    <div class="tag-input__inner">
      <span v-for="tag in modelValue" :key="tag" class="tag-chip">
        {{ tag }}
        <button type="button" class="tag-chip__remove" @click.stop="remove(tag)">×</button>
      </span>
      <input
        v-if="modelValue.length < 7"
        ref="inputEl"
        v-model="current"
        type="text"
        class="tag-input__field"
        :placeholder="modelValue.length === 0 ? 'Добавить теги...' : ''"
        @keydown.enter.prevent="add"
        @keydown="onKeydown"
        @blur="addIfNotEmpty"
      />
    </div>
    <div class="tag-input__hint">Enter или пробел — добавить · Backspace — удалить · макс. 7</div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
})
const emit = defineEmits(['update:modelValue'])

const current = ref('')
const inputEl = ref(null)

function focusInput() {
  inputEl.value?.focus()
}

function add() {
  const tag = current.value.replace(/,/g, '').trim().toLowerCase()
  if (!tag || props.modelValue.includes(tag) || props.modelValue.length >= 7) {
    current.value = ''
    return
  }
  emit('update:modelValue', [...props.modelValue, tag])
  current.value = ''
}

function addIfNotEmpty() {
  if (current.value.trim()) add()
}

function remove(tag) {
  emit('update:modelValue', props.modelValue.filter(t => t !== tag))
}

function onKeydown(e) {
  if (e.key === ' ' || e.key === ',') {
    e.preventDefault()
    add()
    return
  }
  if (e.key === 'Backspace' && current.value === '' && props.modelValue.length) {
    emit('update:modelValue', props.modelValue.slice(0, -1))
  }
}
</script>
