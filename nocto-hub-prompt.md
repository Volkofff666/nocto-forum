# nocto.hub — Claude Code Prompt

Build an MVP of a media platform **nocto.hub** — community about proxies, VPN, digital privacy.

## Stack
- Laravel 11, PHP 8.3
- Laravel Breeze (Inertia + Vue 3) — remove Tailwind after scaffold
- Laravel Socialite (Telegram OAuth)
- PostgreSQL + Redis
- Custom CSS only — NO Tailwind, NO utility frameworks

---

## CSS Design System
File: `resources/css/app.css`

```css
:root {
  --accent: #007bff;
  --accent-dark: #0056b3;
  --bg: #ffffff;
  --bg-secondary: #f7f7f5;
  --border: rgba(0,0,0,0.1);
  --text: #1a1a1a;
  --text-muted: #6b6b6b;
  --radius: 8px;
  --radius-lg: 12px;
  --max-width: 1100px;
}
```

Buttons: `.btn-primary` (accent bg), `.btn-outline` (border only), `.btn-tg` (#229ED9).
Badges by category: `.badge-proxy` (green), `.badge-vpn` (blue), `.badge-security` (purple), `.badge-tools` (amber).
Style: flat, clean, no shadows, no gradients. vc.ru aesthetics.

---

## Database Migrations

```
users:    id, name, username(unique), email(unique,nullable),
          password(nullable), telegram_id(unique,nullable),
          bio, avatar_url, role(enum:user,moderator,admin default:user),
          email_verified_at, remember_token, timestamps

articles: id, user_id(fk), title, slug(unique), excerpt, body(text),
          category(enum:proxy,vpn,security,tools,other),
          status(enum:draft,published default:draft),
          votes_count(default:0), views_count(default:0), timestamps

comments: id, user_id(fk), article_id(fk), parent_id(nullable fk→comments),
          body, timestamps

votes:    id, user_id(fk), article_id(fk), type(enum:up,down), timestamps
          unique[user_id, article_id]
```

---

## Models

**User** — hasMany articles, comments, votes. `getAvatarAttribute()`: initials from name if no avatar_url.

**Article** — belongsTo user, hasMany comments, votes. Scopes: `published()`, `byCategory()`, `ordered('latest'|'popular')`. Auto-generate slug from title on `creating`.

**Comment** — belongsTo user, article, parent. hasMany replies.

**Vote** — belongsTo user, article.

---

## Controllers

### ArticleController (resource)
- `index`: paginate(10) published, filter `?category=`, `?sort=latest|popular|discussed`. Eager load user. Cache key `articles.{params}` TTL 60s.
- `show`: find by slug, increment views_count, load comments.user.
- `store`: auth required. Validate title/excerpt/body/category. Create as draft.
- `update`: authorize owner. Validate. Clear cache on publish.
- `destroy`: authorize owner. Delete. Clear cache.
- `publish`: `PATCH /articles/{article}/publish` → set status=published, clear cache.

### CommentController
- `store`: auth. Validate body(max:2000), parent_id(nullable,exists). Return `back()`.
- `destroy`: authorize owner or admin. Return `back()`.

### VoteController
- `store`: `POST /articles/{article}/vote` auth. Body: `{type: up|down}`.
  - Same type exists → delete (toggle off)
  - Different type exists → update
  - None → create
  - Recalculate `votes_count` on article. Return `back()`.

### SocialiteController
- `redirect`: `GET /auth/telegram`
- `callback`: `GET /auth/telegram/callback` — find-or-create user by telegram_id. If username taken append 4 random digits. Login, redirect `/`.

### ProfileController
- `show`: `GET /profile/{username}` — load user with paginated published articles.

---

## Routes (`web.php`)

```
GET    /                          → ArticleController@index
GET    /articles/create           → [auth] ArticleController@create
POST   /articles                  → ArticleController@store
GET    /articles/{slug}           → ArticleController@show
GET    /articles/{id}/edit        → [auth] ArticleController@edit
PATCH  /articles/{id}             → ArticleController@update
DELETE /articles/{id}             → ArticleController@destroy
PATCH  /articles/{id}/publish     → ArticleController@publish
POST   /articles/{id}/comments    → CommentController@store
DELETE /comments/{id}             → CommentController@destroy
POST   /articles/{id}/vote        → VoteController@store
GET    /profile/{username}        → ProfileController@show
GET    /auth/telegram             → SocialiteController@redirect
GET    /auth/telegram/callback    → SocialiteController@callback
+ Breeze auth routes
```

---

## Policies

- `ArticlePolicy`: update/delete → `user->id === article->user_id || admin`
- `CommentPolicy`: delete → owner || moderator || admin

---

## Pages (`resources/js/Pages/`)

### AppLayout.vue
Header: logo "nocto**.**hub" (dot in --accent), nav (Лента/Статьи/Инструменты).
Right: if auth → avatar+username dropdown (профиль, написать, выйти). If guest → "Войти через Telegram" + "Войти".
Max-width: var(--max-width), centered.

### Articles/Index.vue
Props: `articles` (paginated), `filters` (category, sort).
Tab bar: Новое/Популярное/Обсуждения → updates `?sort=` via `Inertia.get`.
Category filter pills. ArticleCard component. Pagination links.
Sidebar: TelegramBlock (9047 subs), WriteCtaBlock, CategoryList with counts.

### Articles/Show.vue
Props: `article` (with user, comments.user, replies).
Full body (newlines → paragraphs). VoteBar component. CommentSection (2 levels max, inline reply form, delete button for owner). RelatedArticles (3 same category).

### Articles/Create.vue + Edit.vue
Form: title input (large), excerpt textarea, body textarea (auto-resize via JS), category select.
Buttons: "Сохранить черновик" + "Опубликовать". Validation errors display.

### Profile/Show.vue
Avatar circle (initials), name, bio, stats (статьи/голоса/подписчики). Published articles list.

### Auth/Login.vue + Register.vue
Standard Breeze forms + "Войти через Telegram" button → `/auth/telegram`.

---

## Components

- `ArticleCard.vue` — avatar, author, category badge, time-ago, title, excerpt (truncated), vote count, comment count.
- `VoteBar.vue` — up/down buttons with active state, count, POST to `/articles/{id}/vote`.
- `CommentItem.vue` — recursive for nested replies.

---

## Seeders

`DatabaseSeeder`: 5 users (1 admin), 15 published articles (mixed categories), 20 comments, random votes.

---

## Deployment

### `Dockerfile`
```dockerfile
FROM php:8.3-fpm-alpine
RUN apk add --no-cache nginx nodejs npm postgresql-client
RUN docker-php-ext-install pdo pdo_pgsql opcache pcntl
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer
WORKDIR /var/www
COPY . .
RUN composer install --no-dev --optimize-autoloader
RUN npm ci && npm run build && rm -rf node_modules
RUN chown -R www-data:www-data storage bootstrap/cache
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
EXPOSE 9000
ENTRYPOINT ["/entrypoint.sh"]
```

### `docker/entrypoint.sh`
```sh
#!/bin/sh
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php-fpm -D
nginx -g "daemon off;"
```

### `docker-compose.yml`
```yaml
services:
  app:
    build: .
    restart: always
    env_file: .env
    environment:
      DB_HOST: postgres
      REDIS_HOST: redis
    depends_on:
      postgres:
        condition: service_healthy
      redis:
        condition: service_started

  postgres:
    image: postgres:16-alpine
    restart: always
    environment:
      POSTGRES_DB: ${DB_DATABASE}
      POSTGRES_USER: ${DB_USERNAME}
      POSTGRES_PASSWORD: ${DB_PASSWORD}
    volumes:
      - pgdata:/var/lib/postgresql/data
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U ${DB_USERNAME}"]
      interval: 5s
      retries: 5

  redis:
    image: redis:7-alpine
    restart: always
    volumes:
      - redisdata:/data

  nginx:
    image: nginx:alpine
    restart: always
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./docker/nginx.conf:/etc/nginx/conf.d/default.conf
      - ./public:/var/www/public
      - certbot_conf:/etc/letsencrypt
      - certbot_www:/var/www/certbot
    depends_on: [app]

  certbot:
    image: certbot/certbot
    volumes:
      - certbot_conf:/etc/letsencrypt
      - certbot_www:/var/www/certbot
    entrypoint: >
      sh -c "trap exit TERM; while :;
      do certbot renew --quiet; sleep 12h & wait; done"

volumes:
  pgdata:
  redisdata:
  certbot_conf:
  certbot_www:
```

### `docker/nginx.conf`
```nginx
server {
    listen 80;
    server_name nocto.online www.nocto.online;
    root /var/www/public;
    index index.php;

    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### `.env.example`
```
APP_NAME=NoctoHub
APP_ENV=production
APP_KEY=
APP_URL=https://nocto.online
APP_DEBUG=false

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=nocto
DB_USERNAME=nocto
DB_PASSWORD=

REDIS_HOST=redis
REDIS_PORT=6379

SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis

TELEGRAM_BOT_TOKEN=
TELEGRAM_REDIRECT_URI=https://nocto.online/auth/telegram/callback
```

### `setup.sh`
```bash
#!/bin/bash
set -e
echo "=== Nocto Hub Setup ==="

if ! command -v docker &> /dev/null; then
  echo "Installing Docker..."
  curl -fsSL https://get.docker.com | sh
  systemctl enable --now docker
fi

if [ ! -f .env ]; then
  cp .env.example .env
  echo ""
  echo ">> .env created. Fill in DB_PASSWORD and TELEGRAM_BOT_TOKEN, then run again."
  exit 0
fi

if grep -q "APP_KEY=$" .env; then
  KEY=$(openssl rand -base64 32)
  sed -i "s|APP_KEY=|APP_KEY=base64:$KEY|" .env
  echo ">> APP_KEY generated"
fi

docker compose up -d --build
echo ""
echo "=== Done! Site: http://$(curl -s ifconfig.me) ==="
echo "For SSL: bash ssl.sh your@email.com"
```

### `ssl.sh`
```bash
#!/bin/bash
set -e
EMAIL=$1
DOMAIN=nocto.online
if [ -z "$EMAIL" ]; then
  echo "Usage: bash ssl.sh your@email.com"
  exit 1
fi
docker compose exec certbot certbot certonly --webroot \
  -w /var/www/certbot -d $DOMAIN -d www.$DOMAIN \
  --email $EMAIL --agree-tos --non-interactive
docker compose exec nginx nginx -s reload
echo "=== SSL ready ==="
```

---

## README.md (in Russian)

```markdown
## Запуск на VPS (Ubuntu 22.04)

git clone https://github.com/you/nocto-hub /app
cd /app
bash setup.sh
# заполни .env: DB_PASSWORD и TELEGRAM_BOT_TOKEN
bash setup.sh
# SSL:
bash ssl.sh your@email.com

## Обновление
git pull && docker compose up -d --build

## Логи
docker compose logs -f app

## Telegram бот
1. @BotFather → /newbot → скопируй токен в .env
2. /setdomain → nocto.online

## Требования к VPS
- Ubuntu 22.04
- 2 GB RAM минимум
- 20 GB SSD
```

---

## Instructions for Claude Code

- Write all files completely, no TODOs
- All mock content (articles, comments, users) in Russian
- Article mock content topic: "Как выбрать прокси в 2026"
- Zero Tailwind classes anywhere — only custom CSS via variables
- All internal page links must work (relative hrefs)
- Mobile: sidebar hidden, single column, hamburger nav
