FROM registry.cn-hangzhou.aliyuncs.com/sharkseven/gospel-nodejs

MAINTAINER sharkseven 'shark@dodora.cn'

RUN git clone https://github.com/Gospely/vue-f7.git /root/temp

EXPOSE 22
ENTRYPOINT service ssh start && cp -r /root/temp/ /root/workspace && rm -rf /root/temp && cd /root/workspace && npm install && npm run dev && rm -rf .git && /bin/bash
