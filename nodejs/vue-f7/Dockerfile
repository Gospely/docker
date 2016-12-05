FROM registry.cn-hangzhou.aliyuncs.com/sharkseven/gospel-nodejs

MAINTAINER sharkseven 'shark@dodora.cn'

EXPOSE 22
ENTRYPOINT service ssh start && cd /root/workspace && rm -rf .git && /bin/bash
