FROM dunglas/frankenphp:php8.4-bookworm

RUN docker-php-ext-install pdo_mysql mysqli

WORKDIR /app
COPY . /app
