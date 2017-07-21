FROM php:7.1-cli

WORKDIR /www/homie

RUN apt-get update && \
    apt-get install -y --no-install-recommends git zip

RUN curl --silent --show-error https://getcomposer.org/installer | php

RUN apt-get purge -y --auto-remove wget unzip

RUN ./composer.phar install
RUN ./console cc
