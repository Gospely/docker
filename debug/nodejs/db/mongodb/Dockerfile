FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx-mongodb

MAINTAINER ivy 'xieyang@dodora.cn'

ADD config /config
ADD etc /etc

ADD etc /etc
ADD config /config
RUN bash -c '. /root/.nvm/nvm.sh && nvm install v6.9.1'
RUN bash -c '. /root/.nvm/nvm.sh && nvm install v6.2.0'
RUN bash -c '. /root/.nvm/nvm.sh && nvm install v5.12.0'
RUN bash -c '. /root/.nvm/nvm.sh && nvm install v4.4.4'

RUN git clone https://github.com/Gospely/hello_node.git /root/temp

EXPOSE 22 80 27017 28017

ENTRYPOINT service ssh start && sh /run.sh && /config/main && /bin/bash
