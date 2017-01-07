FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx-php-mariadb:5


MAINTAINER sharkseven 'shark@dodora.cn'

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

RUN git clone https://github.com/Gospely/laravel-template.git /root/temp
RUN bash -c "cd /root/temp && rm -rf .git"

EXPOSE 22
EXPOSE 80


ENTRYPOINT service ssh start && /config/main && /bin/bash
