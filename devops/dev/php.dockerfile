FROM php:7.2-fpm
RUN apt-get update -y
RUN apt-get upgrade -y
RUN apt-get install -y git
RUN pecl install -o -f xdebug \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable xdebug
RUN curl https://getcomposer.org/download/1.9.1/composer.phar > /usr/local/bin/composer
RUN chmod 755 /usr/local/bin/composer
ENV XDEBUG_CONFIG="idekey=anything-works-here"
ENV PHP_IDE_CONFIG="serverName=Docker"
RUN useradd -m dev
WORKDIR /srv/scrum-master
