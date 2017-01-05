FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nginx

MAINTAINER sharkseven 'shark@dodora.cn'

ADD etc /etc
ADD config /config
RUN bash -c '. /root/.nvm/nvm.sh && nvm install v6.9.1'
RUN bash -c '. /root/.nvm/nvm.sh && nvm install v6.2.0'
RUN bash -c '. /root/.nvm/nvm.sh && nvm install v5.12.0'
RUN bash -c '. /root/.nvm/nvm.sh && nvm install v4.4.4'

RUN git clone https://github.com/Gospely/hello_node.git /root/temp

EXPOSE 22


ENTRYPOINT service ssh start && /config/main && /bin/bash
