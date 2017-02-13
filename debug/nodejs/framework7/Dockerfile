FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nodejs

MAINTAINER sharkseven 'shark@dodora.cn'

ADD etc /etc
RUN rm -rf /root/temp
RUN git clone https://github.com/nolimits4web/Framework7.git /root/temp
RUN bash -c ". /root/.nvm/nvm.sh && npm install -g gulp && cd /root/temp && npm install"

EXPOSE 3000 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
