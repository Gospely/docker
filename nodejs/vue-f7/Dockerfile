FROM registry.cn-hangzhou.aliyuncs.com/sharkseven/gospel-nodejs

MAINTAINER sharkseven 'shark@dodora.cn'

RUN git clone https://github.com/Gospely/vue-f7.git /root/temp
RUN bash -c '.cd /root/temp && npm install -g cnpm --registry=https://registry.npm.taobao.org && cnpm install'

EXPOSE 22
ENTRYPOINT service ssh start && mv /root/temp/* /root/workspace && rm -rf /root/temp && cd /root/workspace && npm run dev && rm -rf .git && /bin/bash
