FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nodejs

MAINTAINER sharkseven 'shark@dodora.cn'

RUN rm -rf /root/temp
RUN git clone https://github.com/Gospely/angular1-template.git /root/temp
RUN bash -c "cd /root/temp && rm -rf .git && npm install"

EXPOSE 8000 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
