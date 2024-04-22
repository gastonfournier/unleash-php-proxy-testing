FROM php:8.2

RUN apt-get update && apt-get install -y unzip

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN /usr/bin/composer require unleash/client symfony/http-client nyholm/psr7 symfony/cache