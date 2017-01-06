FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx-php-mariadb:5

MAINTAINER sharkseven 'shark@dodora.cn'

RUN git clone https://github.com/Gospely/hello_php.git /root/temp

EXPOSE 22
EXPOSE 80


ENTRYPOINT service ssh start && /config/main && /bin/bash
