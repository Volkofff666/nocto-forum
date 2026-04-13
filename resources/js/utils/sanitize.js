import DOMPurify from 'dompurify'

// FIXED: Centralized sanitize helper to prevent XSS via v-html
// Always use sanitize(html) instead of raw v-html on user-generated content
export function sanitize(html) {
  if (!html) return ''
  return DOMPurify.sanitize(html, {
    ALLOWED_TAGS: ['p', 'br', 'strong', 'em', 'u', 's', 'ul', 'ol', 'li',
                   'h1', 'h2', 'h3', 'h4', 'blockquote', 'code', 'pre',
                   'a', 'img', 'figure', 'figcaption', 'hr', 'span', 'div',
                   'mark'],
    ALLOWED_ATTR: ['href', 'src', 'alt', 'title', 'class', 'target', 'rel'],
  })
}
