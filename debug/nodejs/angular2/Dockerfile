FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nodejs

MAINTAINER sharkseven 'shark@dodora.cn'
ADD etc /etc
RUN rm -rf /root/temp
RUN git clone https://github.com/Gospely/angular2-template.git /root/temp
RUN bash -c ". /root/.nvm/nvm.sh && cd /root/temp && rm -rf .git && nvm use v6.9.1 && npm install node-sass && npm install -g angular-cli && npm install"

EXPOSE 4200 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
