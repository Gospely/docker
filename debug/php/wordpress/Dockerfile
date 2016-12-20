FROM registry.cn-hangzhou.aliyuncs.com/sharkseven/gospel-nginx-php


MAINTAINER sharkseven 'shark@dodora.cn'

ENV WORDPRESS_VERSION 4.6.1
ENV WORDPRESS_SHA1 027e065d30a64720624a7404a1820e6c6fff1202

RUN git clone https://github.com/WordPress/WordPress.git /root/temp

EXPOSE 22
EXPOSE 80


ENTRYPOINT service ssh start && test -d /root/temp && mv /root/temp/* /root/workspace && rm -rf /root/temp || echo "文件夹不存在" && /config/main && /bin/bash
