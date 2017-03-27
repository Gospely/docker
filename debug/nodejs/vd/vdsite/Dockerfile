FROM registry.cn-hangzhou.aliyuncs.com/dodora/debug-nginx

MAINTAINER sharkseven 'shark@dodora.cn'

RUN rm -rf /root/temp
RUN git clone https://github.com/Gospely/vdsite-seed /root/temp

RUN bash -c "cd /root/temp && git submodule init && git submodule update"

EXPOSE 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
