FROM registry.cn-hangzhou.aliyuncs.com/gospel/nginx

MAINTAINER sharkseven 'shark@dodora.cn'

RUN rm -rf /root/temp
RUN git clone https://github.com/Gospely/vdsite-seed /root/temp

EXPOSE 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
