FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx-php:5
MAINTAINER sharkseven 'shark@dodora.cn'



ADD upload /root/temp
EXPOSE 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
