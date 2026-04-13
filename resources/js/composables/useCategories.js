// FIXED: Single source of truth for categories — synced with backend enum values
// Old values (proxy, vpn, tools) removed. New values match DB enum after 2026-04-13 migration.
export const CATEGORIES = [
  { value: 'tech',     label: 'Технологии' },
  { value: 'security', label: 'Безопасность' },
  { value: 'guides',   label: 'Гайды' },
  { value: 'news',     label: 'Новости' },
  { value: 'other',    label: 'Другое' },
]

export function useCategoryLabel(value) {
  const cat = CATEGORIES.find(c => c.value === value)
  return cat ? cat.label : value
}
