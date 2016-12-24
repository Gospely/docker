FROM registry.cn-hangzhou.aliyuncs.com/gospel/nodejs
MAINTAINER sharkseven 'shark@dodora.cn'

RUN apt-get update
RUN apt-get install sudo -y

RUN curl -sL https://deb.nodesource.com/setup_4.x | sudo -E bash -
RUN apt-get install -y mongodb

RUN git clone https://github.com/NodeBB/NodeBB.git /root/temp

EXPOSE 4567

ENTRYPOINT service ssh start && /config/main && test -d /root/temp && (mv /root/temp/* /root/workspace && rm -rf /root/temp && cd /root/workspace && npm install --production && npm start && bin/bash) || (cd /root/workspace && npm install --production && npm start && bin/bash)
