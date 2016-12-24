FROM registry.cn-hangzhou.aliyuncs.com/gospel/nodejs
MAINTAINER sharkseven 'shark@dodora.cn'

RUN apt-get install sudo

RUN curl -sL https://deb.nodesource.com/setup_4.x | sudo -E bash -
RUN apt-get install -y git nodejs mongodb build-essential

RUN git clone https://github.com/NodeBB/NodeBB.git /root/temp

RUN cd /root/temp
RUN npm install --production
EXPOSE 4567

ENTRYPOINT cd /root/workspace && npm start || /bin/bash
