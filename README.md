# nocto.hub

Медиаплатформа сообщества о прокси, VPN и цифровой приватности.

## Запуск на VPS (Ubuntu 22.04)

```bash
git clone https://github.com/you/nocto-hub /app
cd /app
bash setup.sh
# Заполни .env: DB_PASSWORD и TELEGRAM_BOT_TOKEN
bash setup.sh
```

### SSL

```bash
bash ssl.sh your@email.com
```

### Заполнить БД тестовыми данными

```bash
docker compose exec app php artisan db:seed
```

## Обновление

```bash
git pull && docker compose up -d --build
```

## Логи

```bash
docker compose logs -f app
```

## Telegram OAuth

1. `@BotFather` → `/newbot` → скопируй токен в `.env` → `TELEGRAM_BOT_TOKEN=`
2. `/setdomain` → `nocto.online`

## Требования к VPS

- Ubuntu 22.04
- 2 GB RAM минимум
- 20 GB SSD

## Стек

- Laravel 11, PHP 8.3
- Inertia.js + Vue 3
- PostgreSQL 16 + Redis 7
- Nginx + Certbot
