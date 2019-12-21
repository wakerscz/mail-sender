FROM php:7.4-rc-cli
WORKDIR /in-docker

RUN apt-get update && \
    apt-get install -y git zip unzip

RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer global require hirak/prestissimo --no-plugins --no-scripts

COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install --prefer-dist --no-scripts --no-dev  --no-autoloader && \
    rm -rf /root/.composer

RUN composer dump-autoload --no-scripts --no-dev --optimize
#RUN --mount=target=/in-docker/vendor,type=bind,source=vendor \
#COPY --from=builder /in-docker/vendor /vendor

COPY ./campaign /in-docker/campaign
COPY ./sender /in-docker/sender
CMD ["php", "/in-docker/sender/index.php"]