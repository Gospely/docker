registry.cn-hangzhou.aliyuncs.com/dodora/debug-nginx-php
MAINTAINER sharkseven 'shark@dodora.cn'

RUN git clone https://github.com/Gospely/hello_php.git /root/temp

EXPOSE 22
EXPOSE 80


ENTRYPOINT service ssh start && /config/main && /bin/bash
