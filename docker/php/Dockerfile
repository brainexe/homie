# todo alpine
FROM php:7.1-fpm

WORKDIR /www/homie
RUN apt-get update && apt-get install -y wget unzip libmcrypt-dev \
    && docker-php-ext-install -j$(nproc) mcrypt gettext

#RUN wget https://jenkins.mdoetsch.de/job/BuildHomie-Web/lastSuccessfulBuild/artifact/*zip*/archive.zip -O archive.zip
#RUN (rm -Rf web/* && unzip -q archive.zip && cp -Rf archive/* . && rm archive archive.zip -Rf && echo "updated /web/ directory")

RUN bash -c "wget http://getcomposer.org/composer.phar && mv composer.phar /usr/local/bin/composer"

ONBUILD RUN /usr/local/bin/composer install

EXPOSE 9000
