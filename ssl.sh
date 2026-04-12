#!/bin/bash
set -e
EMAIL=$1
DOMAIN=noctocode.online

if [ -z "$EMAIL" ]; then
  echo "Использование: bash ssl.sh your@email.com"
  exit 1
fi

docker compose exec certbot certbot certonly --webroot \
  -w /var/www/certbot -d $DOMAIN -d www.$DOMAIN \
  --email $EMAIL --agree-tos --non-interactive

# Обновляем nginx конфиг для HTTPS
cat > docker/nginx-ssl.conf << 'EOF'
server {
    listen 80;
    server_name noctocode.online www.noctocode.online;
    location /.well-known/acme-challenge/ {
        root /var/www/certbot;
    }
    location / {
        return 301 https://$host$request_uri;
    }
}

server {
    listen 443 ssl http2;
    server_name noctocode.online www.noctocode.online;
    root /var/www/public;
    index index.php;

    ssl_certificate /etc/letsencrypt/live/noctocode.online/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/noctocode.online/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff2?)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
}
EOF

cp docker/nginx-ssl.conf docker/nginx.conf
docker compose exec nginx nginx -s reload

echo "=== SSL настроен ==="
