FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx-php-mariadb:5


MAINTAINER sharkseven 'shark@dodora.cn'

ADD etc /etc
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

RUN composer create-project laravel/laravel --prefer-dist /root/temp
RUN bash -c "cd /root/temp && rm -rf .git"

EXPOSE 22
EXPOSE 80


ENTRYPOINT service ssh start && /config/main && /bin/bash
