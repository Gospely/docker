FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nodejs-mariadb

MAINTAINER sharkseven 'shark@dodora.cn'
ADD etc /etc

RUN rm -rf /root/temp
RUN git clone https://github.com/Gospely/express-template.git /root/temp
RUN bash -c ". /root/.nvm/nvm.sh && cd /root/temp && rm -rf .git && nvm use v6.9.1  && npm install"

EXPOSE 3000 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
