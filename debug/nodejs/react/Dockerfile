FROM registry.cn-hangzhou.aliyuncs.com/gospel/debug-nodejs

MAINTAINER sharkseven 'shark@dodora.cn'

RUN rm -rf /root/temp
RUN git clone https://github.com/Gospely/react-template.git /root/temp
RUN bash -c "cd /root/temp && rm -rf .git"

EXPOSE 8080 80

ENTRYPOINT service ssh start && /config/main && /bin/bash
