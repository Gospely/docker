FROM registry.cn-hangzhou.aliyuncs.com/sharkseven/gospel-nodejs

MAINTAINER sharkseven 'shark@dodora.cn'

EXPOSE 22
ENTRYPOINT service ssh start && cd /root/workspace && npm install && npm run dev && rm -rf .git && /bin/bash
