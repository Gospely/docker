FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nodejs

MAINTAINER sharkseven 'shark@dodora.cn'

ADD etc /etc
RUN rm -rf /root/temp
RUN git clone https://github.com/nolimits4web/Framework7-Vue-Webpack-Template.git /root/temp
RUN bash -c ". /root/.nvm/nvm.sh && nvm alias default v6.9.1 && cd /root/temp && npm install"

EXPOSE 8080 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
