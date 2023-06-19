FROM dockerproxy.com/library/php:8.1.20-cli

RUN sed -i 's/deb.debian.org/mirrors.aliyun.com/g' /etc/apt/sources.list \
&& sed -i 's/security.debian.org/mirrors.aliyun.com/g' /etc/apt/sources.list


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
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd \
  && docker-php-ext-install -j$(nproc) pdo_mysql \
  && docker-php-ext-install -j$(nproc) mysqli \
  && docker-php-ext-install -j$(nproc) bcmath \
  && docker-php-ext-install -j$(nproc) xml \
  && docker-php-ext-install -j$(nproc) opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 安装 Redis 扩展
RUN pecl install redis \
    && pecl install amqp \
    && docker-php-ext-enable redis zip amqp

# 安装 OCI8 和 PDO_OCI 扩展
# 请确保已将 Oracle Instant Client 安装包（以 .zip 格式）正确上传到适当的位置
COPY instantclient-basiclite-linux.x64-19.19.0.0.0dbru.zip /tmp/instantclient-basiclite.zip
COPY instantclient-sdk-linux.x64-19.19.0.0.0dbru.zip /tmp/instantclient-sdk.zip
RUN  unzip /tmp/instantclient-basiclite.zip -d /usr/local/ \
    && unzip /tmp/instantclient-sdk.zip -d /usr/local/ \
    && mv /usr/local/instantclient_19_19 /usr/local/instantclient \
    && rm /tmp/instantclient-basiclite.zip \
    && rm /tmp/instantclient-sdk.zip \
    && echo 'instantclient,/usr/local/instantclient' | pecl install oci8 \
    && echo 'oci8' >> /usr/local/etc/php/conf.d/docker-php-ext-oci8.ini \
    && docker-php-ext-configure pdo_oci --with-pdo-oci=instantclient,/usr/local/instantclient \
    && docker-php-ext-install pdo_oci 
