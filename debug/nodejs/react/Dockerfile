FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nodejs

MAINTAINER sharkseven 'shark@dodora.cn'

ADD etc /etc

RUN rm -rf /root/temp
RUN git clone https://github.com/Gospely/react-template.git /root/temp
RUN bash -c "cd /root/temp && rm -rf .git && nvm use v6.9.1  && npm install"

EXPOSE 8000 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
