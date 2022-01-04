# DockerHubからimageをTag指定で取ってくる
FROM php:8.1.1-apache

# RUNコマンド：「Dockerfile → イメージ」になるときに実行されるコマンド ※1回だけ実行したい例えばインストール系で使える
# CMDコマンド：「イメージ → コンテナ」になるときに実行されるコマンド

# PHPのモジュールなどをインストール ※決まり文句
RUN apt-get update \
  && apt-get install -y zlib1g-dev \
  && apt-get install -y libpng-dev \
  && apt-get install -y libpq-dev \
  && apt-get install -y zip unzip \
  && apt-get -y install libzip-dev \
  && docker-php-ext-install pdo_mysql \
  && a2enmod rewrite \
  #xdebug install(ここ追記)
  && pecl install xdebug \
  && docker-php-ext-enable xdebug \
    # PHP の GD ライブラリ
  && apt-get install -y \
    # zlib1g-dev libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libxpm-dev \
  && docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
    --with-xpm \
  && docker-php-ext-install -j$(nproc) gd

# composerのインストール ※決まり文句
RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer \
  && chmod +x /usr/local/bin/composer

# composer周りの環境変数 ※決まり文句
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
ENV PATH $PATH:/composer/vendor/bin

# 自分が作成したphp.iniファイルを適用する
COPY ./php.ini /usr/local/etc/php/php.ini

# laravelのsrcが入る「var/www/html」を指定
WORKDIR /var/www/html


# タイムゾーン設定
ENV TZ=Asia/Tokyo