FROM php:8.2-fpm as production

RUN apt-get update; \
	apt-get upgrade -y; \
    apt-get install -y --no-install-recommends \
    acl \
    libfcgi-bin \
    gettext \
    git \
    vim \
	openssl \
;

ARG APCU_VERSION=5.1.21
RUN set -eux; \
	apt-get update; \
    apt-get install -y --no-install-recommends \
		$PHPIZE_DEPS \
		icu-devtools \
    	libpng-dev \
		libpq-dev \
		libcurl4-openssl-dev \
		libssl-dev \
		libxml2-dev \
		libxslt-dev \
		libzip-dev \
        libaio1 \
        libaio-dev \
		pkg-config \
		zlib1g-dev \
		unzip \
        rsync \
	; \
	\
	docker-php-ext-configure zip; \
	docker-php-ext-configure pcntl --enable-pcntl; \
	docker-php-ext-install -j$(nproc) \
		intl \
		mysqli  \
        pdo  \
        pdo_mysql  \
		zip \
    	gd \
		soap \
		pcntl \
	; \
	pecl install \
		apcu-${APCU_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
		apcu \
		opcache \
        gd \
	;
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --from=composer:2.4 /usr/bin/composer /usr/bin/composer

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/conf.d/php.prod.ini $PHP_INI_DIR/conf.d/php.ini
COPY docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf


WORKDIR /var/www/html

ARG APP_ENV=prod
ARG COMPOSER_ALLOW_SUPERUSER=1
# Install Dependencies with Composer
# Prevent the reinstallation of vendors at every change in the source code
COPY composer.json composer.lock symfony.lock ./

RUN set -eux; \
	composer install --no-scripts --no-progress --no-dev --no-interaction --prefer-dist --optimize-autoloader; \
	composer clear-cache

# Copy code files
COPY bin bin/
COPY config config/
COPY migrations migrations/
COPY public public/
COPY src src/
COPY .env ./

RUN composer dump-env $APP_ENV;
RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync
VOLUME /var/www/html/var

# Run healthcheck
COPY docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck
HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

# Copy entrypoint
COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

FROM production AS debug

# Install XDebug
RUN	set -eux; \
    pecl install xdebug; \
	pecl clear-cache; \
	docker-php-ext-enable xdebug;

COPY docker/php/conf.d/php.dev.ini $PHP_INI_DIR/conf.d/php.ini
