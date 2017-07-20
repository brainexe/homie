FROM php:7.1-fpm

WORKDIR /www/homie

RUN apt-get update && apt-get install -y wget unzip libmcrypt-dev \
    && docker-php-ext-install -j$(nproc) mcrypt gettext

RUN bash -c "wget http://getcomposer.org/composer.phar && mv composer.phar /usr/local/bin/composer"

RUN apt-get update && apt-get install -y wget unzip libmcrypt-dev \
    && docker-php-ext-install -j$(nproc) mcrypt gettext

RUN apt-get purge -y --auto-remove wget unzip

ONBUILD RUN /usr/local/bin/composer install

EXPOSE 9000
