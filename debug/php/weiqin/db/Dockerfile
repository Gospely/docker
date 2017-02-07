FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx-php-mariadb

MAINTAINER sharkseven 'shark@dodora.cn'

RUN git clone https://git.oschina.net/we7coreteam/pros.git /root/temp
RUN bash -c "cd /root/temp && rm -rf .git"

EXPOSE 22
EXPOSE 80


ENTRYPOINT service ssh start && /config/main && /bin/bash
