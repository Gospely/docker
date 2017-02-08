FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nodejs

MAINTAINER sharkseven 'shark@dodora.cn'

ADD etc /etc
RUN rm -rf /root/temp
RUN  echo 8192000 > /proc/sys/fs/inotify/max_user_watches
RUN bash -c ". /root/.nvm/nvm.sh && nvm use v6.9.1  && npm install -g cordova ionic && ionic start --v2 /root/temp blank -n"

EXPOSE 8100 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
