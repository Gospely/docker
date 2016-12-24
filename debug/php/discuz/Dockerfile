FROM registry.cn-hangzhou.aliyuncs.com/gospel/nginx-php
MAINTAINER sharkseven 'shark@dodora.cn'

ADD upload /root/temp
EXPOSE 22
EXPOSE 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
