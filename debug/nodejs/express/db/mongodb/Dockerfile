FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nodejs-mongodb

MAINTAINER sharkseven 'shark@dodora.cn'

RUN rm -rf /root/temp
RUN git clone https://github.com/Gospely/express-template.git /root/temp
RUN bash -c "cd /root/temp && rm -rf .git && npm install"

EXPOSE 3000 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
