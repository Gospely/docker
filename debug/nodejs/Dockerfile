FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx

MAINTAINER sharkseven 'shark@dodora.cn'

ADD etc /etc
ADD config /config

RUN git clone https://github.com/Gospely/hello_node.git /root/temp

EXPOSE 22


ENTRYPOINT service ssh start && /config/main && /bin/bash
