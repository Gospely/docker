FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nodejs-mongodb
MAINTAINER sharkseven 'shark@dodora.cn'

RUN apt-get update
RUN apt-get install  imagemagick -y

RUN git clone https://github.com/NodeBB/NodeBB.git /root/temp

RUN bash -c 'cd /root/temp && npm install --production'

EXPOSE 4567

ENTRYPOINT service ssh start cd /root/workspace && ./nodebb start || bin/bash
