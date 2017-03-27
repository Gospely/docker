FROM registry.cn-hangzhou.aliyuncs.com/dodora/ionic:blank

MAINTAINER sharkseven 'shark@dodora.cn'

RUN rm -rf /root/temp

RUN bash -c ". /root/.nvm/nvm.sh && nvm use v6.9.1 && ionic start --v2 /root/temp tabs -n"

EXPOSE 8100 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
