FROM quay.io/nadyita/alpine:3.18

LABEL maintainer="nadyita@hodorraid.org" \
      description="self-sustaining docker image to run my test-app" \
      org.opencontainers.image.source="https://github.com/Nadyita/testapp"

ENTRYPOINT ["/usr/bin/php81"]

CMD ["/app/main.php"]

RUN apk --no-cache add \
    php81-cli \
    php81-sqlite3 \
    php81-phar \
    php81-curl \
    php81-sockets \
    php81-pdo \
    php81-pdo_sqlite \
    php81-pdo_mysql \
    php81-mbstring \
    php81-ctype \
    php81-bcmath \
    php81-json \
    php81-posix \
    php81-simplexml \
    php81-dom \
    php81-gmp \
    php81-pcntl \
    php81-zip \
    php81-opcache \
    php81-fileinfo \
    php81-tokenizer

COPY . /app
WORKDIR /app
RUN wget -O /usr/bin/composer https://getcomposer.org/composer-2.phar && \
    php81 /usr/bin/composer install --no-dev --no-interaction --no-progress -q && \
    php81 /usr/bin/composer dumpautoload --no-dev --optimize --no-interaction 2>&1 | grep -v "/20[0-9]\{12\}_.*autoload" && \
    php81 /usr/bin/composer clear-cache -q && \
    rm -f /usr/bin/composer
