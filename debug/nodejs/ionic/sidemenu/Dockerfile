FROM registry.cn-hangzhou.aliyuncs.com/gospel/ionic:blank

MAINTAINER sharkseven 'shark@dodora.cn'

ADD etc /etc
RUN rm -rf /root/temp

RUN bash -c ". /root/.nvm/nvm.sh && nvm use v6.9.1 && ionic start --v2 /root/temp sidemenu -n"

EXPOSE 8100 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
