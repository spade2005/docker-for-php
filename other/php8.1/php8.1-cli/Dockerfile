FROM php:8.1.20-cli

ENV TZ=Asia/Shanghai

RUN apt-get update \
    && apt-get install -y --no-install-recommends libaio1 \
      libfreetype6-dev \
      libjpeg62-turbo-dev \
      libpng-dev \
      libzip-dev \
      libcurl4-openssl-dev \
      libsqlite3-dev \
      libpq-dev \
      libonig-dev \
      unzip  \
      libaio-dev  \
      librabbitmq-dev \
      libxml2-dev \
      supervisor \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd \
  && docker-php-ext-install -j$(nproc) pdo_mysql \
  && docker-php-ext-install -j$(nproc) mysqli \
  && docker-php-ext-install -j$(nproc) bcmath \
  && docker-php-ext-install -j$(nproc) xml \
  && docker-php-ext-install -j$(nproc) sockets \
  && docker-php-ext-install -j$(nproc) calendar \
  && docker-php-ext-install -j$(nproc) pcntl \
  && docker-php-ext-install -j$(nproc) gettext \
  && docker-php-ext-install -j$(nproc) opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 安装 Redis 扩展
COPY ./redis-5.3.7.tgz /tmp/redis-5.3.7.tgz
COPY ./zip-1.21.1.tgz /tmp/zip-1.21.1.tgz
COPY ./amqp-1.11.0.tgz /tmp/amqp-1.11.0.tgz

RUN cd /tmp \
  && pecl install redis-5.3.7.tgz \
  && pecl install zip-1.21.1.tgz \
  && pecl install amqp-1.11.0.tgz \
  && rm -rf redis-5.3.7.tgz \
  && rm -rf zip-1.21.1.tgz \
  && rm -rf amqp-1.11.0.tgz \
  && docker-php-ext-enable redis amqp zip

COPY instantclient-basiclite-linux.x64-19.19.0.0.0dbru.zip /tmp/instantclient-basiclite.zip
COPY instantclient-sdk-linux.x64-19.19.0.0.0dbru.zip /tmp/instantclient-sdk.zip
RUN  unzip /tmp/instantclient-basiclite.zip -d /usr/local/ \
    && unzip /tmp/instantclient-sdk.zip -d /usr/local/ \
    && mv /usr/local/instantclient_19_19 /usr/local/instantclient \
    && rm /tmp/instantclient-basiclite.zip \
    && rm /tmp/instantclient-sdk.zip 

ENV LD_LIBRARY_PATH /usr/local/instantclient
ENV ORACLE_HOME /usr/local/instantclient
ENV PATH $PATH:/usr/local/instantclient


RUN docker-php-ext-configure oci8 --with-oci8=instantclient,/usr/local/instantclient \
    && docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/usr/local/instantclient \
    && docker-php-ext-install oci8 pdo_oci
