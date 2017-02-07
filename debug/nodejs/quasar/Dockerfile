FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nodejs

MAINTAINER sharkseven 'shark@dodora.cn'

ADD etc /etc
RUN rm -rf /root/temp

RUN bash -c ". /root/.nvm/nvm.sh && nvm use v6.9.1  && npm install -g quasar-cli && quasar init /root/temp && cd /root/temp && npm install"

EXPOSE 8080 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
