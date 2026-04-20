import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

export function useCategories() {
  const page = usePage()
  return computed(() => page.props.categories ?? [])
}

export function useCategoryLabel(value) {
  const page = usePage()
  const categories = page.props.categories ?? []
  const cat = categories.find(c => c.value === value)
  return cat ? cat.label : value
}

// Deprecated — use useCategories() instead.
// Kept as empty fallback so any direct import does not throw.
export const CATEGORIES = []
