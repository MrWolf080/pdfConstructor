1. Установить зависимости: docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php83-composer:latest composer install --ignore-platform-reqs
2. cp .env.example .env
3. ./vendor/bin/sail up -d
4. ./vendor/bin/sail artisan migrate
5. ./vendor/bin/sail artisan key:generate
6. ./vendor/bin/sail artisan npm install
7. ./vendor/bin/sail artisan npm run build
