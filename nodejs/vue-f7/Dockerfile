FROM registry.cn-hangzhou.aliyuncs.com/sharkseven/gospel-nodejs

MAINTAINER sharkseven 'shark@dodora.cn'

RUN git clone https://github.com/Gospely/vue-f7.git /root/temp
RUN cd /root/temp
RUN npm install 

EXPOSE 22
ENTRYPOINT service ssh start && mv /root/temp/* /root/workspace && rm -rf /root/temp && cd /root/workspace && npm run dev && rm -rf .git && /bin/bash
