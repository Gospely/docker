FROM registry.cn-hangzhou.aliyuncs.com/dodora/debug-nginx-php:mariadb5


MAINTAINER sharkseven 'shark@dodora.cn'

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer

RUN git clone https://github.com/Gospely/thinkphp-template.git /root/temp
RUN bash -c "cd /root/temp && rm -rf .git"

EXPOSE 22
EXPOSE 80


ENTRYPOINT service ssh start && /config/main && /bin/bash
