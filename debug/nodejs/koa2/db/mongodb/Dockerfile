FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nodejs-mongodb

MAINTAINER sharkseven 'shark@dodora.cn'

ADD etc /etc
RUN rm -rf /root/temp
RUN git clone https://github.com/Gospely/koa2-template.git /root/temp
RUN bash -c ". /root/.nvm/nvm.sh && cd /root/temp && rm -rf .git && nvm use v6.9.1  && npm install"

EXPOSE 3000 80 27017 28017

ENTRYPOINT service ssh start && sh /run.sh && /config/main && /bin/bash
