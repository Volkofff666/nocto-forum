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
  echo ">> .env создан. Заполни DB_PASSWORD и TELEGRAM_BOT_TOKEN, затем запусти скрипт снова."
  exit 0
fi

if grep -q "APP_KEY=$" .env; then
  KEY=$(openssl rand -base64 32)
  sed -i "s|APP_KEY=|APP_KEY=base64:$KEY|" .env
  echo ">> APP_KEY сгенерирован"
fi

docker compose up -d --build

echo ""
echo "=== Готово! Сайт: http://$(curl -s ifconfig.me) ==="
echo "Для SSL: bash ssl.sh your@email.com"
echo "Для заполнения БД тестовыми данными: docker compose exec app php artisan db:seed"
